<?php

declare(strict_types=1);

namespace App\Services\Sms;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

final class KavenegarService
{
    private const string BASE_URL = 'https://api.kavenegar.com/v1';

    /**
     * @return array{success: bool, message_id: ?string, error: ?string, response: ?array}
     */
    public function send(string $phone, string $message, ?string $sender = null): array
    {
        $apiKey = (string) config('visitiranian.kavenegar.api_key');
        $sender ??= (string) config('visitiranian.kavenegar.sender');

        if ($apiKey === '') {
            return [
                'success' => false,
                'message_id' => null,
                'error' => 'Kavenegar API key is not configured',
                'response' => null,
            ];
        }

        $phone = $this->normalizePhone($phone);

        try {
            $response = Http::timeout(15)
                ->get(self::BASE_URL.'/'.$apiKey.'/sms/send.json', [
                    'receptor' => $phone,
                    'sender' => $sender,
                    'message' => $message,
                ])
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            return [
                'success' => false,
                'message_id' => null,
                'error' => $exception->getMessage(),
                'response' => null,
            ];
        }

        $entries = $response['entries'] ?? [];
        $entry = is_array($entries) ? ($entries[0] ?? null) : null;
        $messageId = is_array($entry) ? (string) ($entry['messageid'] ?? '') : null;

        return [
            'success' => ($response['return']['status'] ?? 0) === 200,
            'message_id' => $messageId !== '' ? $messageId : null,
            'error' => ($response['return']['status'] ?? 0) === 200
                ? null
                : (string) ($response['return']['message'] ?? 'Unknown Kavenegar error'),
            'response' => is_array($response) ? $response : null,
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '98')) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '98'.substr($digits, 1);
        }

        if (strlen($digits) === 10) {
            return '98'.$digits;
        }

        return $digits;
    }
}
