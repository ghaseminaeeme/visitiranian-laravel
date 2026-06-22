<?php

declare(strict_types=1);

namespace App\Filament\Doctor\Support;

final class DoctorPanelOptions
{
    /**
     * Carbon dayOfWeek values (0 = Sunday … 6 = Saturday).
     *
     * @return array<int, string>
     */
    public static function daysOfWeek(): array
    {
        return [
            0 => 'یکشنبه',
            1 => 'دوشنبه',
            2 => 'سه‌شنبه',
            3 => 'چهارشنبه',
            4 => 'پنجشنبه',
            5 => 'جمعه',
            6 => 'شنبه',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function socialPlatforms(): array
    {
        return [
            'telegram' => 'تلگرام',
            'whatsapp' => 'واتساپ',
            'instagram' => 'اینستاگرام',
            'linkedin' => 'لینکدین',
            'bale' => 'بله',
            'eita' => 'ایتا',
            'aparat' => 'آپارات',
            'rubika' => 'روبیکا',
            'website' => 'وب‌سایت',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function appointmentStatuses(): array
    {
        return [
            'confirmed' => 'تأیید شده',
            'cancelled' => 'لغو شده',
            'completed' => 'انجام شده',
            'no_show' => 'عدم حضور',
        ];
    }
}
