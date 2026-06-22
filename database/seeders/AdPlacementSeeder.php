<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdPlacement;
use Illuminate\Database\Seeder;

class AdPlacementSeeder extends Seeder
{
    public function run(): void
    {
        $placements = [
            [
                'key' => 'home_hero',
                'name' => 'اسلایدر صفحه اصلی',
                'description' => 'بالای صفحه اصلی',
                'allowed_template_keys' => ['hero_banner'],
                'is_active' => true,
            ],
            [
                'key' => 'listing_sidebar',
                'name' => 'سایدبار لیست پزشکان',
                'description' => 'کنار نتایج جستجو و لیست‌ها',
                'allowed_template_keys' => ['sidebar_ad'],
                'is_active' => true,
            ],
            [
                'key' => 'listing_inline',
                'name' => 'میان نتایج',
                'description' => 'بین کارت‌های پزشک در لیست',
                'allowed_template_keys' => ['inline_card'],
                'is_active' => true,
            ],
        ];

        foreach ($placements as $placement) {
            AdPlacement::query()->updateOrCreate(
                ['key' => $placement['key']],
                $placement,
            );
        }
    }
}
