<?php

declare(strict_types=1);

namespace App\Services\Sms;

use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Services\Settings\SettingService;

final class SmsService
{
    public function __construct(
        private readonly KavenegarService $kavenegar,
        private readonly SettingService $settings,
    ) {}

    /**
     * @param  array<string, string>  $placeholders
     */
    public function send(
        string $eventKey,
        string $phone,
        array $placeholders = [],
        ?int $appointmentId = null,
        ?int $doctorId = null,
    ): bool {
        $template = SmsTemplate::query()
            ->where('event_key', $eventKey)
            ->first();

        if ($template === null || ! $template->is_enabled) {
            return false;
        }

        $body = $this->replacePlaceholders($template->template_body, $placeholders);

        $log = SmsLog::query()->create([
            'phone' => $phone,
            'event_key' => $eventKey,
            'message_body' => $body,
            'status' => 'pending',
            'appointment_id' => $appointmentId,
            'doctor_id' => $doctorId,
            'context' => $placeholders,
        ]);

        $sender = (string) ($this->settings->get('kavenegar_sender') ?: config('visitiranian.kavenegar.sender'));
        $result = $this->kavenegar->send($phone, $body, $sender !== '' ? $sender : null);

        if ($result['success']) {
            $log->update([
                'status' => 'sent',
                'provider_message_id' => $result['message_id'],
                'sent_at' => now(),
            ]);

            return true;
        }

        $log->update([
            'status' => 'failed',
            'error_message' => $result['error'],
        ]);

        return false;
    }

    public function isEnabled(string $eventKey): bool
    {
        return SmsTemplate::query()
            ->where('event_key', $eventKey)
            ->where('is_enabled', true)
            ->exists();
    }

    /**
     * @param  array<string, string>  $placeholders
     */
    public function replacePlaceholders(string $template, array $placeholders): string
    {
        $message = $template;

        foreach ($placeholders as $key => $value) {
            $message = str_replace([
                '{'.$key.'}',
                '%'.$key.'%',
            ], $value, $message);
        }

        return $message;
    }
}
