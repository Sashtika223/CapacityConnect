<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use App\Models\Competency;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\PracticalAssessment;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $trainer1 = User::where('email', 'trainer.cyclone@capacityconnect.gov')->first();
        $trainer2 = User::where('email', 'trainer.rescue@capacityconnect.gov')->first();
        $trainee1 = User::where('email', 'trainee1@capacityconnect.gov')->first();
        $trainee2 = User::where('email', 'trainee2@capacityconnect.gov')->first();

        $ndrDept = Department::where('code', 'NDR-TO')->first();
        $mfDept = Department::where('code', 'MF-EWS')->first();
        $elDept = Department::where('code', 'EL-SCM')->first();

        $cycloneComp = Competency::where('code', 'COMP-NDR-01')->first();
        $warningComp = Competency::where('code', 'COMP-MF-01')->first();
        $floodComp = Competency::where('code', 'COMP-NDR-02')->first();
        $logisticsComp = Competency::where('code', 'COMP-EL-01')->first();

        // COURSE 1: Coastal Cyclone Disaster Mitigation & Emergency Shelter Management
        $course1 = Course::updateOrCreate(
            ['slug' => 'coastal-cyclone-disaster-mitigation'],
            [
                'trainer_id' => $trainer1->id,
                'department_id' => $mfDept?->id,
                'title' => 'Coastal Cyclone Disaster Mitigation & Emergency Shelter Management',
                'short_description' => 'Comprehensive masterclass on storm surge prediction, early warning protocol dissemination, and cyclone shelter operations.',
                'full_description' => "This enterprise capacity building certification equips emergency officers, district collectors, and coastal responders with end-to-end tactical expertise in managing high-intensity cyclonic events.\n\nLearners will study Doppler weather radar interpretations, track forecast cone errors, multi-stage color-coded bulletin protocols, and standardized shelter allocation algorithms.",
                'category' => 'Emergency Response',
                'difficulty' => 'Intermediate',
                'duration_hours' => 12,
                'thumbnail' => 'https://images.unsplash.com/photo-1527482797697-8795b05a13fe?auto=format&fit=crop&w=800&q=80',
                'learning_objectives' => [
                    'Interpret IMD Doppler Weather Radar and atmospheric pressure drops',
                    'Execute timely Phase-1 to Phase-4 evacuation directives',
                    'Administer multi-purpose cyclone shelters with water/power backup standards',
                    'Implement Incident Command System (ICS) communication protocols',
                ],
                'prerequisites' => ['Basic Disaster Management Awareness', 'Field Response Orientation'],
                'completion_criteria' => '100% lesson completion, minimum 70% MCQ assessment score, and passing grade in practical evacuation scenario.',
                'status' => 'published',
                'published_at' => now()->subMonths(2),
            ]
        );

        if ($cycloneComp) {
            $course1->competencies()->syncWithoutDetaching([$cycloneComp->id => ['target_level' => 4]]);
        }
        if ($warningComp) {
            $course1->competencies()->syncWithoutDetaching([$warningComp->id => ['target_level' => 4]]);
        }

        // Module 1
        $mod1 = CourseModule::updateOrCreate(
            ['course_id' => $course1->id, 'title' => 'Module 1: Cyclogenesis, Tracking & Early Warning Signals'],
            ['description' => 'Atmospheric triggers, tropical depression escalation, and bulletin interpretation.', 'sort_order' => 1]
        );

        $l1 = Lesson::updateOrCreate(
            ['module_id' => $mod1->id, 'title' => 'Understanding Storm Surge Dynamics and IMD Color Bulletins'],
            [
                'slug' => 'storm-surge-dynamics-and-bulletins',
                'content_type' => 'text',
                'text_content' => "### Storm Surge Dynamics and IMD Color Bulletins\n\nWhen a Severe Cyclonic Storm approaches the coast, the dangerous rise in sea level above astronomical tide is governed by central pressure deficits and bathymetric slope.\n\n#### The Standard 4-Stage Warning Protocol:\n1. **Pre-Cyclone Watch (72 Hours Prior)**: Issued when a depression forms in the sea, alerting state chief secretaries.\n2. **Cyclone Alert (48 Hours Prior)**: Target coast identified; port signals hoisted.\n3. **Cyclone Warning (24 Hours Prior)**: Expected landfall location, surge height, and wind damage forecast.\n4. **Post-Landfall Outlook (12 Hours Prior)**: Tracking weakening and interior heavy rain inundation.\n\nEmergency officers must synchronize VHF repeaters and inspect siren networks during the 48-hour alert window.",
                'duration_minutes' => 25,
                'sort_order' => 1,
                'is_free_preview' => true,
            ]
        );

        $l2 = Lesson::updateOrCreate(
            ['module_id' => $mod1->id, 'title' => 'Tactical Command for 24-Hour Zero-Casualty Evacuations'],
            [
                'slug' => 'tactical-command-zero-casualty-evacuations',
                'content_type' => 'text',
                'text_content' => "### Tactical Command for Rapid Population Evacuation\n\n#### Priority Triage Matrix:\n- **Zone A (0 - 5 km from shoreline)**: Mandatory evacuation 18 hours prior to landfall.\n- **Zone B (5 - 15 km low-lying estuarine zones)**: Relocation to upper-story reinforced structures.\n- **Vulnerable Groups**: Prioritize expectant mothers, elderly individuals, and differently-abled citizens with designated ambulance escorts.",
                'duration_minutes' => 30,
                'sort_order' => 2,
            ]
        );

        // Resource for Module 1
        Resource::updateOrCreate(
            ['course_id' => $course1->id, 'title' => 'National Cyclone Evacuation Standard Operating Procedure (PDF)'],
            [
                'module_id' => $mod1->id,
                'lesson_id' => $l1->id,
                'uploader_id' => $trainer1->id,
                'description' => 'Official government tactical checklist for district disaster management committees.',
                'file_path' => 'resources/cyclone_sop.pdf',
                'file_type' => 'pdf',
                'file_size' => 4200000,
                'is_private' => false,
            ]
        );

        // Module 2
        $mod2 = CourseModule::updateOrCreate(
            ['course_id' => $course1->id, 'title' => 'Module 2: Multipurpose Cyclone Shelter Operations & Lifeline Restoration'],
            ['description' => 'Managing shelter sanitation, food stockpiles, backup power, and immediate post-storm clearance.', 'sort_order' => 2]
        );

        $l3 = Lesson::updateOrCreate(
            ['module_id' => $mod2->id, 'title' => 'Cyclone Shelter Management & Anti-Epidemic Sanitation'],
            [
                'slug' => 'shelter-management-and-sanitation',
                'content_type' => 'text',
                'text_content' => "### Cyclone Shelter Management Guidelines\n\n1. Ensure minimum 3.5 sq. meters space per evacuee.\n2. Chlorinate all overhead drinking tanks at 2.0 ppm residual.\n3. Position solar-hybrid generator units elevated at least 2 meters above ground flood level.",
                'duration_minutes' => 20,
                'sort_order' => 1,
            ]
        );

        // Assessment for Course 1
        $assessment1 = Assessment::updateOrCreate(
            ['course_id' => $course1->id, 'title' => 'Certified Cyclone Commander Mastery Assessment'],
            [
                'module_id' => $mod2->id,
                'description' => 'Official assessment evaluating theoretical and tactical response competency.',
                'type' => 'mcq',
                'time_limit_minutes' => 15,
                'passing_percentage' => 70,
                'max_attempts' => 3,
                'negative_marks_per_question' => 0.25,
                'is_randomized' => true,
                'is_active' => true,
            ]
        );

        $q1 = Question::updateOrCreate(
            ['assessment_id' => $assessment1->id, 'question_text' => 'What is the standard lead time for issuing the Cyclone Warning (Stage 3)?'],
            [
                'explanation' => 'Stage 3 (Cyclone Warning) is issued at least 24 hours in advance of anticipated landfall.',
                'marks' => 2.0,
                'difficulty' => 'Medium',
                'competency_id' => $warningComp?->id,
                'sort_order' => 1,
            ]
        );

        QuestionOption::updateOrCreate(['question_id' => $q1->id, 'option_text' => '72 hours in advance'], ['is_correct' => false, 'sort_order' => 1]);
        QuestionOption::updateOrCreate(['question_id' => $q1->id, 'option_text' => '48 hours in advance'], ['is_correct' => false, 'sort_order' => 2]);
        QuestionOption::updateOrCreate(['question_id' => $q1->id, 'option_text' => '24 hours in advance'], ['is_correct' => true, 'sort_order' => 3]);
        QuestionOption::updateOrCreate(['question_id' => $q1->id, 'option_text' => '6 hours in advance'], ['is_correct' => false, 'sort_order' => 4]);

        $q2 = Question::updateOrCreate(
            ['assessment_id' => $assessment1->id, 'question_text' => 'Under Incident Command protocols, which action has priority when wind speeds exceed 90 km/h?'],
            [
                'explanation' => 'Once gale force winds exceed safety thresholds, all active outdoor vehicular evacuations must halt to prevent casualties.',
                'marks' => 2.0,
                'difficulty' => 'Medium',
                'competency_id' => $cycloneComp?->id,
                'sort_order' => 2,
            ]
        );

        QuestionOption::updateOrCreate(['question_id' => $q2->id, 'option_text' => 'Dispatch open-bed relief trucks along the coastal highway'], ['is_correct' => false, 'sort_order' => 1]);
        QuestionOption::updateOrCreate(['question_id' => $q2->id, 'option_text' => 'Halt outdoor vehicular movements and secure personnel inside reinforced shelters'], ['is_correct' => true, 'sort_order' => 2]);
        QuestionOption::updateOrCreate(['question_id' => $q2->id, 'option_text' => 'Deploy aerial helicopters for beachside surveillance'], ['is_correct' => false, 'sort_order' => 3]);
        QuestionOption::updateOrCreate(['question_id' => $q2->id, 'option_text' => 'Shut down all radio repeaters'], ['is_correct' => false, 'sort_order' => 4]);

        // Practical Assessment
        PracticalAssessment::updateOrCreate(
            ['course_id' => $course1->id, 'title' => 'Puri District Evacuation Tactical Action Plan'],
            [
                'competency_id' => $cycloneComp?->id,
                'scenario_prompt' => 'A Very Severe Cyclonic Storm (160 km/h winds, 3.5m surge) is forecasted to make landfall near Puri in 36 hours. Draft an operational plan detailing resource mobilization, shelter allocation, and vulnerable population triage.',
                'evaluation_criteria' => 'Clear ICS roles, realistic shelter logistics, backup power plan, anti-epidemic measures, and communication redundancy.',
                'max_score' => 100,
                'passing_score' => 70,
            ]
        );

        // COURSE 2: Advanced Swiftwater Rescue & Urban Flood Inundation Navigation
        $course2 = Course::updateOrCreate(
            ['slug' => 'swiftwater-rescue-flood-navigation'],
            [
                'trainer_id' => $trainer2->id,
                'department_id' => $ndrDept?->id,
                'title' => 'Advanced Swiftwater Rescue & Urban Flood Inundation Navigation',
                'short_description' => 'Tactical operational training for boat rescue crews, rapid current navigation, and submerged obstacle safety.',
                'full_description' => 'Designed for tactical rescue operators. Covers inflatable boat handling in Category 4 currents, mechanical advantage rope systems, and survivor extraction in submerged urban buildings.',
                'category' => 'Tactical Operations',
                'difficulty' => 'Advanced',
                'duration_hours' => 16,
                'thumbnail' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=800&q=80',
                'learning_objectives' => [
                    'Master motorized inflatable rescue boat (IRB) turns in 15-knot currents',
                    'Execute tensioned diagonal rope rescues across swollen river channels',
                    'Conduct night sonar navigation in submerged electrical hazards',
                ],
                'prerequisites' => ['Basic Water Rescue Certification', 'Physical Endurance Clearance'],
                'completion_criteria' => '100% lesson completion and passing final evaluation.',
                'status' => 'published',
                'published_at' => now()->subMonth(),
            ]
        );

        if ($floodComp) {
            $course2->competencies()->syncWithoutDetaching([$floodComp->id => ['target_level' => 4]]);
        }

        $c2Mod1 = CourseModule::updateOrCreate(
            ['course_id' => $course2->id, 'title' => 'Module 1: Swiftwater Hydrodynamics and Vessel Mechanics'],
            ['description' => 'Laminar vs turbulent water, eddy lines, and boat engine trim.', 'sort_order' => 1]
        );

        $c2l1 = Lesson::updateOrCreate(
            ['module_id' => $c2Mod1->id, 'title' => 'Reading River Currents and Hydrology Hazards'],
            [
                'slug' => 'reading-river-currents-hazards',
                'content_type' => 'text',
                'text_content' => "### Hydrology Hazards in Urban Floods\n\n1. **Strainers**: Fences or fallen trees that let water through while trapping boats.\n2. **Low-Head Dams (Drowning Machines)**: Dangerous hydraulic boil lines.\n3. **Debris Hazards**: Submerged vehicles and live power lines.",
                'duration_minutes' => 35,
                'sort_order' => 1,
            ]
        );

        // COURSE 3: Pending Course (for Admin approval demonstration)
        Course::updateOrCreate(
            ['slug' => 'strategic-emergency-warehousing-logistics'],
            [
                'trainer_id' => $trainer1->id,
                'department_id' => $elDept?->id,
                'title' => 'Strategic Emergency Warehousing & Cold-Chain Relief Logistics',
                'short_description' => 'Prepositioning medical supplies, high-calorie ration logistics, and bottleneck clearing during state calamities.',
                'full_description' => 'Draft course submitted for review. Covers strategic buffer warehousing, multimodal freight routing during bridge collapses, and pharmaceutical cold-chain resilience.',
                'category' => 'Logistics',
                'difficulty' => 'Intermediate',
                'duration_hours' => 8,
                'thumbnail' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80',
                'learning_objectives' => [
                    'Manage emergency pharmaceutical stockpiles',
                    'Optimize multimodal transport routes',
                ],
                'status' => 'pending', // Pending approval
            ]
        );

        // ENROLLMENTS & DEMO CERTIFICATES
        // Trainee 1 enrolled in Course 1 (completed 100% with certificate)
        $enroll1 = Enrollment::updateOrCreate(
            ['user_id' => $trainee1->id, 'course_id' => $course1->id],
            [
                'status' => 'completed',
                'progress_percentage' => 100,
                'last_accessed_lesson_id' => $l3->id,
                'enrolled_at' => now()->subDays(20),
                'completed_at' => now()->subDays(2),
                'last_accessed_at' => now()->subDays(2),
            ]
        );

        LessonProgress::updateOrCreate(['enrollment_id' => $enroll1->id, 'lesson_id' => $l1->id], ['is_completed' => true, 'completed_at' => now()->subDays(15)]);
        LessonProgress::updateOrCreate(['enrollment_id' => $enroll1->id, 'lesson_id' => $l2->id], ['is_completed' => true, 'completed_at' => now()->subDays(10)]);
        LessonProgress::updateOrCreate(['enrollment_id' => $enroll1->id, 'lesson_id' => $l3->id], ['is_completed' => true, 'completed_at' => now()->subDays(2)]);

        AssessmentAttempt::updateOrCreate(
            ['assessment_id' => $assessment1->id, 'user_id' => $trainee1->id, 'attempt_number' => 1],
            [
                'started_at' => now()->subDays(2)->subMinutes(12),
                'submitted_at' => now()->subDays(2),
                'score' => 4.0,
                'total_possible_marks' => 4.0,
                'percentage' => 100.0,
                'passed' => true,
            ]
        );

        // 1. Valid Trainee Certificate (Permanent / Lifetime Validity)
        Certificate::updateOrCreate(
            ['user_id' => $trainee1->id, 'course_id' => $course1->id],
            [
                'certificate_number' => 'CC-CERT-CYCLONE-2026-001',
                'verification_code' => 'a9f7e8b2c4d1e3f5a7b9c2d4e6f8a0b1c3d5e7f9a2b4c6d8e0f1a3b5c7d9e1f3',
                'issue_date' => now()->subDays(2)->toDateString(),
                'expiry_date' => null, // Lifetime Permanent Validity for Trainee
                'status' => 'valid',
            ]
        );

        // 2. Additional Valid Trainee Certificate (Permanent / Lifetime Validity)
        Certificate::updateOrCreate(
            ['user_id' => $trainee2->id, 'course_id' => $course2->id],
            [
                'certificate_number' => 'CC-CERT-SWIFTWATER-2026-042',
                'verification_code' => 'd5e6f7a8b9c0d1e2f3a4b5c6d7e8f9a0b1c2d3e4f5a6b7c8d9e0f1a2b3c4d5e6',
                'issue_date' => now()->subMonths(6)->toDateString(),
                'expiry_date' => null, // Lifetime Permanent Validity for Trainee
                'status' => 'valid',
            ]
        );

        // 3. Valid Trainer Master Certification (2-Year Validation Window)
        Certificate::updateOrCreate(
            ['user_id' => $trainer1->id, 'course_id' => $course1->id],
            [
                'certificate_number' => 'CC-TRN-MASTER-2026-101',
                'verification_code' => 'b1c2d3e4f5a6b7c8d9e0f1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6d7e8f9a0b1c2',
                'issue_date' => now()->subMonths(3)->toDateString(),
                'expiry_date' => now()->addMonths(21)->toDateString(),
                'status' => 'valid',
            ]
        );

        // 4. Expired Trainer Certification (Requires MCQ Assessment Renewal to regain instructional authority)
        Certificate::updateOrCreate(
            ['user_id' => $trainer2->id, 'course_id' => $course2->id],
            [
                'certificate_number' => 'CC-TRN-EXPIRED-2025-088',
                'verification_code' => 'c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2c3d4',
                'issue_date' => now()->subYears(2)->toDateString(),
                'expiry_date' => now()->subDays(45)->toDateString(),
                'status' => 'expired',
            ]
        );

        // Trainee 2 enrolled in Course 1 (in progress 66%)
        $enroll2 = Enrollment::updateOrCreate(
            ['user_id' => $trainee2->id, 'course_id' => $course1->id],
            [
                'status' => 'in_progress',
                'progress_percentage' => 66,
                'last_accessed_lesson_id' => $l2->id,
                'enrolled_at' => now()->subDays(10),
                'last_accessed_at' => now()->subHours(5),
            ]
        );

        LessonProgress::updateOrCreate(['enrollment_id' => $enroll2->id, 'lesson_id' => $l1->id], ['is_completed' => true, 'completed_at' => now()->subDays(8)]);
        LessonProgress::updateOrCreate(['enrollment_id' => $enroll2->id, 'lesson_id' => $l2->id], ['is_completed' => true, 'completed_at' => now()->subDays(3)]);
    }
}
