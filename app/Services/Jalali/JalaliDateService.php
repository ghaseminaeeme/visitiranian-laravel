<?php

declare(strict_types=1);

namespace App\Services\Jalali;

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

final class JalaliDateService
{
    public function toJalali(Carbon $date): Jalalian
    {
        return Jalalian::fromCarbon($date);
    }

    public function formatDate(Carbon $date, string $format = 'Y/m/d'): string
    {
        return $this->toJalali($date)->format($format);
    }

    public function formatTime(Carbon $date, string $format = 'H:i'): string
    {
        return $this->toJalali($date)->format($format);
    }

    public function formatDateTime(Carbon $date, string $format = 'Y/m/d H:i'): string
    {
        return $this->toJalali($date)->format($format);
    }

    public function formatForSms(Carbon $date): string
    {
        return $this->formatDateTime($date);
    }
}
