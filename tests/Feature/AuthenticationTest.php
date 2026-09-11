<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'test@capacityconnect.gov',
            'password' => bcrypt('password123'),
            'role' => 'trainee',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@capacityconnect.gov',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('trainee.dashboard'));
    }

    public function test_users_can_register_as_trainee(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'johndoe@capacityconnect.gov',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'trainee',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'johndoe@capacityconnect.gov', 'role' => 'trainee']);
    }

    public function test_trainee_cannot_access_admin_portal(): void
    {
        $trainee = User::factory()->create(['role' => 'trainee', 'status' => 'active']);

        $response = $this->actingAs($trainee)->get('/admin/dashboard');
        $response->assertStatus(403);
    }
}
