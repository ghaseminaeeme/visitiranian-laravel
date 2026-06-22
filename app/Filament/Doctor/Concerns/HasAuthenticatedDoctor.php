<?php

declare(strict_types=1);

namespace App\Filament\Doctor\Concerns;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Builder;

trait HasAuthenticatedDoctor
{
    protected static function authenticatedDoctor(): ?Doctor
    {
        return auth()->user()?->doctor;
    }

    protected static function authenticatedDoctorId(): ?int
    {
        return static::authenticatedDoctor()?->id;
    }

    protected static function scopeToAuthenticatedDoctor(Builder $query, string $column = 'doctor_id'): Builder
    {
        $doctorId = static::authenticatedDoctorId();

        if ($doctorId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where($column, $doctorId);
    }
}
