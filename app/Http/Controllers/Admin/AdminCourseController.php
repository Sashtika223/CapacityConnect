<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');
        $query = Course::with(['trainer.profile', 'department', 'modules.lessons', 'enrollments']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $courses = $query->latest()->paginate(12)->withQueryString();

        return view('admin.courses.index', compact('courses', 'status'));
    }

    public function show(Course $course): View
    {
        $course->load(['trainer.profile', 'department', 'modules.lessons.resources', 'modules.assessments.questions.options', 'practicalAssessments', 'competencies']);

        return view('admin.courses.show', compact('course'));
    }

    public function approve(Course $course): RedirectResponse
    {
        $course->update([
            'status' => 'published',
            'published_at' => now(),
            'rejection_reason' => null,
        ]);

        AuditLogService::log('course.approved', $course, null, ['status' => 'published']);
        $this->notificationService->send($course->trainer, 'Course Approved', "Your course '{$course->title}' is now published.", 'success', route('courses.show', $course->slug), 'course_updates');

        return back()->with('success', "Course '{$course->title}' has been approved and published to the public catalog!");
    }

    public function reject(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $course->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        AuditLogService::log('course.rejected', $course, null, ['reason' => $request->input('rejection_reason')]);
        $this->notificationService->send($course->trainer, 'Course Needs Changes', "Your course '{$course->title}' needs changes before publication.", 'warning', route('trainer.courses.edit', $course->id), 'course_updates');

        return back()->with('info', "Course '{$course->title}' was rejected with feedback provided to the trainer.");
    }
}
