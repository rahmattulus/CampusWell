<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AlertDismissal;
use App\Models\AuditLog;
use App\Models\CdsEvaluation;
use App\Models\HabitLog;
use App\Services\CdsEvaluatorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HabitController extends Controller
{
    /**
     * Simpan atau perbarui log habit hari ini dan jalankan evaluasi CDS.
     */
    public function store(Request $request, CdsEvaluatorService $evaluator): JsonResponse
    {
        $validated = $request->validate([
            'sleep_hours' => ['required', 'numeric', 'min:0', 'max:24'],
            'meal_freq' => ['required', 'integer', 'min:0', 'max:5'],
            'food_categories' => ['required', 'array'],
            'food_categories.*' => ['string'],
            'late_eating_hours' => ['nullable', 'integer', 'min:0', 'max:6'],
            'stress_level' => ['required', 'integer', 'min:1', 'max:5'],
            'complaints' => ['required', 'array'],
            'complaints.*' => ['string'],
            'freshness' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Ambil riwayat 7 hari sebelumnya
        $historyLogs = HabitLog::where('user_id', $user->id)
            ->where('date', '<', $today)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();

        // Profil risiko
        $profile = $user->profile;

        // Dismissal alert aktif dalam 3 hari
        $cooldownCutoff = Carbon::now()->subDays(3);
        $recentDismissals = AlertDismissal::where('user_id', $user->id)
            ->where('dismissed_at', '>=', $cooldownCutoff)
            ->get();

        // Jalankan evaluasi CDS (RBR v2.1)
        $evaluationResult = $evaluator->evaluate(
            $validated,
            $historyLogs->all(),
            $profile,
            $recentDismissals->all()
        );

        $habitLog = null;
        $cdsEvaluation = null;

        DB::transaction(function () use (
            $user,
            $today,
            $validated,
            $evaluationResult,
            $request,
            &$habitLog,
            &$cdsEvaluation
        ) {
            // 1. Simpan atau perbarui HabitLog
            $habitLog = HabitLog::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $today,
                ],
                $validated
            );

            // 2. Simpan atau perbarui CdsEvaluation
            $cdsEvaluation = CdsEvaluation::updateOrCreate(
                [
                    'habit_log_id' => $habitLog->id,
                ],
                [
                    'user_id' => $user->id,
                    'date' => $today,
                    'final_level' => $evaluationResult['final_level'],
                    'fatigue_score' => $evaluationResult['fatigue_score'],
                    'fatigue_level' => $evaluationResult['fatigue_level'],
                    'gastric_score' => $evaluationResult['gastric_score'],
                    'gastric_level' => $evaluationResult['gastric_level'],
                    'alerts' => $evaluationResult['alerts'],
                    'rule_ids' => $evaluationResult['rules_triggered'],
                    'reasons' => $evaluationResult['reasons'],
                    'cbr_result' => $evaluationResult['cbr'],
                    'rules_version' => $evaluationResult['rules_version'],
                    'cooldown_suppressed' => $evaluationResult['cooldown_suppressed'],
                ]
            );

            // 3. CDS-04: Pencatatan Log Audit jika evaluasi PERLU_PERHATIAN
            if ($evaluationResult['final_level'] === 'PERLU_PERHATIAN') {
                AuditLog::create([
                    'actor_id' => $user->id,
                    'action' => 'CDS_EVALUATION_RISK_TRIGGERED',
                    'object_type' => 'cds_evaluations',
                    'object_id' => $cdsEvaluation->id,
                    'details' => [
                        'date' => $today,
                        'final_level' => $evaluationResult['final_level'],
                        'fatigue_score' => $evaluationResult['fatigue_score'],
                        'gastric_score' => $evaluationResult['gastric_score'],
                        'rules_triggered' => $evaluationResult['rules_triggered'],
                        'alerts_count' => count($evaluationResult['alerts']),
                        'cooldown_suppressed' => $evaluationResult['cooldown_suppressed'],
                        'input_summary' => [
                            'sleep_hours' => $validated['sleep_hours'],
                            'meal_freq' => $validated['meal_freq'],
                            'stress_level' => $validated['stress_level'],
                            'complaints' => $validated['complaints'],
                        ],
                    ],
                    'ip_address' => $request->ip(),
                    'created_at' => Carbon::now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Log kebiasaan sehat hari ini berhasil disimpan.',
            'habit_log' => $habitLog,
            'evaluation' => $evaluationResult,
        ]);
    }

    /**
     * Catat penolakan / dismissal alert oleh mahasiswa untuk periode cooldown 3 hari.
     */
    public function dismissAlert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'alert_type' => ['required', 'string', 'in:FATIGUE,GASTRIC'],
            'score' => ['required', 'integer'],
        ]);

        $user = Auth::user();

        AlertDismissal::create([
            'user_id' => $user->id,
            'alert_type' => $validated['alert_type'],
            'score_at_dismissal' => $validated['score'],
            'dismissed_at' => Carbon::now(),
        ]);

        // Catat di audit log
        AuditLog::create([
            'actor_id' => $user->id,
            'action' => 'ALERT_DISMISSED_BY_STUDENT',
            'object_type' => 'alert_dismissals',
            'object_id' => null,
            'details' => [
                'alert_type' => $validated['alert_type'],
                'score_at_dismissal' => $validated['score'],
            ],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alert berhasil ditolak (ditekan selama 3 hari jika skor tidak naik).',
        ]);
    }
}
