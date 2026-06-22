<?php

declare(strict_types=1);

namespace App\Filament\Support;

use DateTimeInterface;
use Morilog\Jalali\Jalalian;

final class JalaliFormatter
{
    public static function date(?DateTimeInterface $date, string $format = 'Y/m/d'): ?string
    {
        if ($date === null) {
            return null;
        }

        return Jalalian::fromDateTime($date)->format($format);
    }

    public static function dateTime(?DateTimeInterface $date): ?string
    {
        return self::date($date, 'Y/m/d H:i');
    }
}
