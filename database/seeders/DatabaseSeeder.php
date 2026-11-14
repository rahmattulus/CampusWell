<?php

namespace Database\Seeders;

use App\Models\CdsConfigVersion;
use App\Models\Doctor;
use App\Models\HabitLog;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        $admin = User::create([
            'name' => 'Admin IT CampusWell',
            'email' => 'admin@campuswell.ac.id',
            'role' => 'admin',
            'verification_status' => 'verified',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // 2. Akun Dokter
        $drSarah = User::create([
            'name' => 'dr. Sarah Paramita, Sp.PD',
            'email' => 'dr.sarah@campuswell.ac.id',
            'role' => 'doctor',
            'verification_status' => 'verified',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Doctor::create([
            'user_id' => $drSarah->id,
            'specialization' => 'Dokter Spesialis Penyakit Dalam',
            'sip_number' => 'SIP/123/2024',
            'practice_schedule' => 'Senin - Kamis, 08:00 - 14:00 WIB',
            'is_online' => true,
        ]);

        $drBudi = User::create([
            'name' => 'dr. Budi Santoso',
            'email' => 'dr.budi@campuswell.ac.id',
            'role' => 'doctor',
            'verification_status' => 'verified',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Doctor::create([
            'user_id' => $drBudi->id,
            'specialization' => 'Dokter Umum Klinik Kampus',
            'sip_number' => 'SIP/456/2024',
            'practice_schedule' => 'Selasa - Jumat, 10:00 - 16:00 WIB',
            'is_online' => false,
        ]);

        // 3. Akun Mahasiswa
        $studentAhmad = User::create([
            'name' => 'Ahmad Fauzi',
            'nim' => '2201011001',
            'email' => 'ahmad.fauzi@student.ac.id',
            'role' => 'student',
            'verification_status' => 'verified',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $studentAhmad->id,
            'p1_gastric_history' => 'Ya',
            'p2_smoking' => 'Tidak',
            'p3_nsaid_usage' => 'Kadang',
            'p4_chronic_conditions' => null,
            'consent_at' => now(),
        ]);

        User::create([
            'name' => 'Siti Rahma',
            'nim' => '2201011002',
            'email' => 'siti.rahma@student.ac.id',
            'role' => 'student',
            'verification_status' => 'pending',
            'password' => Hash::make('password'),
        ]);

        // 4. Inisialisasi Versi Konfigurasi CDS v2.1.0
        $configPath = config_path('cds_config.json');
        if (File::exists($configPath)) {
            $configJson = json_decode(File::get($configPath), true);
            CdsConfigVersion::create([
                'version' => '2.1.0',
                'config_json' => $configJson,
                'changed_by' => $admin->id,
                'approved_by_expert' => true,
                'notes' => 'Konfigurasi bawaan sistem v2.1.0 draf ahli',
            ]);
        }

        // 5. Data Habit Log Contoh (7 Hari Terakhir untuk Ahmad Fauzi)
        $sampleLogs = [
            [
                'days_ago' => 6,
                'sleep_hours' => 7.5,
                'meal_freq' => 3,
                'food_categories' => ['Netral'],
                'late_eating_hours' => 3,
                'stress_level' => 2,
                'complaints' => ['tidak_ada'],
                'freshness' => 4,
            ],
            [
                'days_ago' => 5,
                'sleep_hours' => 7.0,
                'meal_freq' => 3,
                'food_categories' => ['Netral'],
                'late_eating_hours' => 3,
                'stress_level' => 2,
                'complaints' => ['tidak_ada'],
                'freshness' => 4,
            ],
            [
                'days_ago' => 4,
                'sleep_hours' => 6.5,
                'meal_freq' => 3,
                'food_categories' => ['Pedas'],
                'late_eating_hours' => 2,
                'stress_level' => 3,
                'complaints' => ['tidak_ada'],
                'freshness' => 3,
            ],
            [
                'days_ago' => 3,
                'sleep_hours' => 5.0,
                'meal_freq' => 2,
                'food_categories' => ['Pedas', 'High-Caffeine'],
                'late_eating_hours' => 1,
                'stress_level' => 4,
                'complaints' => ['mual'],
                'freshness' => 2,
            ],
            [
                'days_ago' => 2,
                'sleep_hours' => 5.0,
                'meal_freq' => 2,
                'food_categories' => ['Pedas'],
                'late_eating_hours' => 1,
                'stress_level' => 4,
                'complaints' => ['perih'],
                'freshness' => 2,
            ],
            [
                'days_ago' => 1,
                'sleep_hours' => 4.5,
                'meal_freq' => 2,
                'food_categories' => ['High-Caffeine'],
                'late_eating_hours' => 1,
                'stress_level' => 4,
                'complaints' => ['perih', 'lelah'],
                'freshness' => 2,
            ],
            [
                'days_ago' => 0,
                'sleep_hours' => 4.0,
                'meal_freq' => 1,
                'food_categories' => ['Pedas', 'High-Caffeine'],
                'late_eating_hours' => 1,
                'stress_level' => 5,
                'complaints' => ['perih', 'lelah', 'konsentrasi'],
                'freshness' => 1,
            ],
        ];

        foreach ($sampleLogs as $log) {
            HabitLog::create([
                'user_id' => $studentAhmad->id,
                'date' => Carbon::now()->subDays($log['days_ago'])->toDateString(),
                'sleep_hours' => $log['sleep_hours'],
                'meal_freq' => $log['meal_freq'],
                'food_categories' => $log['food_categories'],
                'late_eating_hours' => $log['late_eating_hours'],
                'stress_level' => $log['stress_level'],
                'complaints' => $log['complaints'],
                'freshness' => $log['freshness'],
            ]);
        }
    }
}
