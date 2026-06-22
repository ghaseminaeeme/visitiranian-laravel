<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'ویزیت ایرانیان',
            'site_tagline' => 'معرفی پزشکان سراسر ایران',
            'contact_email' => config('visitiranian.support.email'),
            'contact_phone' => config('visitiranian.support.phone'),
            'appointment_enabled' => true,
            'reviews_enabled' => false,
            'default_meta_title' => 'ویزیت ایرانیان | معرفی پزشکان',
            'default_meta_description' => 'جستجو و معرفی پزشکان بر اساس تخصص و شهر',
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }
    }
}
