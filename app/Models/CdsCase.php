<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CdsCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'source',
        'status',
        'features',
        'outcome',
        'expert_label',
        'validated_by',
        'rules_version',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
        ];
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}

