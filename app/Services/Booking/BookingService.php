<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Jobs\NotifyWaitlistOnCancel;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\Jalali\JalaliDateService;
use App\Services\ShortLink\ShortLinkService;
use App\Services\Sms\SmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class BookingService
{
    private const string TRACKING_CHARS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function __construct(
        private readonly SlotService $slotService,
        private readonly SmsService $smsService,
        private readonly ShortLinkService $shortLinkService,
        private readonly JalaliDateService $jalaliDateService,
    ) {}

    /**
     * @return array{success: bool, message: string, appointment: ?Appointment}
     */
    public function book(
        Doctor $doctor,
        Carbon $startsAt,
        Carbon $endsAt,
        string $patientName,
        string $patientPhone,
        string $patientNationalCode,
    ): array {
        if (! $doctor->is_active || ! $doctor->is_published) {
            return [
                'success' => false,
                'message' => 'پزشک فعال یا منتشر شده نیست',
                'appointment' => null,
            ];
        }

        $available = $this->slotService
            ->getAvailableSlots($doctor, $startsAt->copy()->startOfDay())
            ->first(fn (array $slot): bool => $slot['starts_at']->equalTo($startsAt));

        if ($available === null) {
            return [
                'success' => false,
                'message' => 'این زمان در دسترس نیست',
                'appointment' => null,
            ];
        }

        try {
            $appointment = DB::transaction(function () use (
                $doctor,
                $startsAt,
                $endsAt,
                $patientName,
                $patientPhone,
                $patientNationalCode,
            ): Appointment {
                $existing = Appointment::query()
                    ->where('doctor_id', $doctor->id)
                    ->where('starts_at', $startsAt)
                    ->where('status', 'confirmed')
                    ->lockForUpdate()
                    ->exists();

                if ($existing) {
                    throw new RuntimeException('این زمان قبلاً رزرو شده است');
                }

                return Appointment::query()->create([
                    'doctor_id' => $doctor->id,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'patient_name' => $patientName,
                    'patient_phone' => $patientPhone,
                    'patient_national_code' => $patientNationalCode,
                    'tracking_code' => $this->generateTrackingCode(),
                    'status' => 'confirmed',
                    'booked_at' => now(),
                ]);
            });
        } catch (RuntimeException $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage(),
                'appointment' => null,
            ];
        }

        $this->sendBookingSms($appointment->load('doctor'));

        return [
            'success' => true,
            'message' => 'نوبت با موفقیت ثبت شد',
            'appointment' => $appointment,
        ];
    }

    /**
     * @return array{success: bool, message: string, appointment: ?Appointment}
     */
    public function cancel(Appointment $appointment, ?string $reason = null): array
    {
        if ($appointment->status === 'cancelled') {
            return [
                'success' => false,
                'message' => 'نوبت قبلاً لغو شده است',
                'appointment' => $appointment,
            ];
        }

        if (in_array($appointment->status, ['completed', 'no_show'], true)) {
            return [
                'success' => false,
                'message' => 'نوبت قابل لغو نیست',
                'appointment' => $appointment,
            ];
        }

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        $this->sendCancellationSms($appointment->load('doctor'));

        NotifyWaitlistOnCancel::dispatch(
            $appointment->doctor_id,
            $appointment->starts_at->toDateTimeString(),
        );

        return [
            'success' => true,
            'message' => 'نوبت لغو شد',
            'appointment' => $appointment->fresh(),
        ];
    }

    public function generateTrackingCode(): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $code = '';
            $chars = self::TRACKING_CHARS;

            for ($i = 0; $i < 8; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }

            if (! Appointment::query()->where('tracking_code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('تولید کد پیگیری ناموفق');
    }

    private function sendBookingSms(Appointment $appointment): void
    {
        $doctor = $appointment->doctor;
        $shortLink = $this->shortLinkService->forAppointment($appointment);
        $placeholders = $this->buildPlaceholders($appointment, $shortLink->publicUrl());

        $this->smsService->send(
            'booking_patient',
            $appointment->patient_phone,
            $placeholders,
            $appointment->id,
        );

        if ($doctor?->sms_mobile) {
            $this->smsService->send(
                'booking_doctor',
                $doctor->sms_mobile,
                $placeholders,
                $appointment->id,
                $doctor->id,
            );
        }
    }

    private function sendCancellationSms(Appointment $appointment): void
    {
        $doctor = $appointment->doctor;
        $placeholders = $this->buildPlaceholders($appointment);

        $this->smsService->send(
            'cancel_patient',
            $appointment->patient_phone,
            $placeholders,
            $appointment->id,
        );

        if ($doctor?->sms_mobile) {
            $this->smsService->send(
                'cancel_doctor',
                $doctor->sms_mobile,
                $placeholders,
                $appointment->id,
                $doctor->id,
            );
        }
    }

    /**
     * @return array<string, string>
     */
    private function buildPlaceholders(Appointment $appointment, ?string $trackingUrl = null): array
    {
        $trackingUrl ??= url('/peygiri');

        return [
            'patient_name' => $appointment->patient_name,
            'doctor_name' => $appointment->doctor?->name ?? '',
            'appointment_date' => $this->jalaliDateService->formatDate($appointment->starts_at),
            'appointment_time' => $this->jalaliDateService->formatTime($appointment->starts_at),
            'date' => $this->jalaliDateService->formatDate($appointment->starts_at),
            'time' => $this->jalaliDateService->formatTime($appointment->starts_at),
            'tracking_code' => $appointment->tracking_code,
            'tracking_url' => $trackingUrl,
            'peygiri_url' => url('/peygiri'),
            'short_url' => $trackingUrl,
        ];
    }
}
