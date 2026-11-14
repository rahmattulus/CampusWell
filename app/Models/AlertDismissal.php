<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertDismissal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'alert_type',
        'score_at_dismissal',
        'dismissed_at',
    ];

    protected function casts(): array
    {
        return [
            'score_at_dismissal' => 'integer',
            'dismissed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

