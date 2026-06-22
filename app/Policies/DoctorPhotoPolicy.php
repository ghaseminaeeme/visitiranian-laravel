<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\DoctorPhoto;
use App\Models\User;
use App\Policies\Concerns\DoctorOwnedPolicy;

class DoctorPhotoPolicy
{
    use DoctorOwnedPolicy;

    public function viewAny(User $user): bool
    {
        return $this->hasDoctorProfile($user);
    }

    public function view(User $user, DoctorPhoto $doctorPhoto): bool
    {
        return $this->ownsDoctorRecord($user, $doctorPhoto);
    }

    public function create(User $user): bool
    {
        return $this->hasDoctorProfile($user);
    }

    public function update(User $user, DoctorPhoto $doctorPhoto): bool
    {
        return $this->ownsDoctorRecord($user, $doctorPhoto);
    }

    public function delete(User $user, DoctorPhoto $doctorPhoto): bool
    {
        return $this->ownsDoctorRecord($user, $doctorPhoto);
    }
}
