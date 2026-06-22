<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\DoctorScheduleException;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class SlotService
{
    /**
     * @return Collection<int, array{starts_at: Carbon, ends_at: Carbon, schedule_id: int}>
     */
    public function getAvailableSlots(Doctor $doctor, Carbon $date): Collection
    {
        $date = $date->copy()->startOfDay();
        $dayOfWeek = $date->dayOfWeek;

        $exception = DoctorScheduleException::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('exception_date', $date)
            ->first();

        if ($exception?->is_closed) {
            return collect();
        }

        $schedules = DoctorSchedule::query()
            ->active()
            ->where('doctor_id', $doctor->id)
            ->where('day_of_week', $dayOfWeek)
            ->get();

        if ($schedules->isEmpty()) {
            return collect();
        }

        $bookedStarts = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->where('status', 'confirmed')
            ->whereDate('starts_at', $date)
            ->pluck('starts_at')
            ->map(fn ($startsAt): string => Carbon::parse($startsAt)->format('Y-m-d H:i:s'))
            ->all();

        $slots = collect();

        foreach ($schedules as $schedule) {
            $startTime = $exception && ! $exception->is_closed && $exception->start_time
                ? $exception->start_time
                : $schedule->start_time;
            $endTime = $exception && ! $exception->is_closed && $exception->end_time
                ? $exception->end_time
                : $schedule->end_time;

            $slotStart = $date->copy()->setTimeFromTimeString((string) $startTime);
            $scheduleEnd = $date->copy()->setTimeFromTimeString((string) $endTime);
            $duration = (int) $schedule->slot_duration_minutes;

            while ($slotStart->copy()->addMinutes($duration)->lte($scheduleEnd)) {
                $slotEnd = $slotStart->copy()->addMinutes($duration);

                if ($slotStart->isFuture() && ! in_array($slotStart->format('Y-m-d H:i:s'), $bookedStarts, true)) {
                    $slots->push([
                        'starts_at' => $slotStart->copy(),
                        'ends_at' => $slotEnd->copy(),
                        'schedule_id' => $schedule->id,
                    ]);
                }

                $slotStart = $slotEnd;
            }
        }

        return $slots->sortBy(fn (array $slot): int => $slot['starts_at']->timestamp)->values();
    }

    public function hasAvailableSlot(Doctor $doctor, Carbon $date): bool
    {
        return $this->getAvailableSlots($doctor, $date)->isNotEmpty();
    }
}
