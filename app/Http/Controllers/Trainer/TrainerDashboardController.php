<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\PracticalSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrainerDashboardController extends Controller
{
    public function index(): View
    {
        $trainer = Auth::user();

        $courses = Course::where('trainer_id', $trainer->id)->with(['department', 'enrollments'])->get();
        $courseIds = $courses->pluck('id');

        $totalLearners = Enrollment::whereIn('course_id', $courseIds)->distinct('user_id')->count('user_id');
        $activeEnrollments = Enrollment::whereIn('course_id', $courseIds)->whereIn('status', ['enrolled', 'in_progress'])->count();
        $completedEnrollments = Enrollment::whereIn('course_id', $courseIds)->where('status', 'completed')->count();

        $completionRate = ($activeEnrollments + $completedEnrollments) > 0
            ? round(($completedEnrollments / ($activeEnrollments + $completedEnrollments)) * 100, 1)
            : 0;

        $pendingSubmissions = PracticalSubmission::whereHas('practicalAssessment', function ($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->where('status', 'submitted')->with(['user', 'practicalAssessment.course'])->take(5)->get();

        $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->with(['user.profile', 'course'])
            ->latest('enrolled_at')
            ->take(5)
            ->get();

        $trainerCertificates = $trainer->certificates()->with('course')->get();

        return view('trainer.dashboard', compact(
            'trainer',
            'courses',
            'totalLearners',
            'activeEnrollments',
            'completedEnrollments',
            'completionRate',
            'pendingSubmissions',
            'recentEnrollments',
            'trainerCertificates'
        ));
    }
}
