<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HabitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'sleep_hours',
        'meal_freq',
        'food_categories',
        'late_eating_hours',
        'stress_level',
        'complaints',
        'freshness',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'sleep_hours' => 'float',
            'meal_freq' => 'integer',
            'food_categories' => 'array',
            'late_eating_hours' => 'integer',
            'stress_level' => 'integer',
            'complaints' => 'array',
            'freshness' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluation(): HasOne
    {
        return $this->hasOne(CdsEvaluation::class);
    }
}

