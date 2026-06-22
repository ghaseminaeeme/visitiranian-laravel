<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentWaitlist extends Model
{
    protected $table = 'appointment_waitlist';

    protected $fillable = [
        'doctor_id',
        'patient_name',
        'patient_phone',
        'patient_national_code',
        'preferred_date',
        'status',
        'notified_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'notified_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function scopeWaiting(Builder $query): Builder
    {
        return $query->where('status', 'waiting');
    }

    /** @deprecated Use scopeWaiting — alias for backward compatibility */
    public function scopePending(Builder $query): Builder
    {
        return $query->waiting();
    }
}
