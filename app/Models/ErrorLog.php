<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErrorLog extends Model
{
    protected $fillable = [
        'level',
        'message',
        'exception_class',
        'file',
        'line',
        'stack_trace',
        'url',
        'http_method',
        'user_id',
        'ip_address',
        'user_agent',
        'context',
        'request_input',
        'occurred_at',
        'status',
        'resolved_at',
        'resolved_note',
    ];

    protected function casts(): array
    {
        return [
            'line' => 'integer',
            'context' => 'array',
            'request_input' => 'array',
            'occurred_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->where('status', '!=', 'resolved');
    }
}
