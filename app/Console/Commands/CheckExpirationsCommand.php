<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Doctor;
use App\Services\Booking\WaitlistService;
use App\Services\Settings\SettingService;
use Illuminate\Console\Command;

final class CheckExpirationsCommand extends Command
{
    protected $signature = 'appointments:check-expirations';

    protected $description = 'Expire waitlist holds and hide expired doctor profiles';

    public function handle(WaitlistService $waitlistService, SettingService $settings): int
    {
        $expiredWaitlist = $waitlistService->expireStaleEntries();
        $this->info("Expired {$expiredWaitlist} waitlist entries.");

        if ($settings->get('hide_doctors_on_expiry', true)) {
            $hidden = Doctor::query()
                ->where('is_published', true)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now())
                ->update(['is_published' => false]);

            $this->info("Unpublished {$hidden} expired doctors.");
        }

        return self::SUCCESS;
    }
}
