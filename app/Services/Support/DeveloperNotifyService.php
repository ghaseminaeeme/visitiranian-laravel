<?php

declare(strict_types=1);

namespace App\Services\Support;

use App\Models\SupportTicket;
use App\Services\Settings\SettingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class DeveloperNotifyService
{
    public function __construct(
        private readonly SettingService $settings,
    ) {}

    public function notify(string $message): void
    {
        $channel = (string) $this->settings->get('support_notify_channel', 'telegram');

        match ($channel) {
            'bale' => $this->sendBale($message),
            'both' => $this->sendTelegram($message) || $this->sendBale($message),
            default => $this->sendTelegram($message),
        };
    }

    public function notifySupportTicket(SupportTicket $ticket): void
    {
        $lines = [
            '🎫 تیکت پشتیبانی جدید',
            'شماره: '.$ticket->ticket_number,
            'موضوع: '.$ticket->subject,
            'دسته: '.($ticket->category ?? '—'),
            'وضعیت: '.$ticket->status,
        ];

        if ($ticket->page_url) {
            $lines[] = 'صفحه: '.$ticket->page_url;
        }

        $lines[] = '';
        $lines[] = mb_substr($ticket->body, 0, 500);

        $this->notify(implode("\n", $lines));
    }

    public function sendTelegram(string $message): bool
    {
        $token = (string) config('visitiranian.telegram.bot_token');
        $chatId = (string) config('visitiranian.telegram.chat_id');

        if ($token === '' || $chatId === '') {
            Log::warning('Telegram bot credentials are not configured');

            return false;
        }

        try {
            $response = Http::timeout(10)
                ->post('https://api.telegram.org/bot'.$token.'/sendMessage', [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'disable_web_page_preview' => true,
                ]);

            return $response->successful();
        } catch (\Throwable $exception) {
            Log::error('Telegram notification failed', ['error' => $exception->getMessage()]);

            return false;
        }
    }

    public function sendBale(string $message): bool
    {
        $token = (string) config('visitiranian.bale.bot_token');
        $chatId = (string) config('visitiranian.bale.chat_id');

        if ($token === '' || $chatId === '') {
            Log::warning('Bale bot credentials are not configured');

            return false;
        }

        try {
            $response = Http::timeout(10)
                ->post('https://tapi.bale.ai/bot'.$token.'/sendMessage', [
                    'chat_id' => $chatId,
                    'text' => $message,
                ]);

            return $response->successful();
        } catch (\Throwable $exception) {
            Log::error('Bale notification failed', ['error' => $exception->getMessage()]);

            return false;
        }
    }
}
