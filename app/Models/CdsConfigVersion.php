<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CdsConfigVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'config_json',
        'changed_by',
        'approved_by_expert',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'config_json' => 'array',
            'approved_by_expert' => 'boolean',
        ];
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

