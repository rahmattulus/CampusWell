<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            // D1: Durasi tidur (0–24 jam)
            $table->decimal('sleep_hours', 4, 1);
            // D2: Frekuensi makan (0–5, 5 = 5 atau lebih)
            $table->unsignedTinyInteger('meal_freq');
            // D3: Kategori makanan/minuman (Pedas, Asam, Manis, High-Caffeine, Netral)
            $table->json('food_categories');
            // D4: Jarak makan terakhir ke tidur (0–6 jam; null = tidak makan malam)
            $table->unsignedTinyInteger('late_eating_hours')->nullable();
            // D5: Tingkat stres (1–5)
            $table->unsignedTinyInteger('stress_level');
            // D6: Keluhan hari ini (perih, asam_naik, mual, kembung, lelah, konsentrasi, tidak_ada)
            $table->json('complaints');
            // D7: Kesegaran saat bangun (1–5)
            $table->unsignedTinyInteger('freshness');
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habit_logs');
    }
};

