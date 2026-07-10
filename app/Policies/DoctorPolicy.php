<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;
use App\Policies\Concerns\AllowsAdminAccess;
use App\Policies\Concerns\DoctorOwnedPolicy;

class DoctorPolicy
{
    use AllowsAdminAccess;
    use DoctorOwnedPolicy;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $this->hasDoctorProfile($user);
    }

    public function view(User $user, Doctor $doctor): bool
    {
        return $this->isAdmin($user) || $this->ownsDoctorRecord($user, $doctor);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Doctor $doctor): bool
    {
        return $this->isAdmin($user) || $this->ownsDoctorRecord($user, $doctor);
    }

    public function delete(User $user, Doctor $doctor): bool
    {
        return $this->isAdmin($user);
    }
}
