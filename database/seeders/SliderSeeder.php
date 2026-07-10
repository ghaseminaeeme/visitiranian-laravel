<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DisplayTemplate;
use App\Models\Slider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SliderSeeder extends Seeder
{
    private const ASSETS_DIR = 'database/seeders/assets/sliders';

    /** @var list<array<string, mixed>> */
    private array $slides = [
        [
            'title' => 'سلامتی شما، اولویت ماست',
            'subtitle' => 'رزرو آنلاین نوبت پزشکان برتر ایران',
            'cta_text' => 'جستجوی پزشک',
            'cta_url' => '/doctors',
            'file' => 'slide-1.jpg',
            'fallback' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=1920&h=700&q=80',
            'sort_order' => 1,
        ],
        [
            'title' => 'پزشکان تأییدشده و معتبر',
            'subtitle' => 'متخصصان باتجربه در سراسر کشور',
            'cta_text' => 'مشاهده پزشکان',
            'cta_url' => '/doctors',
            'file' => 'slide-2.jpg',
            'fallback' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1920&h=700&q=80',
            'sort_order' => 2,
        ],
        [
            'title' => 'نوبت‌دهی ۲۴ ساعته',
            'subtitle' => 'بدون تماس تلفنی، در هر ساعت از شبانه‌روز',
            'cta_text' => 'رزرو نوبت',
            'cta_url' => '/doctors',
            'file' => 'slide-3.jpg',
            'fallback' => 'https://images.unsplash.com/photo-1584982751601-97dcc096659c?auto=format&fit=crop&w=1920&h=700&q=80',
            'sort_order' => 3,
        ],
    ];

    public function run(): void
    {
        $template = DisplayTemplate::query()->where('key', 'hero_banner')->first();

        if ($template === null) {
            return;
        }

        Storage::disk('public')->makeDirectory('sliders');

        foreach ($this->slides as $data) {
            $imagePath = $this->resolveImagePath($data['file'], $data['fallback']);

            Slider::query()->updateOrCreate(
                [
                    'template_id' => $template->id,
                    'sort_order' => $data['sort_order'],
                ],
                [
                    'title' => $data['title'],
                    'subtitle' => $data['subtitle'],
                    'cta_text' => $data['cta_text'],
                    'cta_url' => $data['cta_url'],
                    'image_path' => $imagePath,
                    'is_active' => true,
                ],
            );
        }
    }

    private function resolveImagePath(string $filename, string $fallbackUrl): string
    {
        $localAsset = base_path(self::ASSETS_DIR.'/'.$filename);

        if (File::exists($localAsset)) {
            $destination = 'sliders/'.$filename;
            Storage::disk('public')->put($destination, File::get($localAsset));

            return $destination;
        }

        $existing = Storage::disk('public')->path('sliders/'.$filename);
        if (File::exists($existing) && File::size($existing) > 10_000) {
            return 'sliders/'.$filename;
        }

        return $this->downloadImage($filename, $fallbackUrl) ?? $fallbackUrl;
    }

    private function downloadImage(string $filename, string $url): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'VisitIranian/1.0'])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $destination = 'sliders/'.$filename;
            Storage::disk('public')->put($destination, $response->body());

            File::ensureDirectoryExists(base_path(self::ASSETS_DIR));
            File::put(base_path(self::ASSETS_DIR.'/'.$filename), $response->body());

            return $destination;
        } catch (\Throwable) {
            return null;
        }
    }
}
