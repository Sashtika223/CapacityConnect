<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\PracticalAssessment;
use App\Models\PracticalSubmission;
use App\Services\AuditLogService;
use App\Services\CertificateService;
use App\Services\CompetencyService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TraineeAssessmentController extends Controller
{
    public function __construct(
        protected CertificateService $certificateService,
        protected CompetencyService $competencyService,
        protected NotificationService $notificationService
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $enrolledCourseIds = $user->enrollments()->pluck('course_id');

        $assessments = Assessment::with(['course.trainer', 'questions'])
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('is_active', true)
            ->get();

        $attempts = $user->assessmentAttempts()->with('assessment.course')->latest()->paginate(10);
        $practicalSubmissions = $user->practicalSubmissions()->with('practicalAssessment.course')->latest()->get();

        return view('trainee.assessments.index', compact('assessments', 'attempts', 'practicalSubmissions'));
    }

    public function show(Assessment $assessment): View|RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user->can('attempt', $assessment), 403);

        // Check attempt count
        $attemptCount = $user->assessmentAttempts()->where('assessment_id', $assessment->id)->count();
        if ($attemptCount >= $assessment->max_attempts) {
            return back()->with('error', "Maximum attempt limit of {$assessment->max_attempts} reached for this assessment.");
        }

        $assessment->load(['course', 'questions.options']);
        $questions = $assessment->is_randomized ? $assessment->questions->shuffle() : $assessment->questions;

        return view('trainee.assessments.take', compact('assessment', 'questions', 'attemptCount'));
    }

    public function submit(Request $request, Assessment $assessment): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user->can('attempt', $assessment), 403);

        $attemptCount = $user->assessmentAttempts()->where('assessment_id', $assessment->id)->count();
        if ($attemptCount >= $assessment->max_attempts) {
            return redirect()->route('trainee.assessments.index')->with('error', 'Attempt limit reached.');
        }

        $submittedAnswers = $request->input('answers', []); // [question_id => option_id]
        $assessment->load('questions.options');

        $totalScore = 0.0;
        $totalPossible = 0.0;

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'attempt_number' => $attemptCount + 1,
            'started_at' => now()->subMinutes(5), // Approximate or request duration
            'submitted_at' => now(),
            'score' => 0,
            'total_possible_marks' => 0,
            'percentage' => 0,
            'passed' => false,
        ]);

        foreach ($assessment->questions as $question) {
            $totalPossible += (float) $question->marks;
            $selectedOptionId = $submittedAnswers[$question->id] ?? null;

            $isCorrect = false;
            $marksAwarded = 0.0;

            if ($selectedOptionId) {
                $option = $question->options->firstWhere('id', $selectedOptionId);
                if ($option && $option->is_correct) {
                    $isCorrect = true;
                    $marksAwarded = (float) $question->marks;
                } else {
                    $marksAwarded = -1 * (float) $assessment->negative_marks_per_question;
                }
            }

            $totalScore += $marksAwarded;

            AssessmentAnswer::create([
                'assessment_attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_option_id' => $selectedOptionId,
                'is_correct' => $isCorrect,
                'marks_awarded' => $marksAwarded,
            ]);

            // If question is tied to competency and answered correctly, reward evidence
            if ($isCorrect && $question->competency_id) {
                $comp = $question->competency;
                if ($comp) {
                    $this->competencyService->recordCompetencyAchievement(
                        $user,
                        $comp,
                        $comp->required_level,
                        "Demonstrated competency in MCQ assessment: {$assessment->title}"
                    );
                }
            }
        }

        $totalScore = max(0, $totalScore);
        $percentage = $totalPossible > 0 ? round(($totalScore / $totalPossible) * 100, 2) : 0;
        $passed = $percentage >= $assessment->passing_percentage;

        $attempt->update([
            'score' => $totalScore,
            'total_possible_marks' => $totalPossible,
            'percentage' => $percentage,
            'passed' => $passed,
        ]);

        AuditLogService::log('assessment.submitted', $attempt, null, [
            'user' => $user->name,
            'score' => $totalScore,
            'percentage' => $percentage,
            'passed' => $passed,
        ]);

        // If passed and course is 100% completed, check certificate eligibility
        if ($passed) {
            $this->certificateService->issueIfEligible($user, $assessment->course);
        }

        $this->notificationService->send($user, 'Assessment Result', "{$assessment->title}: {$percentage}% ({$assessment->course->title}).", $passed ? 'success' : 'warning', route('trainee.assessments.result', $attempt->id), 'assessment_results');

        return redirect()->route('trainee.assessments.result', $attempt->id);
    }

    public function result(AssessmentAttempt $attempt): View
    {
        if ($attempt->user_id !== Auth::id() && ! Auth::user()->isAdmin() && ! Auth::user()->isTrainer()) {
            abort(403);
        }

        $attempt->load(['assessment.course', 'answers.question.options', 'answers.selectedOption']);

        return view('trainee.assessments.result', compact('attempt'));
    }

    public function submitPractical(Request $request, PracticalAssessment $practical): RedirectResponse
    {
        abort_unless(Auth::user()->can('view', $practical), 403);

        $request->validate([
            'submission_text' => 'required|string|min:50',
            'file' => 'nullable|file|mimes:pdf,docx,zip,png,jpg|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('practical_submissions');
        }

        $submission = PracticalSubmission::create([
            'practical_assessment_id' => $practical->id,
            'user_id' => Auth::id(),
            'submission_text' => $request->input('submission_text'),
            'file_path' => $filePath,
            'status' => 'submitted',
        ]);

        AuditLogService::log('practical.submitted', $submission);

        return back()->with('success', 'Practical scenario response submitted! Trainer review is pending.');
    }
}
