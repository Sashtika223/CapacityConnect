<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\PracticalSubmission;
use App\Services\AuditLogService;
use App\Services\CertificateService;
use App\Services\CompetencyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrainerSubmissionController extends Controller
{
    public function __construct(
        protected CertificateService $certificateService,
        protected CompetencyService $competencyService
    ) {}

    public function index(): View
    {
        $trainerCourseIds = Auth::user()->authoredCourses()->pluck('id');

        $submissions = PracticalSubmission::whereHas('practicalAssessment', function ($q) use ($trainerCourseIds) {
            $q->whereIn('course_id', $trainerCourseIds);
        })->with(['user.profile', 'practicalAssessment.course'])->latest()->paginate(15);

        return view('trainer.submissions.index', compact('submissions'));
    }

    public function show(PracticalSubmission $submission): View
    {
        $submission->load(['user.profile', 'practicalAssessment.course']);

        if ($submission->practicalAssessment->course->trainer_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('trainer.submissions.show', compact('submission'));
    }

    public function grade(Request $request, PracticalSubmission $submission): RedirectResponse
    {
        if ($submission->practicalAssessment->course->trainer_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'score' => 'required|integer|min:0|max:'.$submission->practicalAssessment->max_score,
            'feedback' => 'required|string|min:10',
            'status' => 'required|in:passed,needs_revision,failed',
        ]);

        $submission->update([
            'score' => $request->input('score'),
            'feedback' => $request->input('feedback'),
            'status' => $request->input('status'),
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        AuditLogService::log('practical.graded', $submission, null, [
            'score' => $request->input('score'),
            'status' => $request->input('status'),
            'trainee' => $submission->user->name,
        ]);

        if ($submission->status === 'passed') {
            $practical = $submission->practicalAssessment;
            if ($practical->competency_id) {
                $this->competencyService->recordCompetencyAchievement(
                    $submission->user,
                    $practical->competency,
                    $practical->competency->required_level,
                    "Passed practical disaster evaluation: {$practical->title}",
                    Auth::user()
                );
            }

            $this->certificateService->issueIfEligible($submission->user, $practical->course);
        }

        return back()->with('success', 'Submission evaluated and graded successfully.');
    }
}
