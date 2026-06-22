<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\SupportTicket;
use App\Services\Support\DeveloperNotifyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class SendSupportTicketNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $supportTicketId,
    ) {}

    public function handle(DeveloperNotifyService $notifier): void
    {
        $ticket = SupportTicket::query()->find($this->supportTicketId);

        if ($ticket === null) {
            return;
        }

        $notifier->notifySupportTicket($ticket);

        $channels = $ticket->notified_via ?? [];
        $channels[] = 'developer_bot';
        $ticket->update(['notified_via' => array_values(array_unique($channels))]);
    }
}
