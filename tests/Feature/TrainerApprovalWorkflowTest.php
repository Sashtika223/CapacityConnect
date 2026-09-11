<?php

namespace Tests\Feature;

use App\Mail\TrainerApproved;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TrainerApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();
        $this->department = Department::create([
            'name' => 'Emergency Disaster Management',
            'code' => 'EDM',
            'description' => 'Disaster management unit',
        ]);
    }

    public function test_trainer_registration_creates_pending_account_without_auto_login(): void
    {
        $response = $this->post('/register', [
            'name' => 'Dr. Aditi Sharma',
            'email' => 'aditi.sharma@capacityconnect.gov',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'role' => 'trainer',
            'department_id' => $this->department->id,
        ]);

        // Must redirect to login with awaiting approval notice
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Must NOT be authenticated
        $this->assertGuest();

        // Database must have trainer with pending status
        $this->assertDatabaseHas('users', [
            'email' => 'aditi.sharma@capacityconnect.gov',
            'role' => 'trainer',
            'status' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    public function test_trainee_registration_creates_active_account_and_logs_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'Rahul Verma',
            'email' => 'rahul.verma@capacityconnect.gov',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'role' => 'trainee',
            'department_id' => $this->department->id,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('trainee.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'rahul.verma@capacityconnect.gov',
            'role' => 'trainee',
            'status' => 'active',
        ]);
    }

    public function test_pending_trainer_cannot_log_in_and_receives_clear_error(): void
    {
        $trainer = User::factory()->trainer()->pending()->create([
            'email' => 'pending.trainer@capacityconnect.gov',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'pending.trainer@capacityconnect.gov',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_rejected_trainer_cannot_log_in(): void
    {
        $trainer = User::factory()->trainer()->rejected()->create([
            'email' => 'rejected.trainer@capacityconnect.gov',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'rejected.trainer@capacityconnect.gov',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_suspended_trainer_cannot_log_in(): void
    {
        $trainer = User::factory()->trainer()->suspended()->create([
            'email' => 'suspended.trainer@capacityconnect.gov',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'suspended.trainer@capacityconnect.gov',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_view_pending_trainers_list(): void
    {
        $admin = User::factory()->admin()->create();
        $pendingTrainer = User::factory()->trainer()->pending()->create([
            'name' => 'Trainer Under Review',
            'department_id' => $this->department->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/trainers');

        $response->assertStatus(200);
        $response->assertSee('Trainer Under Review');
        $response->assertSee('PENDING');
    }

    public function test_admin_can_view_trainer_detail_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $trainer = User::factory()->trainer()->pending()->create([
            'name' => 'Expert Trainer Profile',
            'department_id' => $this->department->id,
        ]);

        $response = $this->actingAs($admin)->get("/admin/trainers/{$trainer->id}");

        $response->assertStatus(200);
        $response->assertSee('Expert Trainer Profile');
        $response->assertSee('Approve Trainer');
        $response->assertSee('Reject Application');
    }

    public function test_non_admin_cannot_access_trainer_management_routes(): void
    {
        $trainee = User::factory()->trainee()->create();
        $trainer = User::factory()->trainer()->approved()->create();
        $pendingTrainer = User::factory()->trainer()->pending()->create();

        // Trainee access
        $this->actingAs($trainee)->get('/admin/trainers')->assertStatus(403);
        $this->actingAs($trainee)->post("/admin/trainers/{$pendingTrainer->id}/approve")->assertStatus(403);
        $this->actingAs($trainee)->post("/admin/trainers/{$pendingTrainer->id}/reject")->assertStatus(403);

        // Trainer access
        $this->actingAs($trainer)->get('/admin/trainers')->assertStatus(403);
        $this->actingAs($trainer)->post("/admin/trainers/{$pendingTrainer->id}/approve")->assertStatus(403);
        $this->actingAs($trainer)->post("/admin/trainers/{$pendingTrainer->id}/reject")->assertStatus(403);
    }

    public function test_admin_approving_trainer_updates_status_sends_email_and_logs_audit(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $trainer = User::factory()->trainer()->pending()->create([
            'email' => 'approve.me@capacityconnect.gov',
        ]);

        $response = $this->actingAs($admin)->post("/admin/trainers/{$trainer->id}/approve");

        $response->assertRedirect(route('admin.trainers.index'));
        $response->assertSessionHas('success');

        // Check database status
        $trainer->refresh();
        $this->assertEquals('approved', $trainer->status);
        $this->assertNotNull($trainer->approved_at);
        $this->assertEquals($admin->id, $trainer->approved_by);

        // Check email queued
        Mail::assertQueued(TrainerApproved::class, function ($mail) use ($trainer) {
            return $mail->hasTo('approve.me@capacityconnect.gov') &&
                   $mail->trainer->id === $trainer->id;
        });

        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'trainer_approved',
            'user_id' => $admin->id,
            'entity_type' => User::class,
            'entity_id' => $trainer->id,
        ]);
    }

    public function test_approved_trainer_can_log_in_and_access_trainer_dashboard(): void
    {
        $trainer = User::factory()->trainer()->approved()->create([
            'email' => 'active.trainer@capacityconnect.gov',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'active.trainer@capacityconnect.gov',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('trainer.dashboard'));

        // Access dashboard successfully
        $dashboardResponse = $this->actingAs($trainer)->get('/trainer/dashboard');
        $dashboardResponse->assertStatus(200);
    }

    public function test_admin_can_reject_trainer_with_reason_and_audit_log(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $trainer = User::factory()->trainer()->pending()->create([
            'email' => 'reject.me@capacityconnect.gov',
        ]);

        $response = $this->actingAs($admin)->post("/admin/trainers/{$trainer->id}/reject", [
            'rejection_reason' => 'Incomplete qualification documents',
        ]);

        $response->assertRedirect(route('admin.trainers.index'));

        $trainer->refresh();
        $this->assertEquals('rejected', $trainer->status);
        $this->assertNotNull($trainer->rejected_at);
        $this->assertEquals($admin->id, $trainer->rejected_by);
        $this->assertEquals('Incomplete qualification documents', $trainer->rejection_reason);

        // No approval email should be sent
        Mail::assertNothingSent();

        // Audit log recorded
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'trainer_rejected',
            'user_id' => $admin->id,
            'entity_type' => User::class,
            'entity_id' => $trainer->id,
        ]);
    }

    public function test_cannot_approve_already_approved_trainer(): void
    {
        $admin = User::factory()->admin()->create();
        $trainer = User::factory()->trainer()->approved()->create();

        $response = $this->actingAs($admin)->post("/admin/trainers/{$trainer->id}/approve");

        $response->assertSessionHas('error');
    }

    public function test_cannot_approve_non_trainer_user(): void
    {
        $admin = User::factory()->admin()->create();
        $trainee = User::factory()->trainee()->create();

        $response = $this->actingAs($admin)->post("/admin/trainers/{$trainee->id}/approve");

        $response->assertSessionHas('error');
    }
}
