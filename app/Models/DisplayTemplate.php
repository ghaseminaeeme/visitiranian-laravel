<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisplayTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'image_width',
        'image_height',
        'layout_config',
        'text_limits',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'image_width' => 'integer',
            'image_height' => 'integer',
            'layout_config' => 'array',
            'text_limits' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class, 'template_id');
    }

    public function sliders(): HasMany
    {
        return $this->hasMany(Slider::class, 'template_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
