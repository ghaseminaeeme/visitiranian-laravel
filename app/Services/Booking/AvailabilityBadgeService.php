<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class AvailabilityBadgeService
{
    public function __construct(
        private readonly SlotService $slotService,
    ) {}

    public function badgeFor(Doctor $doctor): ?string
    {
        if ($this->slotService->getAvailableSlots($doctor, Carbon::today())->isNotEmpty()) {
            return 'today';
        }

        if ($this->slotService->getAvailableSlots($doctor, Carbon::tomorrow())->isNotEmpty()) {
            return 'tomorrow';
        }

        return null;
    }

    /**
     * @param  Collection<int, Doctor>  $doctors
     * @return array<int, string|null>
     */
    public function badgesFor(Collection $doctors): array
    {
        $badges = [];

        foreach ($doctors as $doctor) {
            $badges[$doctor->id] = $this->badgeFor($doctor);
        }

        return $badges;
    }
}
