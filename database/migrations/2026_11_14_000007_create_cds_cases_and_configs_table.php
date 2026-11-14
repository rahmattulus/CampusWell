<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cds_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_id', 50)->unique();
            $table->enum('source', ['synthetic', 'consultation'])->default('synthetic');
            $table->enum('status', ['pending_review', 'approved', 'rejected'])->default('pending_review');
            $table->json('features'); // 8 features: avg_sleep, max_sleep_streak, avg_meal_freq, trigger_day_ratio, late_eating_ratio, avg_stress, gastric_complaint_ratio, avg_freshness
            $table->enum('outcome', ['NORMAL', 'PERHATIAN_MANDIRI', 'PERLU_PERHATIAN']);
            $table->string('expert_label', 50)->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('rules_version', 20)->default('2.1.0');
            $table->timestamps();

            $table->index(['status', 'outcome']);
        });

        Schema::create('cds_config_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20)->unique();
            $table->json('config_json');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('approved_by_expert')->default(false);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cds_config_versions');
        Schema::dropIfExists('cds_cases');
    }
};

