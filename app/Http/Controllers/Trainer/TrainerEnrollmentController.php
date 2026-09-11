<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrainerEnrollmentController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Course $course): View
    {
        if ($course->trainer_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $enrollments = Enrollment::where('course_id', $course->id)
            ->with(['user.profile', 'user.assessmentAttempts'])
            ->latest('enrolled_at')
            ->paginate(20);

        return view('trainer.courses.enrollments', compact('course', 'enrollments'));
    }

    public function messageLearner(Request $request, Course $course, User $user): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $this->notificationService->send($user, "Message from Instructor {$course->trainer->name}", $request->input('message'), 'info', route('trainee.learning.player', $course->id), 'course_updates');

        AuditLogService::log('trainer.message_sent', $course, null, [
            'trainer' => Auth::user()->name,
            'recipient' => $user->name,
            'course' => $course->title,
        ]);

        return back()->with('success', "Message sent directly to {$user->name}!");
    }
}
