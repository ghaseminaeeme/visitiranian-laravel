<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\AppointmentWaitlist;
use App\Models\Doctor;
use App\Services\Jalali\JalaliDateService;
use App\Services\Settings\SettingService;
use App\Services\ShortLink\ShortLinkService;
use App\Services\Sms\SmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class WaitlistService
{
    public function __construct(
        private readonly SettingService $settings,
        private readonly SmsService $smsService,
        private readonly ShortLinkService $shortLinkService,
        private readonly JalaliDateService $jalaliDateService,
    ) {}

    public function add(
        Doctor $doctor,
        string $patientName,
        string $patientPhone,
        string $patientNationalCode,
        Carbon $preferredDate,
        ?Carbon $preferredStartsAt = null,
    ): AppointmentWaitlist {
        return AppointmentWaitlist::query()->create([
            'doctor_id' => $doctor->id,
            'patient_name' => $patientName,
            'patient_phone' => $patientPhone,
            'patient_national_code' => $patientNationalCode,
            'preferred_date' => $preferredDate->toDateString(),
            'status' => 'waiting',
        ]);
    }

    public function notifyForCancelledSlot(int $doctorId, Carbon $startsAt): ?AppointmentWaitlist
    {
        $holdHours = (int) $this->settings->get('waitlist_hold_hours', 2);

        $waitlistEntry = DB::transaction(function () use ($doctorId, $startsAt, $holdHours): ?AppointmentWaitlist {
            $entry = AppointmentWaitlist::query()
                ->waiting()
                ->where('doctor_id', $doctorId)
                ->whereDate('preferred_date', $startsAt->toDateString())
                ->orderBy('created_at')
                ->lockForUpdate()
                ->first();

            if ($entry === null) {
                return null;
            }

            $entry->update([
                'status' => 'notified',
                'notified_at' => now(),
                'expires_at' => now()->addHours($holdHours),
            ]);

            return $entry->fresh(['doctor']);
        });

        if ($waitlistEntry === null) {
            return null;
        }

        $this->sendWaitlistNotification($waitlistEntry, $startsAt);

        return $waitlistEntry;
    }

    public function expireStaleEntries(): int
    {
        return AppointmentWaitlist::query()
            ->where('status', 'notified')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }

    private function sendWaitlistNotification(AppointmentWaitlist $entry, Carbon $startsAt): void
    {
        $doctor = $entry->doctor;
        $bookingUrl = url('/d/'.$doctor?->slug.'/book?date='.$startsAt->toDateString());
        $shortLink = $this->shortLinkService->create($bookingUrl);

        $this->smsService->send(
            'waitlist_slot_available',
            $entry->patient_phone,
            [
                'patient_name' => $entry->patient_name,
                'doctor_name' => $doctor?->name ?? '',
                'appointment_date' => $this->jalaliDateService->formatDate($startsAt),
                'appointment_time' => $this->jalaliDateService->formatTime($startsAt),
                'date' => $this->jalaliDateService->formatDate($startsAt),
                'time' => $this->jalaliDateService->formatTime($startsAt),
                'expires_at' => $entry->expires_at
                    ? $this->jalaliDateService->formatDateTime($entry->expires_at)
                    : '',
                'short_url' => $shortLink->publicUrl(),
            ],
            doctorId: $doctor?->id,
        );
    }
}
