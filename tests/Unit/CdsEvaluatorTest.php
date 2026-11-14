<?php

namespace Tests\Unit;

use App\Models\UserProfile;
use App\Services\CdsEvaluatorService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class CdsEvaluatorTest extends TestCase
{
    protected CdsEvaluatorService $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = new CdsEvaluatorService();
    }

    protected function normalDay(string $date = '2026-11-14'): array
    {
        return [
            'date' => $date,
            'sleep_hours' => 7.5,
            'meal_freq' => 3,
            'food_categories' => ['Netral'],
            'late_eating_hours' => 3,
            'stress_level' => 2,
            'complaints' => ['tidak_ada'],
            'freshness' => 4,
        ];
    }

    /** Skenario 1: Semua hari normal -> NORMAL */
    public function test_scenario_01_all_normal_days_returns_normal(): void
    {
        $today = $this->normalDay('2026-11-14');
        $history = [
            $this->normalDay('2026-11-13'),
            $this->normalDay('2026-11-12'),
            $this->normalDay('2026-11-11'),
        ];

        $result = $this->evaluator->evaluate($today, $history);

        $this->assertEquals(0, $result['fatigue_score']);
        $this->assertEquals(0, $result['gastric_score']);
        $this->assertEquals('NORMAL', $result['final_level']);
        $this->assertEmpty($result['alerts']);
    }

    /** Skenario 2: Hari ini tidur 5.5 jam, segar 3 -> PERHATIAN_MANDIRI */
    public function test_scenario_02_short_sleep_single_day_returns_perhatian_mandiri(): void
    {
        $today = array_merge($this->normalDay(), [
            'sleep_hours' => 5.5,
            'freshness' => 3,
        ]);
        $history = [
            $this->normalDay('2026-11-13'),
            $this->normalDay('2026-11-12'),
        ];

        $result = $this->evaluator->evaluate($today, $history);

        $this->assertEquals(2, $result['fatigue_score']);
        $this->assertEquals(0, $result['gastric_score']);
        $this->assertEquals('PERHATIAN_MANDIRI', $result['final_level']);
    }

    /** Skenario 3: Tidur 5 jam selama 3 hari terakhir, stres 3, segar 3 -> PERLU_PERHATIAN (Kelelahan) */
    public function test_scenario_03_sleep_streak_3_days_returns_perlu_perhatian(): void
    {
        $today = array_merge($this->normalDay('2026-11-14'), [
            'sleep_hours' => 5.0,
            'stress_level' => 3,
            'freshness' => 3,
        ]);
        $history = [
            array_merge($this->normalDay('2026-11-13'), ['sleep_hours' => 5.0]),
            array_merge($this->normalDay('2026-11-12'), ['sleep_hours' => 5.0]),
        ];

        $result = $this->evaluator->evaluate($today, $history);

        $this->assertEquals(4, $result['fatigue_score']);
        $this->assertEquals(1, $result['gastric_score']); // Dari stres 3 (M4)
        $this->assertEquals('PERLU_PERHATIAN', $result['final_level']);
        $this->assertEquals('PERLU_PERHATIAN', $result['fatigue_level']);
        $this->assertCount(1, $result['alerts']);
        $this->assertEquals('FATIGUE', $result['alerts'][0]['type']);
    }

    /** Skenario 4: Tidur 6.5 jam, stres 4, segar 2, chip lelah -> PERLU_PERHATIAN (Kelelahan) */
    public function test_scenario_04_sleep_mild_with_high_stress_and_both_subjective_fatigue(): void
    {
        $today = array_merge($this->normalDay(), [
            'sleep_hours' => 6.5,
            'stress_level' => 4,
            'freshness' => 2,
            'complaints' => ['lelah'],
        ]);

        $result = $this->evaluator->evaluate($today, []);

        // F1=1 (6.5j), F2=0, F3=1 (stres 4), F4=2 (segar 2 + chip lelah) -> Total F = 4
        $this->assertEquals(4, $result['fatigue_score']);
        $this->assertEquals(1, $result['gastric_score']); // stres 4 -> M4=1
        $this->assertEquals('PERLU_PERHATIAN', $result['final_level']);
        $this->assertEquals('PERLU_PERHATIAN', $result['fatigue_level']);
    }

    /** Skenario 5: Hari ini chip perih, makan 3x Netral -> PERHATIAN_MANDIRI (aturan M-K1) */
    public function test_scenario_05_perih_complaint_triggers_mk1_perhatian_mandiri(): void
    {
        $today = array_merge($this->normalDay(), [
            'complaints' => ['perih'],
            'meal_freq' => 3,
            'food_categories' => ['Netral'],
        ]);

        $result = $this->evaluator->evaluate($today, []);

        $this->assertEquals(0, $result['fatigue_score']);
        $this->assertEquals(2, $result['gastric_score']); // Dari M5=2
        $this->assertEquals('PERHATIAN_MANDIRI', $result['final_level']);
        $this->assertContains('M-K1', $result['rules_triggered']);
    }

    /** Skenario 6: Chip perih, makan 2x, Pedas, makan malam 3 jam -> PERLU_PERHATIAN (Maag) */
    public function test_scenario_06_gastric_risk_with_complaint_low_meals_spicy(): void
    {
        $today = array_merge($this->normalDay(), [
            'complaints' => ['perih'],
            'meal_freq' => 2,
            'food_categories' => ['Pedas'],
            'late_eating_hours' => 3,
        ]);

        $result = $this->evaluator->evaluate($today, []);

        // M1=2 (2x), M2=1 (Pedas), M3=0, M4=0, M5=2 (perih) -> Total M = 5
        $this->assertEquals(0, $result['fatigue_score']);
        $this->assertEquals(5, $result['gastric_score']);
        $this->assertEquals('PERLU_PERHATIAN', $result['final_level']);
        $this->assertEquals('PERLU_PERHATIAN', $result['gastric_level']);
        $this->assertEquals('GASTRIC', $result['alerts'][0]['type']);
    }

    /** Skenario 7: Chip perih pada 3 dari 5 hari terakhir, hari ini makan 3x Netral -> PERLU_PERHATIAN (M-K3) */
    public function test_scenario_07_gastric_complaint_3_of_5_days_triggers_mk3(): void
    {
        $today = array_merge($this->normalDay('2026-11-14'), [
            'complaints' => ['perih'],
        ]);
        $history = [
            array_merge($this->normalDay('2026-11-13'), ['complaints' => ['perih']]),
            array_merge($this->normalDay('2026-11-12'), ['complaints' => ['perih']]),
            $this->normalDay('2026-11-11'),
            $this->normalDay('2026-11-10'),
        ];

        $result = $this->evaluator->evaluate($today, $history);

        $this->assertEquals(2, $result['gastric_score']);
        $this->assertEquals('PERLU_PERHATIAN', $result['gastric_level']);
        $this->assertContains('M-K3', $result['rules_triggered']);
    }

    /** Skenario 8: Tidur selang-seling (non-beruntun), 4 hari < 6 jam -> PERHATIAN_MANDIRI (F2 non-beruntun) */
    public function test_scenario_08_non_consecutive_low_sleep_days(): void
    {
        // 4, 8, 4, 8, 4, 8, hari ini 5 jam
        $today = array_merge($this->normalDay('2026-11-14'), [
            'sleep_hours' => 5.0,
            'stress_level' => 3,
            'freshness' => 3,
        ]);
        $history = [
            array_merge($this->normalDay('2026-11-13'), ['sleep_hours' => 8.0]),
            array_merge($this->normalDay('2026-11-12'), ['sleep_hours' => 4.0]),
            array_merge($this->normalDay('2026-11-11'), ['sleep_hours' => 8.0]),
            array_merge($this->normalDay('2026-11-10'), ['sleep_hours' => 4.0]),
            array_merge($this->normalDay('2026-11-09'), ['sleep_hours' => 8.0]),
            array_merge($this->normalDay('2026-11-08'), ['sleep_hours' => 4.0]),
        ];

        $result = $this->evaluator->evaluate($today, $history);

        // F1=2 (5j), F2=1 (non-beruntun 4 hari <6j dalam seminggu), F3=0 (stres 3 < 4), F4=0 -> Total F = 3
        $this->assertEquals(3, $result['fatigue_score']);
        $this->assertEquals('PERHATIAN_MANDIRI', $result['fatigue_level']);
        $this->assertContains('F2-3', $result['rules_triggered']);
    }

    /** Skenario 9a: Hari ini makan 2x Netral, tanpa keluhan, tanpa profil berisiko -> NORMAL */
    public function test_scenario_09a_meal_2x_without_profile_booster_returns_normal(): void
    {
        $today = array_merge($this->normalDay(), [
            'meal_freq' => 2,
            'food_categories' => ['Netral'],
        ]);

        $result = $this->evaluator->evaluate($today, [], null);

        $this->assertEquals(2, $result['gastric_score']);
        $this->assertEquals('NORMAL', $result['final_level']);
    }

    /** Skenario 9b: Kasus 9a dengan P1 = Ya dan P3 = Rutin -> PERHATIAN_MANDIRI (penguat profil) */
    public function test_scenario_09b_meal_2x_with_profile_booster_boosts_to_perhatian_mandiri(): void
    {
        $today = array_merge($this->normalDay(), [
            'meal_freq' => 2,
            'food_categories' => ['Netral'],
        ]);

        $profile = new UserProfile([
            'p1_gastric_history' => 'Ya',
            'p2_smoking' => 'Tidak',
            'p3_nsaid_usage' => 'Rutin',
        ]);

        $result = $this->evaluator->evaluate($today, [], $profile);

        // Skor dasar = 2. Booster P1 (+1) + P3 (+1) = +2. Total M = 4.
        $this->assertEquals(4, $result['gastric_score']);
        $this->assertEquals('PERHATIAN_MANDIRI', $result['final_level']);
        $this->assertContains('M6-BOOSTER', $result['rules_triggered']);
    }

    /** Skenario 10: Semua hari normal, P1 = Ya dan P3 = Rutin -> NORMAL (booster tidak berlaku tanpa skor dasar) */
    public function test_scenario_10_profile_booster_not_applied_when_base_score_is_zero(): void
    {
        $today = $this->normalDay();
        $profile = new UserProfile([
            'p1_gastric_history' => 'Ya',
            'p2_smoking' => 'Tidak',
            'p3_nsaid_usage' => 'Rutin',
        ]);

        $result = $this->evaluator->evaluate($today, [], $profile);

        // Skor dasar = 0, jadi booster tidak diaktifkan. Total M = 0.
        $this->assertEquals(0, $result['gastric_score']);
        $this->assertEquals('NORMAL', $result['final_level']);
    }

    /** Skenario 11: Tidur 4j, makan 2x Pedas, makan malam 1j, stres 4, segar 1, chip lelah + perih -> F=8, M=8 */
    public function test_scenario_11_extreme_fatigue_and_gastric_risk(): void
    {
        $today = [
            'sleep_hours' => 4.0,
            'meal_freq' => 2,
            'food_categories' => ['Pedas'],
            'late_eating_hours' => 1,
            'stress_level' => 4,
            'complaints' => ['lelah', 'perih'],
            'freshness' => 1,
        ];
        $history = [
            array_merge($this->normalDay('2026-11-13'), ['sleep_hours' => 4.0, 'complaints' => ['lelah', 'perih']]),
            array_merge($this->normalDay('2026-11-12'), ['sleep_hours' => 4.0, 'complaints' => ['lelah', 'perih']]),
            array_merge($this->normalDay('2026-11-11'), ['sleep_hours' => 4.0, 'complaints' => ['lelah', 'perih']]),
            array_merge($this->normalDay('2026-11-10'), ['sleep_hours' => 4.0, 'complaints' => ['lelah', 'perih']]),
        ];

        $result = $this->evaluator->evaluate($today, $history);

        $this->assertEquals(8, $result['fatigue_score']);
        $this->assertEquals(8, $result['gastric_score']);
        $this->assertEquals('PERLU_PERHATIAN', $result['final_level']);
        $this->assertCount(2, $result['alerts']);
    }

    /** Skenario 12: Alert Kelelahan ditolak kemarin, skor sama hari ini -> ditekan (cooldown) */
    public function test_scenario_12_alert_dismissal_cooldown_suppresses_same_score(): void
    {
        $today = array_merge($this->normalDay(), [
            'sleep_hours' => 5.0,
            'stress_level' => 3,
            'freshness' => 3,
        ]);
        $history = [
            array_merge($this->normalDay('2026-11-13'), ['sleep_hours' => 5.0]),
            array_merge($this->normalDay('2026-11-12'), ['sleep_hours' => 5.0]),
        ];

        $dismissals = [
            [
                'alert_type' => 'FATIGUE',
                'score_at_dismissal' => 4,
                'dismissed_at' => Carbon::now()->subDay()->toDateTimeString(),
            ],
        ];

        $result = $this->evaluator->evaluate($today, $history, null, $dismissals);

        $this->assertEquals('PERLU_PERHATIAN', $result['fatigue_level']);
        $this->assertTrue($result['alerts'][0]['suppressed']);
        $this->assertTrue($result['cooldown_suppressed']);
    }

    /** Skenario 13: Alert ditolak kemarin tapi hari ini skor naik -> cooldown TIDAK menekan alert */
    public function test_scenario_13_alert_cooldown_does_not_suppress_if_score_increased(): void
    {
        $today = array_merge($this->normalDay(), [
            'sleep_hours' => 3.5, // F1 = 3
            'stress_level' => 4, // F3 = 1
            'freshness' => 2,
            'complaints' => ['lelah'], // F4 = 2
        ]);
        $history = [
            array_merge($this->normalDay('2026-11-13'), ['sleep_hours' => 5.0]),
            array_merge($this->normalDay('2026-11-12'), ['sleep_hours' => 5.0]),
        ];

        // Total F = 3 + 2 (streak 3) + 1 + 2 = 8
        $dismissals = [
            [
                'alert_type' => 'FATIGUE',
                'score_at_dismissal' => 4,
                'dismissed_at' => Carbon::now()->subDay()->toDateTimeString(),
            ],
        ];

        $result = $this->evaluator->evaluate($today, $history, null, $dismissals);

        $this->assertGreaterThan(4, $result['fatigue_score']);
        $this->assertFalse($result['alerts'][0]['suppressed']);
        $this->assertFalse($result['cooldown_suppressed']);
    }
}
