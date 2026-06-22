<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Services\Jalali\JalaliDateService;
use App\Services\Settings\SettingService;
use App\Services\ShortLink\ShortLinkService;
use App\Services\Sms\SmsService;
use Carbon\Carbon;

final class AppointmentReminderService
{
    public function __construct(
        private readonly SettingService $settings,
        private readonly SmsService $smsService,
        private readonly ShortLinkService $shortLinkService,
        private readonly JalaliDateService $jalaliDateService,
    ) {}

    public function send24HourReminders(): int
    {
        if (! $this->settings->get('reminder_24h_enabled', true)) {
            return 0;
        }

        $windowStart = now()->addHours(23);
        $windowEnd = now()->addHours(25);

        return $this->sendReminders(
            eventKey: 'reminder_24h_patient',
            windowStart: $windowStart,
            windowEnd: $windowEnd,
            sentAtColumn: 'reminder_24h_sent_at',
        );
    }

    public function send2HourReminders(): int
    {
        if (! $this->settings->get('reminder_2h_enabled', true)) {
            return 0;
        }

        $windowStart = now()->addMinutes(110);
        $windowEnd = now()->addMinutes(130);

        return $this->sendReminders(
            eventKey: 'reminder_2h_patient',
            windowStart: $windowStart,
            windowEnd: $windowEnd,
            sentAtColumn: 'reminder_2h_sent_at',
        );
    }

    private function sendReminders(
        string $eventKey,
        Carbon $windowStart,
        Carbon $windowEnd,
        string $sentAtColumn,
    ): int {
        $appointments = Appointment::query()
            ->confirmed()
            ->upcoming()
            ->whereBetween('starts_at', [$windowStart, $windowEnd])
            ->whereNull($sentAtColumn)
            ->with('doctor')
            ->get();

        $sent = 0;

        foreach ($appointments as $appointment) {
            $shortLink = $this->shortLinkService->forAppointment($appointment);

            $success = $this->smsService->send(
                $eventKey,
                $appointment->patient_phone,
                [
                    'patient_name' => $appointment->patient_name,
                    'doctor_name' => $appointment->doctor?->name ?? '',
                    'appointment_date' => $this->jalaliDateService->formatDate($appointment->starts_at),
                    'appointment_time' => $this->jalaliDateService->formatTime($appointment->starts_at),
                    'time' => $this->jalaliDateService->formatTime($appointment->starts_at),
                    'tracking_code' => $appointment->tracking_code,
                    'tracking_url' => $shortLink->publicUrl(),
                ],
                $appointment->id,
            );

            if ($success) {
                $appointment->update([$sentAtColumn => now()]);
                $sent++;
            }
        }

        return $sent;
    }
}
