<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortLink extends Model
{
    protected $fillable = [
        'code',
        'target_url',
        'appointment_id',
        'expires_at',
        'clicks_count',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'clicks_count' => 'integer',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function publicUrl(): string
    {
        return url('/s/'.$this->code);
    }
}
