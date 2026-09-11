<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CustomNotification;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Carbon;

class CertificateService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Issues a certificate if the trainee has satisfied all completion criteria.
     */
    public function issueIfEligible(User $user, Course $course): ?Certificate
    {
        // Check existing certificate
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Verify enrollment completion
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (! $enrollment || $enrollment->progress_percentage < 100) {
            return null;
        }

        // Check if there are assessments and verify passing
        $assessments = $course->assessments()->where('is_active', true)->get();
        foreach ($assessments as $assessment) {
            $hasPassed = $user->assessmentAttempts()
                ->where('assessment_id', $assessment->id)
                ->where('passed', true)
                ->exists();

            if (! $hasPassed) {
                return null;
            }
        }

        // Every practical assessment must have a passing, trainer-reviewed submission.
        foreach ($course->practicalAssessments()->get() as $practical) {
            $hasPassed = $practical->submissions()
                ->where('user_id', $user->id)
                ->where('status', 'passed')
                ->where('score', '>=', $practical->passing_score)
                ->exists();

            if (! $hasPassed) {
                return null;
            }
        }

        // Create new Certificate (Trainees receive Lifetime Permanent certification, Trainers have 2-year validity)
        do {
            $certNumber = Certificate::generateNumber();
        } while (Certificate::where('certificate_number', $certNumber)->exists());

        do {
            $verificationCode = Certificate::generateVerificationCode();
        } while (Certificate::where('verification_code', $verificationCode)->exists());
        $expiryDate = $user->isTrainer() ? now()->addYears(2)->toDateString() : null;

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'certificate_number' => $certNumber,
            'verification_code' => $verificationCode,
            'issue_date' => now()->toDateString(),
            'expiry_date' => $expiryDate,
            'status' => 'valid',
        ]);

        // Create in-app notification
        $this->notificationService->send(
            $user,
            'Certificate Issued!',
            "Congratulations! You have successfully earned the certification for '{$course->title}'.",
            'success',
            route('trainee.certificates.show', $certificate->id),
            'certificate_updates'
        );

        AuditLogService::log('certificate.issued', $certificate, null, [
            'certificate_number' => $certNumber,
            'user' => $user->name,
            'course' => $course->title,
        ]);

        return $certificate;
    }

    /**
     * Check certificates for impending expiry and update statuses.
     */
    public function checkAndNotifyExpiries(): array
    {
        $expiringCount = 0;
        $expiredCount = 0;

        // Find certificates expiring in next 30 days
        $expiring = Certificate::with(['course.trainer', 'course.competencies'])
            ->where('status', 'valid')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->get();

        foreach ($expiring as $cert) {
            $cert->update(['status' => 'expiring_soon']);
            $daysLeft = (int) now()->diffInDays($cert->expiry_date, false);

            $refresher = Course::where('status', 'published')
                ->where('id', '!=', $cert->course_id)
                ->whereHas('competencies', function ($query) use ($cert) {
                    $query->whereIn('competencies.id', $cert->course->competencies->pluck('id'));
                })
                ->first();
            $actionUrl = $refresher
                ? route('courses.show', $refresher->slug)
                : route('trainee.certificates.show', $cert->id);
            $message = "Your {$cert->course->title} certification expires in {$daysLeft} days.";
            $recipients = collect([$cert->user_id, $cert->course->trainer_id])
                ->merge(User::where('role', 'admin')->pluck('id'))
                ->filter()
                ->unique();

            foreach ($recipients as $recipientId) {
                $recipient = User::find($recipientId);
                if ($recipient) {
                    $this->createDeadlineNotification($recipient, 'Certificate Expiring Soon', $message, 'warning', $actionUrl);
                }
            }
            $expiringCount++;
        }

        // Find already expired certificates
        $expired = Certificate::with(['course.trainer'])
            ->whereIn('status', ['valid', 'expiring_soon'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->toDateString())
            ->get();

        foreach ($expired as $cert) {
            $cert->update(['status' => 'expired']);

            $message = "Your {$cert->course->title} certification has expired. Re-enrollment or recertification is required.";
            $recipients = collect([$cert->user_id, $cert->course->trainer_id])
                ->merge(User::where('role', 'admin')->pluck('id'))
                ->filter()
                ->unique();

            foreach ($recipients as $recipientId) {
                $recipient = User::find($recipientId);
                if ($recipient) {
                    $this->createDeadlineNotification(
                        $recipient,
                        'Certificate Expired',
                        $message,
                        'alert',
                        route('courses.show', $cert->course->slug)
                    );
                }
            }
            $expiredCount++;
        }

        return ['expiring' => $expiringCount, 'expired' => $expiredCount];
    }

    private function createDeadlineNotification(User $recipient, string $title, string $message, string $type, string $actionUrl): void
    {
        $alreadyNotified = CustomNotification::where('user_id', $recipient->id)
            ->where('title', $title)
            ->where('message', $message)
            ->whereDate('created_at', Carbon::today())
            ->exists();

        if (! $alreadyNotified) {
            $this->notificationService->send($recipient, $title, $message, $type, $actionUrl, 'certificate_updates');
        }
    }
}
