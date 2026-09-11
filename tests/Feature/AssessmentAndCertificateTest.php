<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\PracticalAssessment;
use App\Models\PracticalSubmission;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentAndCertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_trainee_can_take_mcq_assessment_and_get_scored(): void
    {
        $trainee = User::factory()->create(['role' => 'trainee', 'status' => 'active']);
        $trainer = User::factory()->create(['role' => 'trainer', 'status' => 'active']);

        $course = Course::create([
            'trainer_id' => $trainer->id,
            'title' => 'Cyclone Relief Management',
            'slug' => 'cyclone-relief-management',
            'short_description' => 'Course description',
            'full_description' => 'Full detailed course syllabus description',
            'category' => 'Disaster Management',
            'difficulty' => 'Intermediate',
            'duration_hours' => 10,
            'status' => 'published',
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'Cyclone Preparedness MCQ Test',
            'type' => 'mcq',
            'time_limit_minutes' => 30,
            'passing_percentage' => 50,
            'max_attempts' => 3,
            'is_active' => true,
        ]);

        Enrollment::create([
            'user_id' => $trainee->id,
            'course_id' => $course->id,
            'status' => 'in_progress',
            'progress_percentage' => 0,
        ]);

        $question = Question::create([
            'assessment_id' => $assessment->id,
            'question_text' => 'What is the primary action during level 3 cyclone warning?',
            'marks' => 10,
            'difficulty' => 'Medium',
        ]);

        $optCorrect = QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Evacuate coastal vulnerable zones to designated shelter',
            'is_correct' => true,
        ]);

        $optWrong = QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Ignore evacuation orders',
            'is_correct' => false,
        ]);

        // Submit assessment answers
        $response = $this->actingAs($trainee)->post(route('trainee.assessments.submit', $assessment->id), [
            'answers' => [
                $question->id => $optCorrect->id,
            ],
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('assessment_attempts', [
            'user_id' => $trainee->id,
            'assessment_id' => $assessment->id,
            'passed' => true,
            'percentage' => 100,
        ]);
    }

    public function test_admin_dashboard_renders_successfully(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Enterprise Capacity Command Dashboard');
    }

    public function test_certificate_verification_page_renders_with_sample_codes(): void
    {
        $response = $this->get(route('certificate.verify'));

        $response->assertStatus(200);
        $response->assertSee('Sample Certificate Codes for Instant Verification Testing');
        $response->assertSee('CC-CERT-CYCLONE-2026-001');
        $response->assertSee('CC-TRN-EXPIRED-2025-088');
    }

    public function test_certificate_requires_a_passing_practical_assessment(): void
    {
        $trainee = User::factory()->create(['role' => 'trainee', 'status' => 'active']);
        $trainer = User::factory()->create(['role' => 'trainer', 'status' => 'active']);
        $course = Course::create([
            'trainer_id' => $trainer->id,
            'title' => 'Practical Response Course',
            'slug' => 'practical-response-course',
            'short_description' => 'Course description',
            'full_description' => 'Course syllabus',
            'category' => 'Response',
            'difficulty' => 'Intermediate',
            'duration_hours' => 8,
            'status' => 'published',
        ]);
        $practical = PracticalAssessment::create([
            'course_id' => $course->id,
            'title' => 'Field Response',
            'scenario_prompt' => 'Respond to the scenario.',
            'evaluation_criteria' => 'Follow the response checklist.',
            'max_score' => 100,
            'passing_score' => 70,
        ]);
        Enrollment::create([
            'user_id' => $trainee->id,
            'course_id' => $course->id,
            'status' => 'completed',
            'progress_percentage' => 100,
        ]);

        $service = app(CertificateService::class);

        $this->assertNull($service->issueIfEligible($trainee, $course));

        PracticalSubmission::create([
            'practical_assessment_id' => $practical->id,
            'user_id' => $trainee->id,
            'submission_text' => 'A sufficiently detailed practical response for review.',
            'status' => 'passed',
            'score' => 85,
        ]);

        $certificate = $service->issueIfEligible($trainee, $course);

        $this->assertNotNull($certificate);
        $this->assertDatabaseHas('certificates', [
            'user_id' => $trainee->id,
            'course_id' => $course->id,
            'status' => 'valid',
        ]);
    }

    public function test_trainee_can_download_certificate_without_exposing_private_data_publicly(): void
    {
        $trainee = User::factory()->create(['role' => 'trainee', 'status' => 'active']);
        $trainer = User::factory()->create(['role' => 'trainer', 'status' => 'active']);
        $course = Course::create([
            'trainer_id' => $trainer->id,
            'title' => 'Verified Course',
            'slug' => 'verified-course',
            'short_description' => 'Course description',
            'full_description' => 'Course syllabus',
            'category' => 'Response',
            'difficulty' => 'Beginner',
            'duration_hours' => 4,
            'status' => 'published',
        ]);
        $certificate = Certificate::create([
            'user_id' => $trainee->id,
            'course_id' => $course->id,
            'certificate_number' => 'CC-DOWNLOAD-001',
            'verification_code' => str_repeat('a', 64),
            'issue_date' => now()->toDateString(),
            'status' => 'valid',
        ]);

        $download = $this->actingAs($trainee)->get(route('trainee.certificates.download', $certificate));

        $download->assertOk();
        $download->assertHeader('content-disposition', 'attachment; filename=certificate-CC-DOWNLOAD-001.html');

        $public = $this->get(route('certificate.verify', $certificate->verification_code));

        $public->assertOk();
        $public->assertSee('Personal details are withheld for privacy.');
        $public->assertDontSee($trainee->name);
    }

    public function test_trainer_can_pass_recertification_and_renew_expired_certificate(): void
    {
        $trainer = User::factory()->create(['role' => 'trainer', 'status' => 'active']);
        $course = Course::create([
            'trainer_id' => $trainer->id,
            'title' => 'Advanced Water Rescue',
            'slug' => 'advanced-water-rescue',
            'short_description' => 'Course description',
            'full_description' => 'Full syllabus',
            'category' => 'Rescue',
            'difficulty' => 'Advanced',
            'duration_hours' => 12,
            'status' => 'published',
        ]);

        $cert = Certificate::create([
            'user_id' => $trainer->id,
            'course_id' => $course->id,
            'certificate_number' => 'CC-TRN-TEST-999',
            'verification_code' => 'test-sha256-code',
            'issue_date' => now()->subYears(2)->toDateString(),
            'expiry_date' => now()->subDays(30)->toDateString(),
            'status' => 'expired',
        ]);

        // Visit recertification exam page
        $res = $this->get(route('trainer.recertify', 'CC-TRN-TEST-999'));
        $res->assertStatus(200);
        $res->assertSee('Instructor Competency Recertification');

        // Submit correct answers (all 5 correct)
        $submitRes = $this->post(route('trainer.recertify.submit', 'CC-TRN-TEST-999'), [
            'answers' => [
                1 => 'b',
                2 => 'b',
                3 => 'c',
                4 => 'b',
                5 => 'a',
            ],
        ]);

        $submitRes->assertStatus(200);
        $submitRes->assertSee('Trainer Certification Successfully Renewed!');

        $cert->refresh();
        $this->assertEquals('valid', $cert->status);
        $this->assertTrue($cert->expiry_date->isFuture());
    }

    public function test_unenrolled_trainee_cannot_attempt_an_assessment(): void
    {
        $trainee = User::factory()->create(['role' => 'trainee', 'status' => 'active']);
        $trainer = User::factory()->create(['role' => 'trainer', 'status' => 'active']);
        $course = Course::create([
            'trainer_id' => $trainer->id,
            'title' => 'Restricted Assessment Course',
            'slug' => 'restricted-assessment-course',
            'short_description' => 'Course description',
            'full_description' => 'Course syllabus',
            'category' => 'Response',
            'difficulty' => 'Beginner',
            'duration_hours' => 2,
            'status' => 'published',
        ]);
        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'Restricted Assessment',
            'passing_percentage' => 70,
            'max_attempts' => 3,
            'is_active' => true,
        ]);

        $this->actingAs($trainee)->get(route('trainee.assessments.take', $assessment))->assertForbidden();
        $this->actingAs($trainee)->post(route('trainee.assessments.submit', $assessment), [
            'answers' => [],
        ])->assertForbidden();
    }

    public function test_unenrolled_trainee_cannot_submit_a_practical_assessment(): void
    {
        $trainee = User::factory()->create(['role' => 'trainee', 'status' => 'active']);
        $trainer = User::factory()->create(['role' => 'trainer', 'status' => 'active']);
        $course = Course::create([
            'trainer_id' => $trainer->id,
            'title' => 'Restricted Practical Course',
            'slug' => 'restricted-practical-course',
            'short_description' => 'Course description',
            'full_description' => 'Course syllabus',
            'category' => 'Response',
            'difficulty' => 'Beginner',
            'duration_hours' => 2,
            'status' => 'published',
        ]);
        $practical = PracticalAssessment::create([
            'course_id' => $course->id,
            'title' => 'Restricted Practical',
            'scenario_prompt' => 'Respond to the scenario.',
            'evaluation_criteria' => 'Follow the checklist.',
        ]);

        $this->actingAs($trainee)->post(route('trainee.practical.submit', $practical), [
            'submission_text' => str_repeat('A practical response. ', 5),
        ])->assertForbidden();
    }
}
