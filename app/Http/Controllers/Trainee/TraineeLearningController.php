<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseFeedback;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Resource;
use App\Services\AuditLogService;
use App\Services\CertificateService;
use App\Services\CompetencyService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TraineeLearningController extends Controller
{
    public function __construct(
        protected CertificateService $certificateService,
        protected CompetencyService $competencyService,
        protected NotificationService $notificationService
    ) {}

    public function myCourses(Request $request): View
    {
        $status = $request->input('status', 'all');
        $query = Auth::user()->enrollments()->with(['course.trainer', 'course.modules.lessons']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $enrollments = $query->latest('last_accessed_at')->paginate(9);

        return view('trainee.learning.my-courses', compact('enrollments', 'status'));
    }

    public function enroll(Course $course): RedirectResponse
    {
        $user = Auth::user();

        if ($course->status !== 'published') {
            return back()->with('error', 'This course is not open for enrollment.');
        }

        $existing = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return redirect()->route('trainee.learning.player', ['course' => $course->id]);
        }

        // Find initial lesson
        $firstLesson = $course->modules()->orderBy('sort_order')->first()?->lessons()->orderBy('sort_order')->first();

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
            'progress_percentage' => 0,
            'last_accessed_lesson_id' => $firstLesson?->id,
            'enrolled_at' => now(),
            'last_accessed_at' => now(),
        ]);

        AuditLogService::log('course.enrolled', $enrollment);
        $this->notificationService->send($user, 'Enrollment Confirmed', "You are enrolled in '{$course->title}'.", 'success', route('trainee.learning.player', $course->id), 'course_updates');

        return redirect()->route('trainee.learning.player', ['course' => $course->id])
            ->with('success', 'You have enrolled successfully! Happy learning.');
    }

    public function player(Course $course, ?Lesson $lesson = null): View|RedirectResponse
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (! $enrollment) {
            return redirect()->route('courses.show', $course->slug)->with('info', 'Please enroll in this course to access lessons.');
        }

        $course->load(['modules.lessons.resources', 'modules.assessments', 'assessments', 'trainer.profile']);

        if (! $lesson) {
            $lesson = $enrollment->lastAccessedLesson
                ?? $course->modules->first()?->lessons->first();
        }

        if (! $course->lessons()->whereKey($lesson->id)->exists()) {
            abort(404);
        }

        if (! $lesson) {
            return back()->with('error', 'No lesson found in this course.');
        }

        // Update last accessed
        $enrollment->update([
            'last_accessed_lesson_id' => $lesson->id,
            'last_accessed_at' => now(),
        ]);

        // Fetch completed lesson IDs for this enrollment
        $completedLessonIds = LessonProgress::where('enrollment_id', $enrollment->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        return view('trainee.learning.player', compact('course', 'lesson', 'enrollment', 'completedLessonIds'));
    }

    public function completeLesson(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $user = Auth::user();
        if (! $course->lessons()->whereKey($lesson->id)->exists()) {
            abort(404);
        }
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        LessonProgress::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        $progressPercentage = $enrollment->recalculateProgress();

        // Check if eligible for certificate & update competency
        if ($progressPercentage >= 100) {
            $this->certificateService->issueIfEligible($user, $course);

            // Award competencies covered by course
            foreach ($course->competencies as $comp) {
                $targetLevel = $comp->pivot->target_level ?? 3;
                $this->competencyService->recordCompetencyAchievement(
                    $user,
                    $comp,
                    $targetLevel,
                    "Completed certified course: {$course->title}",
                    $course->trainer
                );
            }
        }

        // Find next lesson
        $nextLesson = Lesson::where('module_id', $lesson->module_id)
            ->where('sort_order', '>', $lesson->sort_order)
            ->orderBy('sort_order')
            ->first();

        if (! $nextLesson) {
            // Next module's first lesson
            $nextModule = $course->modules()->where('sort_order', '>', $lesson->module->sort_order)->orderBy('sort_order')->first();
            $nextLesson = $nextModule?->lessons()->orderBy('sort_order')->first();
        }

        if ($nextLesson) {
            return redirect()->route('trainee.learning.player', ['course' => $course->id, 'lesson' => $nextLesson->id])
                ->with('success', 'Lesson marked complete!');
        }

        return redirect()->route('trainee.learning.player', ['course' => $course->id, 'lesson' => $lesson->id])
            ->with('success', 'Course curriculum completed!');
    }

    public function downloadResource(Resource $resource): mixed
    {
        abort_unless(Auth::user()->can('download', $resource), 403);
        abort_unless(Storage::disk('local')->exists($resource->file_path), 404);

        return Storage::disk('local')->download($resource->file_path, basename($resource->title));
    }

    public function submitFeedback(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        CourseFeedback::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'course_id' => $course->id,
            ],
            [
                'rating' => $request->input('rating'),
                'comment' => $request->input('comment'),
            ]
        );

        AuditLogService::log('course.feedback_submitted', $course, null, [
            'rating' => $request->input('rating'),
            'user' => Auth::user()->name,
        ]);

        return back()->with('success', 'Thank you! Your course evaluation feedback has been submitted.');
    }
}
