<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\PlatformNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_persist_notification_preferences_and_disable_email(): void
    {
        Notification::fake();
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)->put(route('notifications.preferences.update'), [
            'in_app_enabled' => '1',
            'email_enabled' => '0',
            'course_updates' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $user->id,
            'in_app_enabled' => true,
            'email_enabled' => false,
            'course_updates' => true,
        ]);

        app(NotificationService::class)->send($user, 'New Course', 'A course is available.', 'info', null, 'course_updates');

        $this->assertDatabaseHas('notifications_custom', [
            'user_id' => $user->id,
            'title' => 'New Course',
        ]);
        Notification::assertNothingSent();
    }

    public function test_announcement_is_persisted_and_fanned_out_to_its_audience(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $trainee = User::factory()->create(['role' => 'trainee', 'status' => 'active']);
        User::factory()->create(['role' => 'trainer', 'status' => 'active']);

        $response = $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Emergency Operations Update',
            'content' => 'Updated operational guidance is now available.',
            'target_role' => 'trainee',
            'is_pinned' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('announcements', [
            'user_id' => $admin->id,
            'title' => 'Emergency Operations Update',
            'is_pinned' => true,
        ]);
        $this->assertDatabaseHas('notifications_custom', [
            'user_id' => $trainee->id,
            'title' => 'New Announcement',
        ]);
        Notification::assertSentTo($trainee, PlatformNotification::class);
    }

    public function test_trainee_ai_assistant_accepts_commands_and_persists_the_exchange(): void
    {
        $trainee = User::factory()->create(['role' => 'trainee', 'status' => 'active']);

        $response = $this->actingAs($trainee)->postJson(route('trainee.ai.chat'), [
            'message' => '/help',
        ]);

        $response->assertOk();
        $response->assertJsonPath('answer', fn (string $answer): bool => str_contains($answer, '/courses'));
        $this->assertDatabaseHas('ai_conversations', ['user_id' => $trainee->id]);
        $this->assertDatabaseHas('ai_messages', ['role' => 'user', 'content' => '/help']);
        $this->assertDatabaseHas('ai_messages', ['role' => 'assistant']);
    }
}
