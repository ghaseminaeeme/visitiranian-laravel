<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendAppointmentReminders;
use Illuminate\Console\Command;

final class SendRemindersCommand extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Send 24-hour and 2-hour appointment SMS reminders';

    public function handle(): int
    {
        SendAppointmentReminders::dispatchSync();

        $this->info('Appointment reminders processed.');

        return self::SUCCESS;
    }
}
