<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        'phone',
        'event_key',
        'message_body',
        'status',
        'provider_message_id',
        'error_message',
        'appointment_id',
        'doctor_id',
        'context',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
