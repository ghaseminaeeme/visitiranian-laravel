<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DisplayTemplate;
use Illuminate\Database\Seeder;

class DisplayTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'hero_banner',
                'name' => 'بنر اصلی',
                'description' => 'اسلایدر بالای صفحه اصلی',
                'image_width' => 1920,
                'image_height' => 600,
                'layout_config' => ['position' => 'overlay-bottom'],
                'text_limits' => ['title' => 80, 'subtitle' => 120, 'cta_text' => 30],
                'is_active' => true,
            ],
            [
                'key' => 'sidebar_ad',
                'name' => 'تبلیغ سایدبار',
                'description' => 'بنر کناری صفحات لیست',
                'image_width' => 300,
                'image_height' => 250,
                'layout_config' => ['position' => 'stacked'],
                'text_limits' => ['title' => 60, 'subtitle' => 80, 'cta_text' => 20],
                'is_active' => true,
            ],
            [
                'key' => 'inline_card',
                'name' => 'کارت میان‌صفحه',
                'description' => 'تبلیغ در میان نتایج جستجو',
                'image_width' => 728,
                'image_height' => 90,
                'layout_config' => ['position' => 'inline'],
                'text_limits' => ['title' => 50, 'subtitle' => 70, 'cta_text' => 20],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            DisplayTemplate::query()->updateOrCreate(
                ['key' => $template['key']],
                $template,
            );
        }
    }
}
