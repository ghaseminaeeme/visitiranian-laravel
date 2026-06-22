<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlugRedirect extends Model
{
    protected $fillable = [
        'old_slug',
        'new_slug',
        'model_type',
        'model_id',
    ];

    protected function casts(): array
    {
        return [
            'model_id' => 'integer',
        ];
    }
}
