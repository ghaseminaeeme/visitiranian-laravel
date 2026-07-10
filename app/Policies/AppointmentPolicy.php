<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use App\Policies\Concerns\AllowsAdminAccess;
use App\Policies\Concerns\DoctorOwnedPolicy;

class AppointmentPolicy
{
    use AllowsAdminAccess;
    use DoctorOwnedPolicy;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $this->hasDoctorProfile($user);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $this->isAdmin($user) || $this->ownsDoctorRecord($user, $appointment);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $this->isAdmin($user) || $this->ownsDoctorRecord($user, $appointment);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $this->isAdmin($user);
    }
}
