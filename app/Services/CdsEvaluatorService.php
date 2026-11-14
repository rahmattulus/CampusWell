<?php

namespace App\Services;

use App\Models\UserProfile;
use Carbon\Carbon;

class CdsEvaluatorService
{
    protected array $config;

    public function __construct(?array $config = null)
    {
        if ($config !== null) {
            $this->config = $config;
        } else {
            $configPath = function_exists('config_path') && app()->bound('path.config')
                ? config_path('cds_config.json')
                : dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'cds_config.json';

            if (file_exists($configPath)) {
                $this->config = json_decode(file_get_contents($configPath), true) ?? [];
            } else {
                $this->config = [];
            }
        }
    }

    /**
     * Mengevaluasi data habit harian dan riwayat untuk menghasilkan penilaian risiko CDS (RBR v2.1).
     *
     * @param array $todayLog Data log hari ini (D1 - D7)
     * @param array $historyLogs7Days Array log 7 hari terakhir (kemarin ke belakang atau chronological)
     * @param UserProfile|null $profile Profil faktor risiko pengguna (P1 - P3)
     * @param array $dismissals Daftar dismissal alert terkini pengguna (opsional)
     * @return array Hasil evaluasi CDS
     */
    public function evaluate(
        array $todayLog,
        array $historyLogs7Days = [],
        ?UserProfile $profile = null,
        array $dismissals = []
    ): array {
        $todaySleep = (float) ($todayLog['sleep_hours'] ?? 7.0);
        $todayMealFreq = (int) ($todayLog['meal_freq'] ?? 3);
        $todayFoodCategories = (array) ($todayLog['food_categories'] ?? ['Netral']);
        $todayLateEating = isset($todayLog['late_eating_hours']) && $todayLog['late_eating_hours'] !== null
            ? (int) $todayLog['late_eating_hours']
            : null;
        $todayStress = (int) ($todayLog['stress_level'] ?? 2);
        $todayComplaints = (array) ($todayLog['complaints'] ?? ['tidak_ada']);
        $todayFreshness = (int) ($todayLog['freshness'] ?? 4);

        // 1. Hitung Fitur Turunan
        $derived = $this->calculateDerivedFeatures($todayLog, $historyLogs7Days);
        $sleepStreak = $derived['sleep_streak'];
        $lowSleepDays7 = $derived['low_sleep_days_7'];
        $gastricComplaintDays5 = $derived['gastric_complaint_days_5'];
        $gastricHitDays5 = $derived['gastric_hit_days_5'];

        // 2. Evaluasi Kelompok F (Kelelahan)
        $fatigueResult = $this->evaluateFatigue(
            $todaySleep,
            $sleepStreak,
            $lowSleepDays7,
            $todayStress,
            $todayFreshness,
            $todayComplaints
        );

        // 3. Evaluasi Kelompok M (Lambung)
        $gastricResult = $this->evaluateGastric(
            $todayMealFreq,
            $todayFoodCategories,
            $todayLateEating,
            $todayStress,
            $todayComplaints,
            $profile,
            $gastricComplaintDays5,
            $gastricHitDays5,
            $derived['filled_days_5']
        );

        // 4. Tentukan Level Akhir RBR
        $levelsOrder = ['NORMAL' => 1, 'PERHATIAN_MANDIRI' => 2, 'PERLU_PERHATIAN' => 3];
        $fLevelVal = $levelsOrder[$fatigueResult['level']] ?? 1;
        $mLevelVal = $levelsOrder[$gastricResult['level']] ?? 1;

        $finalLevel = ($fLevelVal >= $mLevelVal) ? $fatigueResult['level'] : $gastricResult['level'];

        // 5. Susun Alert dan Rules Triggered
        $alerts = [];
        $rulesTriggered = array_merge($fatigueResult['rules'], $gastricResult['rules']);
        $allReasons = array_merge($fatigueResult['reasons'], $gastricResult['reasons']);

        // Check Cooldown untuk tiap alert berisiko
        $cooldownSuppressed = false;
        $hasActiveRiskAlert = false;

        if ($fatigueResult['level'] === 'PERLU_PERHATIAN') {
            $isSuppressed = $this->isAlertSuppressed('FATIGUE', $fatigueResult['score'], $dismissals);
            if ($isSuppressed) {
                $cooldownSuppressed = true;
            } else {
                $hasActiveRiskAlert = true;
            }

            $alerts[] = [
                'type' => 'FATIGUE',
                'title' => 'Risiko Kelelahan Ekstrem & Burnout',
                'level' => 'PERLU_PERHATIAN',
                'score' => $fatigueResult['score'],
                'rule_ids' => $fatigueResult['rules'],
                'reasons' => $fatigueResult['reasons'],
                'suppressed' => $isSuppressed,
            ];
        }

        if ($gastricResult['level'] === 'PERLU_PERHATIAN') {
            $isSuppressed = $this->isAlertSuppressed('GASTRIC', $gastricResult['score'], $dismissals);
            if ($isSuppressed) {
                $cooldownSuppressed = true;
            } else {
                $hasActiveRiskAlert = true;
            }

            $alerts[] = [
                'type' => 'GASTRIC',
                'title' => 'Risiko Gangguan Asam Lambung / Maag',
                'level' => 'PERLU_PERHATIAN',
                'score' => $gastricResult['score'],
                'rule_ids' => $gastricResult['rules'],
                'reasons' => $gastricResult['reasons'],
                'suppressed' => $isSuppressed,
            ];
        }

        return [
            'final_level' => $finalLevel,
            'fatigue_score' => $fatigueResult['score'],
            'fatigue_level' => $fatigueResult['level'],
            'gastric_score' => $gastricResult['score'],
            'gastric_level' => $gastricResult['level'],
            'rules_triggered' => array_values(array_unique($rulesTriggered)),
            'alerts' => $alerts,
            'reasons' => $allReasons,
            'cbr' => [
                'enabled' => false,
                'result' => null,
                'top_similarity' => null,
            ],
            'rules_version' => $this->config['rules_version'] ?? '2.1.0',
            'cooldown_suppressed' => $cooldownSuppressed && !$hasActiveRiskAlert,
            'derived_features' => [
                'sleep_streak' => $sleepStreak,
                'low_sleep_days_7' => $lowSleepDays7,
                'gastric_complaint_days_5' => $gastricComplaintDays5,
                'gastric_hit_days_5' => $gastricHitDays5,
            ],
        ];
    }

    /**
     * Hitung Skor dan Level Kelompok F (Kelelahan).
     */
    protected function evaluateFatigue(
        float $sleepHours,
        int $sleepStreak,
        int $lowSleepDays7,
        int $stressLevel,
        int $freshness,
        array $complaints
    ): array {
        $score = 0;
        $rules = [];
        $reasons = [];

        // F1: Durasi tidur hari ini
        if ($sleepHours < 4.5) {
            $score += 3;
            $rules[] = 'F1-1';
            $reasons[] = 'Durasi tidur sangat kurang (< 4.5 jam)';
        } elseif ($sleepHours < 6.0) {
            $score += 2;
            $rules[] = 'F1-2';
            $reasons[] = 'Durasi tidur kurang (4.5 - < 6 jam)';
        } elseif ($sleepHours < 7.0) {
            $score += 1;
            $rules[] = 'F1-3';
        }

        // F2: Kurang tidur beruntun vs non-beruntun
        if ($sleepStreak >= 3) {
            $score += 2;
            $rules[] = 'F2-1';
            $reasons[] = "Tidur kurang dari 6 jam selama {$sleepStreak} hari berturut-turut";
        } elseif ($sleepStreak === 2) {
            $score += 1;
            $rules[] = 'F2-2';
            $reasons[] = 'Tidur kurang dari 6 jam selama 2 hari berturut-turut';
        } elseif ($sleepStreak < 2 && $lowSleepDays7 >= 3) {
            $score += 1;
            $rules[] = 'F2-3';
            $reasons[] = "Pola tidur kurang sering terjadi ({$lowSleepDays7} hari dalam seminggu terakhir)";
        }

        // F3: Tingkat Stres
        if ($stressLevel >= 4) {
            $score += 1;
            $rules[] = 'F3';
            $reasons[] = 'Tingkat stres tinggi/berat (skala ' . $stressLevel . '/5)';
        }

        // F4: Kelelahan Subjektif (D7 dan chip lelah/konsentrasi)
        $isFreshLow = ($freshness <= 2);
        $hasFatigueComplaint = in_array('lelah', $complaints) || in_array('konsentrasi', $complaints);

        if ($isFreshLow && $hasFatigueComplaint) {
            $score += 2;
            $rules[] = 'F4-BOTH';
            $reasons[] = 'Tingkat kesegaran bangun rendah disertai keluhan lelah atau sulit konsentrasi';
        } elseif ($isFreshLow || $hasFatigueComplaint) {
            $score += 1;
            $rules[] = 'F4-ONE';
            if ($isFreshLow) {
                $reasons[] = 'Tingkat kesegaran bangun rendah (skala ' . $freshness . '/5)';
            } else {
                $reasons[] = 'Terdapat keluhan lelah fisik atau penurunan konsentrasi';
            }
        }

        // Level Keputusan F
        if ($score >= 4) {
            $level = 'PERLU_PERHATIAN';
            $rules[] = 'F-01';
        } elseif ($score >= 2) {
            $level = 'PERHATIAN_MANDIRI';
            $rules[] = 'F-02';
        } else {
            $level = 'NORMAL';
        }

        return [
            'score' => $score,
            'level' => $level,
            'rules' => $rules,
            'reasons' => $reasons,
        ];
    }

    /**
     * Hitung Skor dan Level Kelompok M (Lambung).
     */
    protected function evaluateGastric(
        int $mealFreq,
        array $foodCategories,
        ?int $lateEatingHours,
        int $stressLevel,
        array $complaints,
        ?UserProfile $profile,
        int $gastricComplaintDays5,
        int $gastricHitDays5,
        int $filledDays5
    ): array {
        $baseScore = 0;
        $rules = [];
        $reasons = [];

        // M1: Frekuensi makan
        if ($mealFreq <= 1) {
            $baseScore += 3;
            $rules[] = 'M1-1';
            $reasons[] = 'Frekuensi makan sangat rendah (≤ 1 kali sehari)';
        } elseif ($mealFreq === 2) {
            $baseScore += 2;
            $rules[] = 'M1-2';
            $reasons[] = 'Frekuensi makan kurang (hanya 2 kali sehari)';
        }

        // M2: Pemicu makanan
        $triggers = ['Pedas', 'Asam', 'High-Caffeine'];
        $triggerMatches = array_intersect($triggers, $foodCategories);
        $triggerPoints = min(count($triggerMatches), 2);
        if ($triggerPoints > 0) {
            $baseScore += $triggerPoints;
            $rules[] = 'M2';
            $reasons[] = 'Konsumsi makanan/minuman pemicu lambung (' . implode(', ', $triggerMatches) . ')';
        }

        // M3: Makan dekat tidur
        if ($lateEatingHours !== null && $lateEatingHours < 2) {
            $baseScore += 2;
            $rules[] = 'M3';
            $reasons[] = "Jarak makan malam ke waktu tidur terlalu dekat ({$lateEatingHours} jam)";
        }

        // M4: Stres pemicu lambung
        if ($stressLevel >= 3) {
            $baseScore += 1;
            $rules[] = 'M4';
            $reasons[] = 'Tingkat stres cukup menekan (dapat memicu produksi asam lambung)';
        }

        // M5: Keluhan lambung hari ini
        $hasSpecific = in_array('perih', $complaints) || in_array('asam_naik', $complaints);
        $hasNonspecific = in_array('mual', $complaints) || in_array('kembung', $complaints);

        if ($hasSpecific) {
            $baseScore += 2;
            $rules[] = 'M5-SPECIFIC';
            $reasons[] = 'Keluhan spesifik lambung (perih ulu hati / sensasi asam lambung naik)';
        } elseif ($hasNonspecific) {
            $baseScore += 1;
            $rules[] = 'M5-NONSPECIFIC';
            $reasons[] = 'Keluhan perut tidak nyaman (mual / kembung)';
        }

        // M6: Penguat profil faktor risiko (HANYA jika baseScore >= 1)
        $m6Score = 0;
        if ($baseScore >= 1 && $profile) {
            if ($profile->p1_gastric_history === 'Ya') {
                $m6Score += 1;
            }
            if ($profile->p2_smoking === 'Rutin') {
                $m6Score += 1;
            }
            if ($profile->p3_nsaid_usage === 'Rutin') {
                $m6Score += 1;
            }
            $m6Score = min($m6Score, 2);
            if ($m6Score > 0) {
                $rules[] = 'M6-BOOSTER';
                $reasons[] = 'Faktor risiko riwayat maag / kebiasaan memperberat kondisi lambung';
            }
        }

        $totalScore = $baseScore + $m6Score;

        // Level dasar dari skor M
        if ($totalScore >= 5) {
            $level = 'PERLU_PERHATIAN';
            $rules[] = 'M-01';
        } elseif ($totalScore >= 3) {
            $level = 'PERHATIAN_MANDIRI';
            $rules[] = 'M-02';
        } else {
            $level = 'NORMAL';
        }

        // Aturan Khusus Keluhan dan Pola:
        // M-K1: IF chip perih atau asam_naik hari ini -> level lambung minimal PERHATIAN_MANDIRI
        if ($hasSpecific) {
            if ($level === 'NORMAL') {
                $level = 'PERHATIAN_MANDIRI';
                $rules[] = 'M-K1';
            }
        }

        // M-K2: IF GASTRIC_COMPLAINT_DAYS_5 >= 2 AND Skor M hari ini >= 3 -> PERLU_PERHATIAN
        if ($gastricComplaintDays5 >= 2 && $totalScore >= 3) {
            $level = 'PERLU_PERHATIAN';
            $rules[] = 'M-K2';
            $reasons[] = 'Keluhan lambung berulang disertai pemicu';
        }

        // M-K3: IF GASTRIC_COMPLAINT_DAYS_5 >= 3 -> PERLU_PERHATIAN
        if ($gastricComplaintDays5 >= 3) {
            $level = 'PERLU_PERHATIAN';
            $rules[] = 'M-K3';
            $reasons[] = 'Keluhan lambung berulang dalam 5 hari terakhir';
        }

        // M-P: IF GASTRIC_HIT_DAYS_5 >= 3 (dan hari terisi >= 3) -> PERLU_PERHATIAN
        if ($gastricHitDays5 >= 3 && $filledDays5 >= 3) {
            $level = 'PERLU_PERHATIAN';
            $rules[] = 'M-P';
            $reasons[] = 'Pola pemicu lambung berulang dalam beberapa hari terakhir';
        }

        return [
            'base_score' => $baseScore,
            'score' => $totalScore,
            'level' => $level,
            'rules' => $rules,
            'reasons' => $reasons,
        ];
    }

    /**
     * Hitung Fitur Turunan dari Data Hari Ini + Riwayat.
     */
    public function calculateDerivedFeatures(array $todayLog, array $historyLogs7Days = []): array
    {
        $todaySleep = (float) ($todayLog['sleep_hours'] ?? 7.0);
        $todayComplaints = (array) ($todayLog['complaints'] ?? []);
        $todayHasGastricComplaint = in_array('perih', $todayComplaints) || in_array('asam_naik', $todayComplaints);

        // Urutkan riwayat berdasarkan tanggal descending (terbaru / kemarin dahulu)
        $sortedHistory = $historyLogs7Days;
        usort($sortedHistory, function ($a, $b) {
            $dateA = is_object($a) ? $a->date : ($a['date'] ?? null);
            $dateB = is_object($b) ? $b->date : ($b['date'] ?? null);
            return strcmp($dateB, $dateA);
        });

        // 1. SLEEP STREAK: Hari beruntun tidur < 6 jam sampai hari ini
        $sleepStreak = 0;
        if ($todaySleep < 6.0) {
            $sleepStreak = 1;
            foreach ($sortedHistory as $past) {
                $hours = is_object($past) ? (float) $past->sleep_hours : (float) ($past['sleep_hours'] ?? 7.0);
                if ($hours < 6.0) {
                    $sleepStreak++;
                } else {
                    break;
                }
            }
        }

        // 2. LOW_SLEEP_DAYS_7: Jumlah hari tidur < 6 jam dalam jendela 7 hari (termasuk hari ini)
        $lowSleepDays7 = ($todaySleep < 6.0) ? 1 : 0;
        $historySlice6 = array_slice($sortedHistory, 0, 6);
        foreach ($historySlice6 as $past) {
            $hours = is_object($past) ? (float) $past->sleep_hours : (float) ($past['sleep_hours'] ?? 7.0);
            if ($hours < 6.0) {
                $lowSleepDays7++;
            }
        }

        // 3. Jendela 5 Hari untuk Pola Lambung (Hari ini + maks 4 hari riwayat terakhir)
        $historySlice4 = array_slice($sortedHistory, 0, 4);
        $filledDays5 = 1 + count($historySlice4);

        // GASTRIC_COMPLAINT_DAYS_5: Hari dengan chip 'perih' atau 'asam_naik'
        $gastricComplaintDays5 = $todayHasGastricComplaint ? 1 : 0;
        foreach ($historySlice4 as $past) {
            $pastComplaints = is_object($past)
                ? (array) $past->complaints
                : (array) ($past['complaints'] ?? []);
            if (in_array('perih', $pastComplaints) || in_array('asam_naik', $pastComplaints)) {
                $gastricComplaintDays5++;
            }
        }

        // GASTRIC_HIT_DAYS_5: Hari dengan skor lambung >= 3 dalam 5 hari
        // Hitung skor lambung parsial hari ini (tanpa pola)
        $todayMealFreq = (int) ($todayLog['meal_freq'] ?? 3);
        $todayFoodCategories = (array) ($todayLog['food_categories'] ?? ['Netral']);
        $todayLateEating = isset($todayLog['late_eating_hours']) && $todayLog['late_eating_hours'] !== null
            ? (int) $todayLog['late_eating_hours']
            : null;
        $todayStress = (int) ($todayLog['stress_level'] ?? 2);

        $todayMScore = $this->calculateBasicGastricScore(
            $todayMealFreq,
            $todayFoodCategories,
            $todayLateEating,
            $todayStress,
            $todayComplaints
        );

        $gastricHitDays5 = ($todayMScore >= 3) ? 1 : 0;
        foreach ($historySlice4 as $past) {
            $pastMealFreq = is_object($past) ? (int) $past->meal_freq : (int) ($past['meal_freq'] ?? 3);
            $pastFoods = is_object($past) ? (array) $past->food_categories : (array) ($past['food_categories'] ?? ['Netral']);
            $pastLate = is_object($past) ? $past->late_eating_hours : ($past['late_eating_hours'] ?? null);
            $pastStress = is_object($past) ? (int) $past->stress_level : (int) ($past['stress_level'] ?? 2);
            $pastComplaints = is_object($past) ? (array) $past->complaints : (array) ($past['complaints'] ?? []);

            $pastScore = $this->calculateBasicGastricScore($pastMealFreq, $pastFoods, $pastLate, $pastStress, $pastComplaints);
            if ($pastScore >= 3) {
                $gastricHitDays5++;
            }
        }

        return [
            'sleep_streak' => $sleepStreak,
            'low_sleep_days_7' => $lowSleepDays7,
            'gastric_complaint_days_5' => $gastricComplaintDays5,
            'gastric_hit_days_5' => $gastricHitDays5,
            'filled_days_5' => $filledDays5,
        ];
    }

    /**
     * Hitung Skor Lambung Dasar Tanpa Penguat Profil / Aturan Pola.
     */
    protected function calculateBasicGastricScore(
        int $mealFreq,
        array $foodCategories,
        ?int $lateEatingHours,
        int $stressLevel,
        array $complaints
    ): int {
        $score = 0;
        if ($mealFreq <= 1) {
            $score += 3;
        } elseif ($mealFreq === 2) {
            $score += 2;
        }

        $triggers = ['Pedas', 'Asam', 'High-Caffeine'];
        $triggerMatches = array_intersect($triggers, $foodCategories);
        $score += min(count($triggerMatches), 2);

        if ($lateEatingHours !== null && $lateEatingHours < 2) {
            $score += 2;
        }

        if ($stressLevel >= 3) {
            $score += 1;
        }

        if (in_array('perih', $complaints) || in_array('asam_naik', $complaints)) {
            $score += 2;
        } elseif (in_array('mual', $complaints) || in_array('kembung', $complaints)) {
            $score += 1;
        }

        return $score;
    }

    /**
     * Periksa apakah alert ditekan oleh cooldown dismissal.
     */
    protected function isAlertSuppressed(string $alertType, int $currentScore, array $dismissals): bool
    {
        $cooldownDays = (int) ($this->config['cooldown']['dismissal_days'] ?? 3);
        $cutoff = Carbon::now()->subDays($cooldownDays);

        foreach ($dismissals as $dismissal) {
            $type = is_object($dismissal) ? $dismissal->alert_type : ($dismissal['alert_type'] ?? '');
            if ($type !== $alertType) {
                continue;
            }

            $dismissedAt = is_object($dismissal)
                ? Carbon::parse($dismissal->dismissed_at)
                : Carbon::parse($dismissal['dismissed_at'] ?? '1970-01-01');

            if ($dismissedAt->gte($cutoff)) {
                $scoreAtDismissal = is_object($dismissal)
                    ? (int) $dismissal->score_at_dismissal
                    : (int) ($dismissal['score_at_dismissal'] ?? 0);

                // Ditekan jika skor sekarang TIDAK naik (> score_at_dismissal)
                if ($currentScore <= $scoreAtDismissal) {
                    return true;
                }
            }
        }

        return false;
    }
}
