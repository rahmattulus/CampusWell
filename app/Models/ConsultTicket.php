<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ConsultTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'student_id',
        'doctor_id',
        'evaluation_id',
        'source',
        'status',
        'consent_share_data',
        'consent_share_profile',
        'requested_at',
        'accepted_at',
        'closed_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'consent_share_data' => 'boolean',
            'consent_share_profile' => 'boolean',
            'requested_at' => 'datetime',
            'accepted_at' => 'datetime',
            'closed_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(CdsEvaluation::class, 'evaluation_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class, 'ticket_id');
    }
}

