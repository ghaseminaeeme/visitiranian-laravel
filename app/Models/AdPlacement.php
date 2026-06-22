<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdPlacement extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'allowed_template_keys',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'allowed_template_keys' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class, 'placement_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
