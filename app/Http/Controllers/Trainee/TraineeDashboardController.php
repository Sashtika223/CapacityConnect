<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\CustomNotification;
use App\Services\CompetencyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TraineeDashboardController extends Controller
{
    public function __construct(
        protected CompetencyService $competencyService
    ) {}

    public function index(): View
    {
        $user = Auth::user();

        $activeEnrollments = $user->enrollments()
            ->with(['course.trainer', 'course.modules.lessons', 'lastAccessedLesson'])
            ->whereIn('status', ['enrolled', 'in_progress'])
            ->latest('last_accessed_at')
            ->take(4)
            ->get();

        $completedEnrollments = $user->enrollments()
            ->with(['course.department', 'course.trainer'])
            ->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->get();
        $completedEnrollmentsCount = $completedEnrollments->count();
        $certificatesCount = $user->certificates()->where('status', 'valid')->count();

        $twinData = $this->competencyService->getDigitalTwinProfile($user);

        $recommendedCourses = Course::with(['trainer', 'department'])
            ->where('status', 'published')
            ->whereNotIn('id', $user->enrollments()->pluck('course_id'))
            ->take(3)
            ->get();

        $announcements = Announcement::whereIn('target_role', ['all', 'trainee'])->latest('published_at')->take(2)->get();
        $notifications = CustomNotification::where('user_id', $user->id)->latest()->take(5)->get();

        return view('trainee.dashboard', compact(
            'user',
            'activeEnrollments',
            'completedEnrollments',
            'completedEnrollmentsCount',
            'certificatesCount',
            'twinData',
            'recommendedCourses',
            'announcements',
            'notifications'
        ));
    }
}
