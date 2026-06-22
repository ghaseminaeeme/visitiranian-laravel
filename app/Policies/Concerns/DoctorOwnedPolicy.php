<?php

declare(strict_types=1);

namespace App\Policies\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait DoctorOwnedPolicy
{
    protected function hasDoctorProfile(User $user): bool
    {
        return $user->hasRole('doctor') && $user->doctor !== null;
    }

    protected function ownsDoctorRecord(User $user, Model $record): bool
    {
        return $this->hasDoctorProfile($user)
            && (int) $record->getAttribute('doctor_id') === (int) $user->doctor->id;
    }
}
