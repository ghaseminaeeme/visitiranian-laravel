<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DisplayTemplate;
use App\Models\Slider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SliderSeeder extends Seeder
{
    /** @var list<array<string, mixed>> */
    private array $slides = [
        [
            'title' => 'سلامتی شما، اولویت ماست',
            'subtitle' => 'رزرو آنلاین نوبت پزشکان برتر ایران',
            'cta_text' => 'جستجوی پزشک',
            'cta_url' => '/doctors',
            'image' => 'https://picsum.photos/seed/visitiranian-hero-1/1920/600',
            'sort_order' => 1,
        ],
        [
            'title' => 'پزشکان تأییدشده و معتبر',
            'subtitle' => 'بیش از صدها متخصص در سراسر کشور',
            'cta_text' => 'مشاهده پزشکان',
            'cta_url' => '/doctors',
            'image' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=1920&q=80',
            'sort_order' => 2,
        ],
        [
            'title' => 'نوبت‌دهی ۲۴ ساعته',
            'subtitle' => 'بدون تماس تلفنی، در هر ساعت از شبانه‌روز',
            'cta_text' => 'رزرو نوبت',
            'cta_url' => '/doctors',
            'image' => 'https://picsum.photos/seed/visitiranian-hero-3/1920/600',
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

        foreach ($this->slides as $index => $data) {
            $imagePath = $this->downloadImage('slide-'.($index + 1), $data['image']);

            Slider::query()->updateOrCreate(
                [
                    'template_id' => $template->id,
                    'title' => $data['title'],
                ],
                [
                    'subtitle' => $data['subtitle'],
                    'cta_text' => $data['cta_text'],
                    'cta_url' => $data['cta_url'],
                    'image_path' => $imagePath,
                    'sort_order' => $data['sort_order'],
                    'is_active' => true,
                ],
            );
        }
    }

    private function downloadImage(string $name, string $url): ?string
    {
        try {
            $contents = file_get_contents($url);

            if ($contents === false) {
                return $url;
            }

            $destination = 'sliders/'.$name.'.jpg';
            Storage::disk('public')->put($destination, $contents);

            return $destination;
        } catch (\Throwable) {
            return $url;
        }
    }
}
