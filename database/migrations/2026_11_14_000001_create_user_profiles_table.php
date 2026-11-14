<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            // P1: Riwayat maag/GERD yang pernah didiagnosis dokter (Ya / Tidak / Tidak tahu)
            $table->string('p1_gastric_history', 20)->default('Tidak');
            // P2: Merokok (Tidak / Kadang / Rutin)
            $table->string('p2_smoking', 20)->default('Tidak');
            // P3: Pemakaian rutin obat pereda nyeri NSAID (Tidak / Kadang / Rutin)
            $table->string('p3_nsaid_usage', 20)->default('Tidak');
            // P4: Kondisi kronis lain (opsional)
            $table->text('p4_chronic_conditions')->nullable();
            // Consent persetujuan penyimpanan dan pemrosesan data kesehatan
            $table->timestamp('consent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};

