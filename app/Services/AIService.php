<?php

namespace App\Services;

use App\Contracts\AiProvider;
use App\Models\AIMessage;
use App\Models\Competency;
use App\Models\User;
use App\Services\Ai\FallbackAiProvider;
use Throwable;

class AIService
{
    public function __construct(
        protected AiProvider $provider
    ) {}

    /**
     * Answer Trainee questions with platform-aware, disaster-management context and fallback.
     */
    public function askAssistant(string $query, ?User $user = null): string
    {
        $commandAnswer = $user && str_starts_with(trim($query), '/')
            ? $this->handleCommand(trim($query), $user)
            : null;

        $conversation = $user?->aiConversations()->latest()->first();
        $history = $conversation?->messages()->latest()->limit(10)->get()->reverse()
            ->map(fn (AIMessage $message): array => ['role' => $message->role, 'content' => $message->content])
            ->values()->all() ?? [];
        $systemPrompt = 'You are the Capacity Connect AI Learning Assistant. Be concise, distinguish observed platform data from recommendations, cite available learning material, and escalate questions requiring official or unpublished policy to a trainer or department lead.';

        try {
            $answer = $commandAnswer ?? $this->provider->complete($systemPrompt, $query, $history);
        } catch (Throwable) {
            $answer = (new FallbackAiProvider)->complete($systemPrompt, $query, $history);
        }

        if ($user) {
            $conversation ??= $user->aiConversations()->create(['title' => 'Learning Assistant']);
            $conversation->messages()->create(['role' => 'user', 'content' => $query, 'is_ai_generated' => false]);
            $conversation->messages()->create(['role' => 'assistant', 'content' => $answer, 'is_ai_generated' => true]);
        }

        return $answer;
    }

    protected function handleCommand(string $query, User $user): string
    {
        [$command, $argument] = array_pad(preg_split('/\s+/', $query, 2), 2, '');

        return match (strtolower($command)) {
            '/help' => "Available commands:\n/help - show this list\n/courses - list your courses\n/certificates - list your certificates\n/competencies - show your competency levels\n/progress - show your learning progress\n/search <term> - find published courses",
            '/courses' => $this->formatCourses($user),
            '/certificates' => $this->formatCertificates($user),
            '/competencies' => $this->formatCompetencies($user),
            '/progress' => $this->formatProgress($user),
            '/search' => $this->searchCourses($argument),
            default => "Unknown command '{$command}'. Type /help to see available commands.",
        };
    }

    protected function formatCourses(User $user): string
    {
        $courses = $user->enrollments()->with('course')->latest()->limit(10)->get();
        if ($courses->isEmpty()) {
            return 'You are not enrolled in any courses yet.';
        }

        return "Your courses:\n".$courses->map(fn ($enrollment): string => "- {$enrollment->course->title}: {$enrollment->progress_percentage}% ({$enrollment->status})")->implode("\n");
    }

    protected function formatCertificates(User $user): string
    {
        $certificates = $user->certificates()->with('course')->latest('issue_date')->limit(10)->get();
        if ($certificates->isEmpty()) {
            return 'You do not have any certificates yet.';
        }

        return "Your certificates:\n".$certificates->map(fn ($certificate): string => "- {$certificate->course->title}: ".strtoupper($certificate->status)." ({$certificate->certificate_number})")->implode("\n");
    }

    protected function formatCompetencies(User $user): string
    {
        $competencies = $user->userCompetencies()->with('competency')->latest('assessed_at')->limit(10)->get();
        if ($competencies->isEmpty()) {
            return 'No competency evidence has been recorded yet.';
        }

        return "Your competency profile:\n".$competencies->map(fn ($entry): string => "- {$entry->competency->title}: level {$entry->proficiency_level} (observed evidence)")->implode("\n");
    }

    protected function formatProgress(User $user): string
    {
        $enrollments = $user->enrollments()->get();
        $completed = $enrollments->where('status', 'completed')->count();
        $average = $enrollments->isEmpty() ? 0 : round($enrollments->avg('progress_percentage'), 1);

        return "Learning progress:\n- Courses completed: {$completed}\n- Average course progress: {$average}%";
    }

    protected function searchCourses(string $argument): string
    {
        if (trim($argument) === '') {
            return 'Usage: /search <course topic>';
        }

        $courses = Course::where('status', 'published')
            ->where(fn ($builder) => $builder->where('title', 'like', '%'.trim($argument).'%')->orWhere('short_description', 'like', '%'.trim($argument).'%'))
            ->limit(5)->get();
        if ($courses->isEmpty()) {
            return "No published courses matched '{$argument}'.";
        }

        return "Matching published courses:\n".$courses->map(fn ($course): string => "- {$course->title} ({$course->difficulty}, {$course->duration_hours} hours)")->implode("\n");
    }

    /**
     * Generate content drafts for Trainer/Admin Human-in-the-Loop review.
     */
    public function generateContent(string $topic, string $type, string $difficulty = 'Intermediate', ?Competency $competency = null): string
    {
        $compTitle = $competency ? $competency->title : 'Disaster Management & Response';

        switch ($type) {
            case 'course_description':
                return "This comprehensive {$difficulty} course on '{$topic}' equips emergency response personnel and administrative officers with essential protocols in {$compTitle}.\n\nLearners will explore standard operating procedures, multi-agency coordination frameworks, real-time risk mitigation strategies, and post-incident stabilization methods aligned with national resilience standards.";

            case 'learning_objectives':
                return json_encode([
                    "Understand the core principles and regulatory mandates of {$topic}.",
                    "Execute rapid assessment protocols during early-stage {$topic} emergencies.",
                    'Coordinate resource logistics and field communications across inter-agency units.',
                    'Evaluate operational risks and maintain command safety standards under stress.',
                    "Demonstrate proficiency in {$compTitle} at the {$difficulty} level.",
                ], JSON_PRETTY_PRINT);

            case 'lesson_summary':
                return "### Lesson Summary: {$topic}\n\nIn this lesson, we examined key operational triggers and safety thresholds in {$topic}. Key takeaways include:\n1. **Early Warning Indicators**: Detecting pre-incident signals and activating alert level 2.\n2. **Command Structure**: Aligning field teams under Incident Command System (ICS) protocols.\n3. **Community Evacuation & Resource Logistics**: Securing primary escape corridors and priority supplies.\n4. **Review & Debrief**: Conducting standard after-action reviews to enhance institutional resilience.";

            case 'mcqs':
                return json_encode([
                    [
                        'question' => "What is the primary objective of the initial incident assessment in {$topic}?",
                        'options' => [
                            ['text' => 'Establishing rapid situational awareness and life-safety priorities', 'is_correct' => true],
                            ['text' => 'Calculating long-term infrastructure repair budgets', 'is_correct' => false],
                            ['text' => 'Issuing public press releases before verifying casualty reports', 'is_correct' => false],
                            ['text' => 'Deploying heavy reconstruction machinery immediately', 'is_correct' => false],
                        ],
                        'explanation' => 'Initial response protocols mandate prioritizing life safety, immediate threat isolation, and situational verification.',
                        'difficulty' => $difficulty,
                    ],
                    [
                        'question' => "Which command system is globally standardized for inter-agency emergency management in {$topic}?",
                        'options' => [
                            ['text' => 'Incident Command System (ICS)', 'is_correct' => true],
                            ['text' => 'Decentralized Ad-hoc Protocol (DAP)', 'is_correct' => false],
                            ['text' => 'Static Bureaucracy Matrix (SBM)', 'is_correct' => false],
                            ['text' => 'Linear Task Queue (LTQ)', 'is_correct' => false],
                        ],
                        'explanation' => 'The Incident Command System (ICS) provides a standardized, on-scene, all-hazard incident management hierarchy.',
                        'difficulty' => $difficulty,
                    ],
                    [
                        'question' => "When communicating during an active {$topic} crisis, emergency personnel must ensure:",
                        'options' => [
                            ['text' => 'Closed-loop communication with clear concise terminology', 'is_correct' => true],
                            ['text' => 'Unverified rumors are broadcast to maintain transparency', 'is_correct' => false],
                            ['text' => 'Only written postal memos are exchanged', 'is_correct' => false],
                            ['text' => 'All radio channels are merged into a single unrestricted frequency', 'is_correct' => false],
                        ],
                        'explanation' => 'Closed-loop communications eliminate ambiguity and confirm that critical emergency directives are received and understood.',
                        'difficulty' => $difficulty,
                    ],
                ], JSON_PRETTY_PRINT);

            case 'practice_questions':
                return "1. **Scenario Analysis**: Describe the initial 30-minute actions required when a Category 4 warning is issued for your operational zone.\n2. **Resource Allocation**: How would you prioritize emergency water distribution when supply routes are cut off?\n3. **Inter-agency Coordination**: Outline the protocol for handing over incident command to the national disaster response force.";

            default:
                return "Generated foundational guidelines and learning material for {$topic} under the {$compTitle} framework.";
        }
    }

    protected function getRuleBasedFallback(string $query): string
    {
        $q = strtolower($query);

        if (str_contains($q, 'cyclone') || str_contains($q, 'storm') || str_contains($q, 'wind')) {
            return "🌪️ **Cyclone Preparedness Protocol**:\n\n1. **Alert Levels**: Monitor IMD color-coded bulletins (Yellow = Watch, Orange = Alert, Red = Warning).\n2. **Key Action**: Secure loose objects, verify satellite communication devices, inspect shelter generator fuel, and evacuate low-lying coastal sectors at least 12 hours prior to landfall.\n3. **Recommended Course**: Check out our certified course *'Coastal Cyclone Disaster Mitigation & Emergency Shelter Management'* in the Course Catalog.";
        }

        if (str_contains($q, 'flood') || str_contains($q, 'water') || str_contains($q, 'river')) {
            return "🌊 **Flood Response Operations**:\n\n1. **Inundation Mapping**: Utilize GIS contour maps to identify breach risks and safe high-ground rallying points.\n2. **Logistics**: Position inflatable rescue boats (IRBs), life vests, and water purification units upstream of vulnerable embankments.\n3. **Recommended Training**: Enroll in *'Advanced Flood Risk Assessment & Urban Drainage Management'* to complete your Intermediate competency requirements.";
        }

        if (str_contains($q, 'certificate') || str_contains($q, 'certification') || str_contains($q, 'verify')) {
            return "📜 **Certification & Verification**:\n\nCertificates are automatically generated upon achieving 100% course progress and scoring >= 70% in all required MCQ and practical assessments. Each certificate contains a tamper-evident SHA-256 verification code accessible at `/certificate/verify/{code}`.";
        }

        if (str_contains($q, 'digital twin') || str_contains($q, 'competency') || str_contains($q, 'skill')) {
            return "🧬 **Competency Digital Twin**:\n\nYour Digital Twin synthesizes your verified qualifications, completed assessments, disaster simulations, and supervisor ratings across 5 proficiency levels (Beginner to Expert). Visit **'My Competency Digital Twin'** in your trainee sidebar to inspect your active skill gap recommendations.";
        }

        return "🤖 **Capacity Connect Learning Companion**:\n\nI can assist you with course concepts, emergency disaster response SOPs, assessment preparation, and your competency development path. You can browse active certified courses in the Course Catalog or take our interactive practical disaster simulations!";
    }
}
