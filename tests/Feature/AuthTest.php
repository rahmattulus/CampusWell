<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun');
        $response->assertSee('Alamat Email');
    }

    public function test_users_can_authenticate_with_email_and_password(): void
    {
        $user = User::factory()->create([
            'email' => 'student@test.com',
            'role' => 'student',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'student@test.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('student.home'));
    }

    public function test_doctor_is_redirected_to_doctor_dashboard(): void
    {
        $doctor = User::factory()->create([
            'email' => 'doctor@test.com',
            'role' => 'doctor',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'doctor@test.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('doctor.dashboard'));
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password123'),
        ]);

        $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
