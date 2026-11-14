<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\HabitLog;
use App\Services\CdsEvaluatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(CdsEvaluatorService $evaluator)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // 1. Ambil log hari ini
        $todayLog = HabitLog::with('evaluation')
            ->where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // 2. Ambil 7 hari terakhir (kemarin ke belakang)
        $historyLogs = HabitLog::where('user_id', $user->id)
            ->where('date', '<', $today)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();

        // 3. Profil risiko
        $profile = $user->profile;

        // 4. Hitung fitur turunan ringkasan
        $todayArray = $todayLog ? [
            'sleep_hours' => $todayLog->sleep_hours,
            'meal_freq' => $todayLog->meal_freq,
            'food_categories' => $todayLog->food_categories,
            'late_eating_hours' => $todayLog->late_eating_hours,
            'stress_level' => $todayLog->stress_level,
            'complaints' => $todayLog->complaints,
            'freshness' => $todayLog->freshness,
        ] : [
            'sleep_hours' => 7.0,
            'meal_freq' => 3,
            'food_categories' => ['Netral'],
            'late_eating_hours' => 3,
            'stress_level' => 2,
            'complaints' => ['tidak_ada'],
            'freshness' => 4,
        ];

        $derived = $evaluator->calculateDerivedFeatures($todayArray, $historyLogs->all());

        // 5. Evaluasi aktif hari ini
        $todayEvaluation = $todayLog?->evaluation;

        // Ambil dokter online / siap konsultasi
        $onlineDoctors = Doctor::with('user')
            ->where('is_online', true)
            ->take(3)
            ->get();

        return view('student.home', compact(
            'user',
            'todayLog',
            'todayEvaluation',
            'historyLogs',
            'profile',
            'derived',
            'onlineDoctors'
        ));
    }
}
