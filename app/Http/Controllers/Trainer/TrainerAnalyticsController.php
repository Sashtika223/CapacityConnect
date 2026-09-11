<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrainerAnalyticsController extends Controller
{
    public function index(): View
    {
        $trainer = Auth::user();
        $courses = Course::where('trainer_id', $trainer->id)->with(['enrollments', 'assessments'])->get();
        $courseIds = $courses->pluck('id');

        $enrollments = Enrollment::whereIn('course_id', $courseIds)->get();
        $completedCount = $enrollments->where('status', 'completed')->count();
        $inProgressCount = $enrollments->where('status', 'in_progress')->count();
        $enrolledCount = $enrollments->where('status', 'enrolled')->count();

        $assessmentAttempts = AssessmentAttempt::whereHas('assessment', function ($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->get();

        $avgScore = $assessmentAttempts->count() > 0 ? round($assessmentAttempts->avg('percentage'), 1) : 0;
        $passRate = $assessmentAttempts->count() > 0 ? round(($assessmentAttempts->where('passed', true)->count() / $assessmentAttempts->count()) * 100, 1) : 0;

        return view('trainer.analytics.index', compact(
            'courses',
            'completedCount',
            'inProgressCount',
            'enrolledCount',
            'avgScore',
            'passRate',
            'assessmentAttempts'
        ));
    }
}
