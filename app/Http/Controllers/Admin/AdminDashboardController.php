<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttempt;
use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\RiskRadarService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected RiskRadarService $riskRadarService
    ) {}

    public function index(): View
    {
        $totalUsers = User::count();
        $traineesCount = User::where('role', 'trainee')->count();
        $trainersCount = User::where('role', 'trainer')->where('status', 'approved')->count(); // approved = active for trainers
        $pendingTrainersCount = User::where('role', 'trainer')->where('status', 'pending')->count();

        // For the inline dashboard approval card
        $pendingTrainersList = User::where('role', 'trainer')
            ->where('status', 'pending')
            ->with(['department', 'profile'])
            ->latest()
            ->take(5)
            ->get();

        $publishedCoursesCount = Course::where('status', 'published')->count();
        $pendingCoursesCount = Course::where('status', 'pending')->count();

        $totalEnrollments = Enrollment::count();
        $completedEnrollments = Enrollment::where('status', 'completed')->count();
        $overallCompletionRate = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0;

        $validCertificatesCount = Certificate::where('status', 'valid')->count();
        $expiringCertificatesCount = Certificate::where('status', 'expiring_soon')->count();
        $averageAssessmentScore = round((float) (AssessmentAttempt::avg('percentage') ?? 0), 1);
        $trainingDemand = Enrollment::whereIn('status', ['enrolled', 'in_progress'])->count();
        $topCourses = Course::withCount(['enrollments', 'certificates'])
            ->where('status', 'published')->orderByDesc('enrollments_count')->take(5)->get();
        $topTrainers = User::where('role', 'trainer')->withCount('authoredCourses')
            ->orderByDesc('authored_courses_count')->take(5)->get();

        $riskAnalysis = $this->riskRadarService->analyzeOrganizationalRisks();

        $recentAuditLogs = AuditLog::with('user')->latest()->take(6)->get();
        $pendingCourses = Course::where('status', 'pending')->with(['trainer', 'department'])->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'traineesCount',
            'trainersCount',
            'pendingTrainersCount',
            'publishedCoursesCount',
            'pendingCoursesCount',
            'totalEnrollments',
            'completedEnrollments',
            'overallCompletionRate',
            'validCertificatesCount',
            'expiringCertificatesCount',
            'averageAssessmentScore',
            'trainingDemand',
            'topCourses',
            'topTrainers',
            'riskAnalysis',
            'recentAuditLogs',
            'pendingCourses',
            'pendingTrainersList'
        ));
    }
}
