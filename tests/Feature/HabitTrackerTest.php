<?php

namespace Tests\Feature;

use App\Models\AlertDismissal;
use App\Models\AuditLog;
use App\Models\CdsEvaluation;
use App\Models\HabitLog;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitTrackerTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create([
            'role' => 'student',
            'verification_status' => 'verified',
            'email' => 'student.test@campuswell.ac.id',
        ]);

        UserProfile::create([
            'user_id' => $this->student->id,
            'p1_gastric_history' => 'Tidak',
            'p2_smoking' => 'Tidak',
            'p3_nsaid_usage' => 'Tidak',
            'consent_at' => Carbon::now(),
        ]);
    }

    /** HAB-01: Beranda Mahasiswa dapat diakses */
    public function test_student_can_view_home_dashboard(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.home'));

        $response->assertStatus(200);
        $response->assertSee('Kesehatanmu hari ini');
        $response->assertSee('Log Kebiasaan Hari Ini');
    }

    /** HAB-03 & CDS-01: Menyimpan log habit harian yang normal */
    public function test_student_can_store_normal_habit_log(): void
    {
        $payload = [
            'sleep_hours' => 7.5,
            'meal_freq' => 3,
            'food_categories' => ['Netral'],
            'late_eating_hours' => 3,
            'stress_level' => 2,
            'complaints' => ['tidak_ada'],
            'freshness' => 4,
        ];

        $response = $this->actingAs($this->student)->postJson(route('student.habit-logs.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'evaluation' => [
                'final_level' => 'NORMAL',
                'fatigue_score' => 0,
                'gastric_score' => 0,
            ],
        ]);

        $this->assertDatabaseHas('habit_logs', [
            'user_id' => $this->student->id,
            'date' => Carbon::today()->toDateString(),
            'sleep_hours' => 7.5,
        ]);

        $this->assertDatabaseHas('cds_evaluations', [
            'user_id' => $this->student->id,
            'final_level' => 'NORMAL',
        ]);
    }

    /** CDS-04: Menyimpan log habit berisiko mencatat log audit */
    public function test_storing_risk_habit_log_creates_audit_log_entry(): void
    {
        // Kondisi ekstrem memicu PERLU_PERHATIAN (Kelelahan + Maag)
        $payload = [
            'sleep_hours' => 3.5,
            'meal_freq' => 1,
            'food_categories' => ['Pedas', 'High-Caffeine'],
            'late_eating_hours' => 1,
            'stress_level' => 4,
            'complaints' => ['lelah', 'perih'],
            'freshness' => 1,
        ];

        $response = $this->actingAs($this->student)->postJson(route('student.habit-logs.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'evaluation' => [
                'final_level' => 'PERLU_PERHATIAN',
            ],
        ]);

        // Verifikasi tabel cds_evaluations
        $this->assertDatabaseHas('cds_evaluations', [
            'user_id' => $this->student->id,
            'final_level' => 'PERLU_PERHATIAN',
        ]);

        // Verifikasi tabel audit_logs (CDS-04)
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->student->id,
            'action' => 'CDS_EVALUATION_RISK_TRIGGERED',
            'object_type' => 'cds_evaluations',
        ]);
    }

    /** HAB-06: Mengubah log di hari berjalan memperbarui data tanpa duplikasi */
    public function test_updating_log_on_same_day_updates_existing_record(): void
    {
        $payload1 = [
            'sleep_hours' => 7.0,
            'meal_freq' => 3,
            'food_categories' => ['Netral'],
            'late_eating_hours' => 3,
            'stress_level' => 2,
            'complaints' => ['tidak_ada'],
            'freshness' => 4,
        ];

        $this->actingAs($this->student)->postJson(route('student.habit-logs.store'), $payload1);
        $this->assertEquals(1, HabitLog::where('user_id', $this->student->id)->count());

        // Update nilai tidur menjadi 8 jam
        $payload2 = array_merge($payload1, ['sleep_hours' => 8.0]);
        $this->actingAs($this->student)->postJson(route('student.habit-logs.store'), $payload2);

        $this->assertEquals(1, HabitLog::where('user_id', $this->student->id)->count());
        $this->assertDatabaseHas('habit_logs', [
            'user_id' => $this->student->id,
            'sleep_hours' => 8.0,
        ]);
    }

    /** Cooldown / Dismissal: Mahasiswa dapat menolak alert dan tercatat */
    public function test_student_can_dismiss_alert_for_cooldown(): void
    {
        $payload = [
            'alert_type' => 'FATIGUE',
            'score' => 4,
        ];

        $response = $this->actingAs($this->student)->postJson(route('student.alerts.dismiss'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('alert_dismissals', [
            'user_id' => $this->student->id,
            'alert_type' => 'FATIGUE',
            'score_at_dismissal' => 4,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->student->id,
            'action' => 'ALERT_DISMISSED_BY_STUDENT',
        ]);
    }
}
