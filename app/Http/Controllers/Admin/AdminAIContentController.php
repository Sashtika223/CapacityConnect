<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIContentGeneration;
use App\Models\Competency;
use App\Models\Course;
use App\Services\AIService;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAIContentController extends Controller
{
    public function __construct(
        protected AIService $aiService
    ) {}

    public function index(): View
    {
        $generations = AIContentGeneration::with(['user', 'reviewer', 'competency', 'course'])
            ->latest()
            ->paginate(15);

        $competencies = Competency::orderBy('title')->get();
        $courses = Course::orderBy('title')->get();

        return view('admin.ai-content.index', compact('generations', 'competencies', 'courses'));
    }

    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'type' => 'required|in:course_description,learning_objectives,lesson_summary,mcqs,practice_questions',
            'difficulty' => 'required|in:Beginner,Intermediate,Advanced,Expert',
            'competency_id' => 'nullable|exists:competencies,id',
            'course_id' => 'nullable|exists:courses,id',
            'prompt' => 'nullable|string',
        ]);

        $competency = $request->filled('competency_id') ? Competency::find($request->input('competency_id')) : null;

        $generatedText = $this->aiService->generateContent(
            $request->input('topic'),
            $request->input('type'),
            $request->input('difficulty'),
            $competency
        );

        $generation = AIContentGeneration::create([
            'user_id' => Auth::id(),
            'topic' => $request->input('topic'),
            'type' => $request->input('type'),
            'difficulty' => $request->input('difficulty'),
            'competency_id' => $request->input('competency_id'),
            'course_id' => $request->input('course_id'),
            'prompt' => $request->input('prompt') ?? 'Standard generation request for '.$request->input('topic'),
            'generated_content' => $generatedText,
            'status' => 'ai_generated',
        ]);

        AuditLogService::log('ai_content.generated', $generation);

        return back()->with('success', 'AI draft generated successfully! Pending human editorial review.');
    }

    public function review(Request $request, AIContentGeneration $generation): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:approve,reject,publish',
            'generated_content' => 'required|string',
            'rejection_reason' => 'nullable|string',
        ]);

        $action = $request->input('action');
        $newStatus = match ($action) {
            'approve' => 'approved',
            'reject' => 'rejected',
            'publish' => 'published',
            default => 'under_review',
        };

        $generation->update([
            'generated_content' => $request->input('generated_content'),
            'status' => $newStatus,
            'reviewer_id' => Auth::id(),
            'rejection_reason' => $request->input('rejection_reason'),
            'reviewed_at' => now(),
        ]);

        AuditLogService::log('ai_content.reviewed', $generation, null, ['status' => $newStatus]);

        return back()->with('success', "AI draft updated to {$newStatus}.");
    }
}
