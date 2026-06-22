<?php

use App\Console\Commands\CheckExpirationsCommand;
use App\Console\Commands\SendRemindersCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendRemindersCommand::class)->everyFiveMinutes();
Schedule::command(CheckExpirationsCommand::class)->hourly();
