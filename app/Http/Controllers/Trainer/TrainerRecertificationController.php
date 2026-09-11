<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainerRecertificationController extends Controller
{
    /**
     * Standard 2026 Real-Time Emergency Assessment Questions for Trainer Recertification.
     */
    protected array $recertificationQuestions = [
        [
            'id' => 1,
            'question' => 'Under the 2026 Revised IMD Protocol for coastal cyclones, what is the mandatory minimum lead time required for Stage 3 (Cyclone Warning) bulletin issuance?',
            'options' => [
                'a' => '12 hours prior to landfall',
                'b' => '24 hours prior to landfall',
                'c' => '48 hours prior to landfall',
                'd' => '72 hours prior to landfall',
            ],
            'correct' => 'b',
            'explanation' => 'Stage 3 Cyclone Warning requires issuance at least 24 hours in advance to complete Tier-1 coastal evacuations.',
        ],
        [
            'id' => 2,
            'question' => 'When urban inundation current velocities exceed 12 knots during extreme monsoon discharge, which operational protocol must lead instructors enforce?',
            'options' => [
                'a' => 'Deploy high-profile wheeled transport along unpaved causeways',
                'b' => 'Halt un-tethered inflatable boat missions and establish tensioned diagonal highline rope systems',
                'c' => 'Instruct civilian volunteers to perform human-chain extractions without personal flotation devices',
                'd' => 'Switch all UHF communication to civilian mobile frequencies',
            ],
            'correct' => 'b',
            'explanation' => 'Water currents above 10-12 knots pose severe capsizing risks; standardized SOP mandates highline rope tethering and motorized IRB anchoring.',
        ],
        [
            'id' => 3,
            'question' => 'In multi-purpose cyclone shelters during multi-day grid failure, what is the mandatory chlorination standard for stored overhead drinking water?',
            'options' => [
                'a' => '0.2 ppm residual chlorine',
                'b' => '0.5 ppm residual chlorine',
                'c' => '2.0 ppm residual chlorine with daily testing',
                'd' => 'No chlorination required if water is boiled',
            ],
            'correct' => 'c',
            'explanation' => 'Post-disaster epidemic prevention requires a mandatory 2.0 ppm residual chlorine buffer for communal shelter storage reservoirs.',
        ],
        [
            'id' => 4,
            'question' => 'Under the unified Incident Command System (ICS) 2026 real-time coordination standard, which channel is prioritized for inter-agency drone reconnaissance telemetry?',
            'options' => [
                'a' => 'Public cellular 4G/5G networks only',
                'b' => 'Dedicated Tactical Air-to-Ground Mesh Network (ICS Channel Alpha-9)',
                'c' => 'Commercial VHF FM Marine Band',
                'd' => 'Local FM Radio broadcast relay',
            ],
            'correct' => 'b',
            'explanation' => 'ICS Channel Alpha-9 provides dedicated secure mesh encryption immune to terrestrial cellular tower collapse.',
        ],
        [
            'id' => 5,
            'question' => 'What is the required trigger threshold for initiating Phase-2 preventive evacuation in coastal low-lying zones?',
            'options' => [
                'a' => 'Storm surge forecast exceeding 1.5 meters with high tide coincidence within 36 hours',
                'b' => 'When rainfall exceeds 10mm in 24 hours',
                'c' => 'Only after cyclone eye crosses the coastline',
                'd' => 'When wind gusts exceed 40 km/h',
            ],
            'correct' => 'a',
            'explanation' => 'Phase-2 mandatory preventive evacuation is initiated 36 hours prior when surge height projection exceeds 1.5m above normal astronomical tide.',
        ],
    ];

    /**
     * Real-time 2026 emergency updates briefing material.
     */
    protected array $realTimeUpdates = [
        [
            'title' => '2026 IMD Cyclone Warning Protocol Revision',
            'category' => 'Early Warning & Radar',
            'badge' => 'CRITICAL SOP',
            'content' => 'Introduction of 4-stage color coded alerts with mandatory 24-hour lead time for Stage 3 Warning. Automated Doppler radar wind-field velocity integration replaces manual estimation.',
        ],
        [
            'title' => 'Swiftwater & Urban Inundation Safety Mandate',
            'category' => 'Tactical Rescue',
            'badge' => 'REVISED STANDARD',
            'content' => 'Prohibition of unanchored motorized boats in currents exceeding 12 knots. Tensioned mechanical advantage rope systems and sonar hazard scanning are now mandatory for all instructor-led units.',
        ],
        [
            'title' => 'Disaster Shelter Hygiene & Anti-Epidemic Standard',
            'category' => 'Public Health',
            'badge' => 'COMPLIANCE',
            'content' => 'Mandatory 2.0 ppm chlorine residual buffer for shelter water tanks, solar-hybrid emergency generator elevation of min. 2.0m, and real-time biometric evacuee manifests.',
        ],
        [
            'title' => 'Inter-Agency Drone & ICS Mesh Communication',
            'category' => 'Incident Command',
            'badge' => 'NEW 2026',
            'content' => 'Deployment of Channel Alpha-9 tactical mesh networks for drone feeds and survivor localization, bypassing disrupted commercial cellular infrastructure.',
        ],
    ];

    /**
     * Show the Trainer Recertification Assessment and Real-Time Updates.
     */
    public function show(string $certificateNumber): View
    {
        $certificate = Certificate::with(['user', 'course.department', 'course.trainer'])
            ->where('certificate_number', $certificateNumber)
            ->orWhere('verification_code', $certificateNumber)
            ->firstOrFail();

        return view('trainer.recertify', [
            'certificate' => $certificate,
            'questions' => $this->recertificationQuestions,
            'updates' => $this->realTimeUpdates,
            'result' => null,
        ]);
    }

    /**
     * Evaluate the Recertification Exam and renew certificate if passed.
     */
    public function submit(Request $request, string $certificateNumber): View|RedirectResponse
    {
        $certificate = Certificate::with(['user', 'course.department', 'course.trainer'])
            ->where('certificate_number', $certificateNumber)
            ->orWhere('verification_code', $certificateNumber)
            ->firstOrFail();

        $answers = $request->input('answers', []);
        $correctCount = 0;
        $totalQuestions = count($this->recertificationQuestions);
        $questionResults = [];

        foreach ($this->recertificationQuestions as $q) {
            $userAns = $answers[$q['id']] ?? null;
            $isCorrect = ($userAns === $q['correct']);
            if ($isCorrect) {
                $correctCount++;
            }

            $questionResults[] = [
                'id' => $q['id'],
                'question' => $q['question'],
                'user_answer' => $userAns ? $q['options'][$userAns] ?? $userAns : 'Not answered',
                'correct_answer' => $q['options'][$q['correct']],
                'is_correct' => $isCorrect,
                'explanation' => $q['explanation'],
            ];
        }

        $percentage = round(($correctCount / $totalQuestions) * 100, 1);
        $passed = $percentage >= 80.0; // 80% passing threshold (4 of 5)

        if ($passed) {
            // Renew certificate validity
            $certificate->update([
                'status' => 'valid',
                'issue_date' => now()->toDateString(),
                'expiry_date' => now()->addYears(2)->toDateString(), // 2 years renewal
                'revocation_reason' => null,
            ]);

            AuditLogService::log('trainer.recertified', $certificate, null, [
                'certificate_number' => $certificate->certificate_number,
                'trainer' => $certificate->user->name,
                'score' => "{$percentage}% ({$correctCount}/{$totalQuestions})",
                'new_expiry' => $certificate->expiry_date->format('Y-m-d'),
            ]);
        }

        $result = [
            'passed' => $passed,
            'score' => $percentage,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'question_results' => $questionResults,
            'new_expiry' => $passed ? $certificate->expiry_date->format('d M Y') : null,
        ];

        return view('trainer.recertify', [
            'certificate' => $certificate->fresh(),
            'questions' => $this->recertificationQuestions,
            'updates' => $this->realTimeUpdates,
            'result' => $result,
        ]);
    }
}
