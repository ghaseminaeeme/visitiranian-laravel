<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;

class SmsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'event_key' => 'appointment.confirmed',
                'is_enabled' => true,
                'template_body' => '%patient_name% عزیز، نوبت شما نزد %doctor_name% در تاریخ %date% ساعت %time% ثبت شد. کد پیگیری: %tracking_code%',
            ],
            [
                'event_key' => 'appointment.cancelled',
                'is_enabled' => true,
                'template_body' => '%patient_name% عزیز، نوبت شما با کد %tracking_code% لغو شد.',
            ],
            [
                'event_key' => 'appointment.reminder_24h',
                'is_enabled' => true,
                'template_body' => 'یادآوری: فردا ساعت %time% نوبت شما نزد %doctor_name% است. کد: %tracking_code%',
            ],
            [
                'event_key' => 'appointment.reminder_2h',
                'is_enabled' => true,
                'template_body' => 'یادآوری: ۲ ساعت دیگر نوبت شما نزد %doctor_name% است. کد: %tracking_code%',
            ],
            [
                'event_key' => 'waitlist.notified',
                'is_enabled' => true,
                'template_body' => '%patient_name% عزیز، نوبت خالی برای %doctor_name% در تاریخ %date% موجود است.',
            ],
        ];

        foreach ($templates as $template) {
            SmsTemplate::query()->updateOrCreate(
                ['event_key' => $template['event_key']],
                $template,
            );
        }
    }
}
