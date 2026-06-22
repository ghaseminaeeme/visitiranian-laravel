<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\DoctorSchedule;
use App\Models\User;
use App\Policies\Concerns\DoctorOwnedPolicy;

class DoctorSchedulePolicy
{
    use DoctorOwnedPolicy;

    public function viewAny(User $user): bool
    {
        return $this->hasDoctorProfile($user);
    }

    public function view(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return $this->ownsDoctorRecord($user, $doctorSchedule);
    }

    public function create(User $user): bool
    {
        return $this->hasDoctorProfile($user);
    }

    public function update(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return $this->ownsDoctorRecord($user, $doctorSchedule);
    }

    public function delete(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return $this->ownsDoctorRecord($user, $doctorSchedule);
    }
}
