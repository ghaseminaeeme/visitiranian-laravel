<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdPlacement;
use App\Models\Advertisement;
use App\Models\DisplayTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AdvertisementSeeder extends Seeder
{
    public function run(): void
    {
        $sidebarTemplate = DisplayTemplate::query()->where('key', 'sidebar_ad')->first();
        $sidebarPlacement = AdPlacement::query()->where('key', 'home_sidebar')->first();

        if ($sidebarTemplate === null || $sidebarPlacement === null) {
            return;
        }

        Storage::disk('public')->makeDirectory('ads');

        $imagePath = $this->downloadImage(
            'home-sidebar-promo',
            'https://images.pexels.com/photos/7579831/pexels-photo-7579831.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop',
        );

        Advertisement::query()->updateOrCreate(
            [
                'placement_id' => $sidebarPlacement->id,
                'title' => 'بیمه تکمیلی درمان',
            ],
            [
                'template_id' => $sidebarTemplate->id,
                'subtitle' => 'پوشش هزینه‌های درمانی با بهترین نرخ‌ها',
                'cta_text' => 'اطلاعات بیشتر',
                'cta_url' => '/doctors',
                'image_path' => $imagePath,
                'sort_order' => 1,
                'is_active' => true,
            ],
        );
    }

    private function downloadImage(string $name, string $url): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->withHeaders(['User-Agent' => 'VisitIranian/1.0'])
                ->get($url);

            if (! $response->successful()) {
                return $url;
            }

            $destination = 'ads/'.$name.'.jpg';
            Storage::disk('public')->put($destination, $response->body());

            return $destination;
        } catch (\Throwable) {
            return $url;
        }
    }
}
