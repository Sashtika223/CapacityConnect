<?php

namespace App\Services;

use App\Models\Competency;
use App\Models\Course;
use App\Models\User;

class CapacitySimulatorService
{
    /**
     * Run simulation projections based on input parameters.
     */
    public function runSimulation(array $params): array
    {
        $targetDepartmentId = $params['department_id'] ?? null;
        $targetCompetencyId = $params['competency_id'] ?? null;
        $workforceCount = (int) ($params['workforce_count'] ?? 100);
        $trainingCapacityPerBatch = (int) ($params['batch_size'] ?? 20);
        $completionRateAssumption = (float) ($params['completion_rate'] ?? 85.0); // %
        $targetProficiencyLevel = (int) ($params['target_level'] ?? 4); // 1-5

        // Fetch competency & available courses
        $competency = $targetCompetencyId ? Competency::find($targetCompetencyId) : Competency::first();
        $courses = $competency ? $competency->courses()->where('status', 'published')->get() : Course::where('status', 'published')->take(2)->get();
        $trainersAvailable = User::where('role', 'trainer')->where('status', 'active')
            ->when($targetDepartmentId, fn ($query) => $query->where('department_id', $targetDepartmentId))
            ->count();
        $actualWorkforceCount = User::where('role', 'trainee')->where('status', 'active')
            ->when($targetDepartmentId, fn ($query) => $query->where('department_id', $targetDepartmentId))
            ->count();
        $workforceCount = $workforceCount > 0 ? $workforceCount : $actualWorkforceCount;

        // Calculate batches needed
        $batches = ceil($workforceCount / max(1, $trainingCapacityPerBatch));
        $avgCourseWeeks = 4;
        $estimatedTotalWeeks = $trainersAvailable > 0
            ? ceil(($batches / max(1, min($trainersAvailable, 3))) * $avgCourseWeeks)
            : $batches * $avgCourseWeeks;

        $projectedCertified = (int) round(($workforceCount * ($completionRateAssumption / 100)));
        $projectedSkillDeficit = max(0, $workforceCount - $projectedCertified);

        $trainingHoursTotal = $courses->sum('duration_hours') * $workforceCount;

        return [
            'is_estimate' => true,
            'disclaimer' => 'SIMULATION / ESTIMATE: This projection calculates operational readiness estimates based on statistical assumptions and trainer bandwidth. Actual emergency performance may vary.',
            'parameters' => [
                'workforce_count' => $workforceCount,
                'department_id' => $targetDepartmentId,
                'batch_size' => $trainingCapacityPerBatch,
                'completion_rate' => $completionRateAssumption,
                'target_level' => $targetProficiencyLevel,
                'competency_title' => $competency?->title ?? 'Incident Command & Disaster Response',
            ],
            'outputs' => [
                'total_batches' => $batches,
                'estimated_weeks' => $estimatedTotalWeeks,
                'trainers_available' => $trainersAvailable,
                'projected_certified' => $projectedCertified,
                'projected_ready_headcount' => $projectedCertified,
                'projected_deficit' => $projectedSkillDeficit,
                'total_training_hours' => $trainingHoursTotal,
                'readiness_percentage' => round(($projectedCertified / max(1, $workforceCount)) * 100, 1),
                'priority_level' => $projectedSkillDeficit > ($workforceCount * 0.3) ? 'HIGH PRIORITY' : 'MODERATE',
            ],
        ];
    }
}
