<?php

declare(strict_types=1);

namespace App\Services\Appointments;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\Booking\BookingService;
use App\Services\Booking\SlotService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use RuntimeException;

final class AppointmentService
{
    public function __construct(
        private readonly SlotService $slotService,
        private readonly BookingService $bookingService,
    ) {}

    public function generateTrackingCode(): string
    {
        return $this->bookingService->generateTrackingCode();
    }

    /**
     * @return Collection<int, Carbon>
     */
    public function availableSlots(Doctor $doctor, Carbon $date): Collection
    {
        return $this->slotService
            ->getAvailableSlots($doctor, $date)
            ->map(fn (array $slot): Carbon => $slot['starts_at']);
    }

    public function book(
        Doctor $doctor,
        Carbon $startsAt,
        string $patientName,
        string $patientPhone,
        string $patientNationalCode,
    ): Appointment {
        $duration = $doctor->schedules()
            ->active()
            ->where('day_of_week', $startsAt->dayOfWeek)
            ->value('slot_duration_minutes') ?? 30;

        $endsAt = $startsAt->copy()->addMinutes((int) $duration);

        $result = $this->bookingService->book(
            doctor: $doctor,
            startsAt: $startsAt,
            endsAt: $endsAt,
            patientName: $patientName,
            patientPhone: $patientPhone,
            patientNationalCode: $patientNationalCode,
        );

        if (! $result['success'] || $result['appointment'] === null) {
            throw new RuntimeException($result['message']);
        }

        return $result['appointment'];
    }

    public function cancel(Appointment $appointment, ?string $reason = null): array
    {
        return $this->bookingService->cancel($appointment, $reason);
    }

    public function findByTrackingCode(string $code): ?Appointment
    {
        return Appointment::query()
            ->with(['doctor.city', 'doctor.primarySpecialty'])
            ->where('tracking_code', strtoupper($code))
            ->first();
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function findByPatient(string $phone, string $nationalCode): Collection
    {
        return Appointment::query()
            ->with(['doctor.city', 'doctor.primarySpecialty'])
            ->where('patient_phone', $phone)
            ->where('patient_national_code', $nationalCode)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('starts_at', '>=', now()->subDays(30))
            ->orderByDesc('starts_at')
            ->get();
    }
}
