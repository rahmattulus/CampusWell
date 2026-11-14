<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cds_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_log_id')->constrained('habit_logs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->enum('final_level', ['NORMAL', 'PERHATIAN_MANDIRI', 'PERLU_PERHATIAN']);
            $table->integer('fatigue_score')->default(0);
            $table->string('fatigue_level')->default('NORMAL');
            $table->integer('gastric_score')->default(0);
            $table->string('gastric_level')->default('NORMAL');
            $table->json('alerts')->nullable();
            $table->json('rule_ids')->nullable();
            $table->json('reasons')->nullable();
            $table->json('cbr_result')->nullable();
            $table->string('rules_version', 20)->default('2.1.0');
            $table->boolean('cooldown_suppressed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['final_level']);
        });

        Schema::create('alert_dismissals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('alert_type', 50); // FATIGUE, GASTRIC
            $table->integer('score_at_dismissal');
            $table->timestamp('dismissed_at');
            $table->timestamps();

            $table->index(['user_id', 'alert_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_dismissals');
        Schema::dropIfExists('cds_evaluations');
    }
};

