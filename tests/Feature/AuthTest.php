<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
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

    public function test_staff_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login/staff');

        $response->assertStatus(200);
        $response->assertSee('Masuk Staf Klinik');
    }

    public function test_verified_student_can_login_and_redirects_to_home(): void
    {
        $student = User::factory()->create([
            'email' => 'student@test.com',
            'role' => 'student',
            'verification_status' => 'verified',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'student@test.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('student.home'));
    }

    public function test_pending_student_is_redirected_to_verification_notice(): void
    {
        $student = User::factory()->create([
            'email' => 'pending@test.com',
            'role' => 'student',
            'verification_status' => 'pending',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'pending@test.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice'));
    }

    public function test_doctor_is_redirected_to_doctor_dashboard(): void
    {
        $doctor = User::factory()->create([
            'email' => 'doctor@test.com',
            'role' => 'doctor',
            'verification_status' => 'verified',
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
            'verification_status' => 'verified',
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

    public function test_student_can_register_with_two_step_profile_and_consent(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $postData = [
            'name' => 'Budi Pratama',
            'nim' => '2201011099',
            'email' => 'budi.pratama@student.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'p1_gastric_history' => 'Ya',
            'p2_smoking' => 'Tidak',
            'p3_nsaid_usage' => 'Kadang',
            'p4_chronic_conditions' => 'Alergi dingin',
            'consent' => '1',
        ];

        $postResponse = $this->post('/register', $postData);

        $this->assertAuthenticated();
        $postResponse->assertRedirect(route('verification.notice'));

        // Check user created with pending status
        $this->assertDatabaseHas('users', [
            'email' => 'budi.pratama@student.ac.id',
            'nim' => '2201011099',
            'role' => 'student',
            'verification_status' => 'pending',
        ]);

        // Check profile created
        $user = User::where('email', 'budi.pratama@student.ac.id')->first();
        $this->assertNotNull($user->profile);
        $this->assertEquals('Ya', $user->profile->p1_gastric_history);
        $this->assertEquals('Tidak', $user->profile->p2_smoking);
        $this->assertEquals('Kadang', $user->profile->p3_nsaid_usage);
        $this->assertNotNull($user->profile->consent_at);
    }

    public function test_rbac_middleware_restricts_unauthorized_roles(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'verification_status' => 'verified',
        ]);

        // Student accessing doctor dashboard should be forbidden
        $response = $this->actingAs($student)->get('/doctor/dashboard');
        $response->assertStatus(403);

        // Student accessing admin dashboard should be forbidden
        $response = $this->actingAs($student)->get('/admin/dashboard');
        $response->assertStatus(403);

        $doctor = User::factory()->create([
            'role' => 'doctor',
            'verification_status' => 'verified',
        ]);

        // Doctor accessing student home should be forbidden
        $response = $this->actingAs($doctor)->get('/student/home');
        $response->assertStatus(403);
    }

    public function test_pending_student_cannot_access_student_home(): void
    {
        $pendingStudent = User::factory()->create([
            'role' => 'student',
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($pendingStudent)->get('/student/home');
        $response->assertRedirect(route('verification.notice'));
    }
}
