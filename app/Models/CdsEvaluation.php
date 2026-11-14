<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CdsEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'habit_log_id',
        'user_id',
        'date',
        'final_level',
        'fatigue_score',
        'fatigue_level',
        'gastric_score',
        'gastric_level',
        'alerts',
        'rule_ids',
        'reasons',
        'cbr_result',
        'rules_version',
        'cooldown_suppressed',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'fatigue_score' => 'integer',
            'gastric_score' => 'integer',
            'alerts' => 'array',
            'rule_ids' => 'array',
            'reasons' => 'array',
            'cbr_result' => 'array',
            'cooldown_suppressed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function habitLog(): BelongsTo
    {
        return $this->belongsTo(HabitLog::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(ConsultTicket::class, 'evaluation_id');
    }
}

