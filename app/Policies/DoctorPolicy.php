<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;
use App\Policies\Concerns\DoctorOwnedPolicy;

class DoctorPolicy
{
    use DoctorOwnedPolicy;

    public function viewAny(User $user): bool
    {
        return $this->hasDoctorProfile($user);
    }

    public function view(User $user, Doctor $doctor): bool
    {
        return $this->ownsDoctorRecord($user, $doctor);
    }

    public function update(User $user, Doctor $doctor): bool
    {
        return $this->ownsDoctorRecord($user, $doctor);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, Doctor $doctor): bool
    {
        return false;
    }
}
