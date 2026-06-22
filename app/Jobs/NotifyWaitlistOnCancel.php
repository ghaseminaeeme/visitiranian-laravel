<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Booking\WaitlistService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class NotifyWaitlistOnCancel implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $doctorId,
        public readonly string $startsAt,
    ) {}

    public function handle(WaitlistService $waitlistService): void
    {
        $waitlistService->notifyForCancelledSlot(
            $this->doctorId,
            Carbon::parse($this->startsAt),
        );
    }
}
