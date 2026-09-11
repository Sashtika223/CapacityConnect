<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Competency;
use App\Models\Department;
use App\Models\User;

class RiskRadarService
{
    /**
     * Scan personnel and calculate transparent risk radar metrics.
     */
    public function analyzeOrganizationalRisks(): array
    {
        $trainees = User::where('role', 'trainee')->with(['department', 'userCompetencies.competency', 'certificates', 'enrollments'])->get();
        $departments = Department::with('competencies')->get();

        $riskItems = [];
        $criticalDeficits = [];
        $highRiskCount = 0;
        $mediumRiskCount = 0;
        $lowRiskCount = 0;

        foreach ($trainees as $trainee) {
            $dept = $trainee->department;
            $requiredCompetencies = $dept ? $dept->competencies : Competency::take(5)->get();

            foreach ($requiredCompetencies as $comp) {
                $userComp = $trainee->userCompetencies->firstWhere('competency_id', $comp->id);
                $currentLevel = $userComp ? $userComp->proficiency_level : 0;
                $gap = $comp->required_level - $currentLevel;

                // Check expired certifications in this area
                $expiredCert = $trainee->certificates->where('status', 'expired')->first();

                $riskLevel = 'LOW';
                $reasons = [];

                if ($gap >= 3 || ($gap >= 2 && $comp->required_level >= 4)) {
                    $riskLevel = 'HIGH';
                    $reasons[] = "Critical skill deficit: Required level is {$comp->required_level} ({$comp->required_level_name}), but employee holds level {$currentLevel}.";
                } elseif ($gap >= 1) {
                    $riskLevel = 'MEDIUM';
                    $reasons[] = "Moderate gap: Needs advancement to reach {$comp->required_level_name} standard.";
                }

                if ($expiredCert) {
                    $riskLevel = 'HIGH';
                    $reasons[] = 'Expired emergency response certification requiring immediate recertification.';
                }

                // Inactivity check
                $activeEnrollment = $trainee->enrollments->whereIn('status', ['enrolled', 'in_progress'])->first();
                if (! $activeEnrollment && $gap > 0) {
                    $reasons[] = 'Employee has no active training enrolled to address this competency gap.';
                }

                $item = [
                    'user' => $trainee,
                    'department' => $dept?->name ?? 'General Workforce',
                    'competency' => $comp,
                    'current_level' => $currentLevel,
                    'required_level' => $comp->required_level,
                    'risk_level' => $riskLevel,
                    'reasons' => $reasons,
                    'mitigation_course' => $comp->courses()->where('status', 'published')->first(),
                ];

                if ($riskLevel === 'HIGH') {
                    $highRiskCount++;
                    $criticalDeficits[] = $item;
                } elseif ($riskLevel === 'MEDIUM') {
                    $mediumRiskCount++;
                } else {
                    $lowRiskCount++;
                }

                if ($riskLevel !== 'LOW' || count($riskItems) < 10) {
                    $riskItems[] = $item;
                }
            }
        }

        // Department-by-Department Risk Calculation
        $departmentRisks = [];
        $highRiskDeptCount = 0;

        foreach ($departments as $dept) {
            $deptTrainees = $trainees->where('department_id', $dept->id);
            $userCount = $deptTrainees->count();

            $deptHighGaps = 0;
            foreach ($deptTrainees as $t) {
                foreach ($dept->competencies as $comp) {
                    $uComp = $t->userCompetencies->firstWhere('competency_id', $comp->id);
                    $cLevel = $uComp ? $uComp->proficiency_level : 0;
                    if ($comp->required_level - $cLevel >= 2) {
                        $deptHighGaps++;
                    }
                }
            }

            if ($userCount > 0 && $deptHighGaps >= 2) {
                $dRiskLevel = 'High Risk';
                $dScore = 85;
                $dReason = "Severe deficit in {$dept->name} operational roles. Multiple personnel lack mandatory Stage-4 competency.";
                $highRiskDeptCount++;
            } elseif ($userCount > 0 && $deptHighGaps >= 1) {
                $dRiskLevel = 'Medium Risk';
                $dScore = 55;
                $dReason = 'Moderate skill gaps identified. Targeted refresher modules recommended.';
            } else {
                $dRiskLevel = 'Low Risk';
                $dScore = 20;
                $dReason = 'Workforce readiness meets minimum operational thresholds.';
            }

            $departmentRisks[] = [
                'department_name' => $dept->name,
                'user_count' => $userCount,
                'risk_level' => $dRiskLevel,
                'risk_score' => $dScore,
                'reason' => $dReason,
            ];
        }

        $overallStatus = ($highRiskCount > 0 || $highRiskDeptCount > 0) ? 'High Risk' : ($mediumRiskCount > 0 ? 'Medium Risk' : 'Low Risk');
        $expiringCertsCount = Certificate::whereIn('status', ['expired', 'expiring_soon'])->count();

        $mitigationSteps = [
            [
                'title' => 'Mandatory Rapid Refresher Deployment',
                'description' => 'Fast-track enrollment for personnel with Level-1 proficiency in high-consequence operational roles.',
            ],
            [
                'title' => 'Trainer Credential Verification & Recertification',
                'description' => 'Ensure all active instructors complete the 2026 Real-Time Assessment updates prior to lead trainer deployment.',
            ],
            [
                'title' => 'Automated Tactical Simulation Drills',
                'description' => 'Trigger AI-assisted multi-agency flood and cyclone simulation scenarios for frontline response units.',
            ],
        ];

        return [
            'overall_status' => $overallStatus,
            'high_risk_department_count' => $highRiskDeptCount,
            'critical_deficits' => $criticalDeficits,
            'expiring_certificates_count' => $expiringCertsCount,
            'department_risks' => $departmentRisks,
            'mitigation_steps' => $mitigationSteps,
            'summary' => [
                'high_risk' => $highRiskCount,
                'medium_risk' => $mediumRiskCount,
                'low_risk' => $lowRiskCount,
                'total_analyzed' => count($trainees),
            ],
            'items' => collect($riskItems)->sortByDesc(function ($item) {
                return $item['risk_level'] === 'HIGH' ? 3 : ($item['risk_level'] === 'MEDIUM' ? 2 : 1);
            })->values()->all(),
        ];
    }
}
