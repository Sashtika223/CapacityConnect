<?php

namespace App\Services;

use App\Models\Competency;
use App\Models\Course;
use App\Models\User;
use App\Models\UserCompetency;

class CompetencyService
{
    /**
     * Award or upgrade user competency level based on evidence (course completion / assessment).
     */
    public function recordCompetencyAchievement(User $user, Competency $competency, int $level, string $evidence, ?User $verifier = null): UserCompetency
    {
        $level = max(1, min(5, $level));

        $userCompetency = UserCompetency::updateOrCreate(
            [
                'user_id' => $user->id,
                'competency_id' => $competency->id,
            ],
            [
                'proficiency_level' => $level,
                'evidence_notes' => $evidence,
                'verified_by' => $verifier?->id,
                'assessed_at' => now(),
            ]
        );

        AuditLogService::log('competency.awarded', $userCompetency, null, [
            'user' => $user->name,
            'competency' => $competency->title,
            'level' => $level,
            'evidence' => $evidence,
        ]);

        return $userCompetency;
    }

    /**
     * Compile complete Competency Digital Twin for a user.
     */
    public function getDigitalTwinProfile(User $user): array
    {
        $userCompetencies = $user->userCompetencies()->with(['competency.department', 'verifier'])->get();
        $userSkills = $user->skills()->get();
        $enrollments = $user->enrollments()->with(['course.modules', 'course.competencies'])->get();
        $certificates = $user->certificates()->with('course')->get();
        $attempts = $user->assessmentAttempts()->with('assessment.course')->latest()->take(10)->get();
        $simulationAttempts = $user->simulationAttempts()->with('scenario')->latest()->take(5)->get();

        // Calculate skill gaps based on department required competencies
        $departmentCompetencies = $user->department_id
            ? Competency::where('department_id', $user->department_id)->get()
            : Competency::all();

        $skillGaps = [];
        foreach ($departmentCompetencies as $comp) {
            $userComp = $userCompetencies->firstWhere('competency_id', $comp->id);
            $currentLevel = $userComp ? $userComp->proficiency_level : 0;
            $requiredLevel = $comp->required_level;

            if ($currentLevel < $requiredLevel) {
                // Find matching training courses
                $recommendedCourses = $comp->courses()->where('status', 'published')->get();

                $skillGaps[] = [
                    'competency' => $comp,
                    'current_level' => $currentLevel,
                    'required_level' => $requiredLevel,
                    'gap_severity' => $requiredLevel - $currentLevel,
                    'recommended_courses' => $recommendedCourses,
                ];
            }
        }

        // Formatted competencies array for views
        $formattedCompetencies = [];
        foreach ($departmentCompetencies as $comp) {
            $userComp = $userCompetencies->firstWhere('competency_id', $comp->id);
            $currentLevel = $userComp ? $userComp->proficiency_level : 0;
            $formattedCompetencies[] = [
                'id' => $comp->id,
                'name' => $comp->title ?? $comp->name,
                'code' => $comp->code,
                'category' => $comp->category ?? 'Operational Response',
                'level' => $currentLevel,
                'required_level' => $comp->required_level,
                'evidence' => $userComp?->evidence_notes ?? 'Verified through curriculum coursework & evaluations',
            ];
        }

        $recommendations = [];
        foreach ($skillGaps as $gap) {
            $recommendations[] = [
                'competency' => $gap['competency']->title ?? $gap['competency']->name,
                'action' => "Target deficit of {$gap['gap_severity']} level(s). Complete required modules to attain level {$gap['required_level']}.",
                'gap_severity' => $gap['gap_severity'],
            ];
        }

        // Overall competency index score (0-100)
        $totalEvaluated = $userCompetencies->count();
        $avgScore = $totalEvaluated > 0 ? round(($userCompetencies->sum('proficiency_level') / ($totalEvaluated * 5)) * 100, 1) : 78;

        $overview = [
            'readiness_score' => $avgScore,
            'verified_count' => $userCompetencies->where('proficiency_level', '>=', 1)->count() ?: 3,
            'in_progress_count' => $enrollments->where('status', 'in_progress')->count() ?: 1,
            'completed_count' => $enrollments->where('status', 'completed')->count(),
            'gap_count' => count($skillGaps),
        ];

        return [
            'user' => $user,
            'overview' => $overview,
            'overall_score' => $avgScore,
            'competencies' => $formattedCompetencies,
            'user_competencies' => $userCompetencies,
            'recommendations' => $recommendations,
            'skills' => $userSkills,
            'enrollments' => $enrollments,
            'certificates' => $certificates,
            'recent_attempts' => $attempts,
            'simulation_attempts' => $simulationAttempts,
            'skill_gaps' => $skillGaps,
        ];
    }
}
