<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Slider;
use App\Models\Specialty;
use App\Services\Ads\AdService;
use App\Services\Booking\AvailabilityBadgeService;
use App\Services\Seo\SeoBuilder;
use App\Services\Settings\SettingService;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __construct(
        private readonly SeoBuilder $seo,
        private readonly AdService $ads,
        private readonly SettingService $settings,
        private readonly AvailabilityBadgeService $availability,
    ) {}

    public function __invoke(): View
    {
        $sliders = Slider::query()->active()->ordered()->limit(5)->get();

        $featuredDoctors = \App\Models\Doctor::query()
            ->with(['city', 'primarySpecialty'])
            ->visible()
            ->published()
            ->orderByDesc('is_vip')
            ->orderByDesc('published_at')
            ->limit(8)
            ->get();

        $specialties = Specialty::query()->ordered()->limit(12)->get();
        $cities = City::query()->ordered()->limit(12)->get();

        return view('home', [
            'seo' => $this->seo->forHome(),
            'sliders' => $sliders,
            'featuredDoctors' => $featuredDoctors,
            'availabilityBadges' => $this->availability->badgesFor($featuredDoctors),
            'specialties' => $specialties,
            'cities' => $cities,
            'heroAds' => $this->ads->forPlacement('home_hero', 1),
            'siteTagline' => $this->settings->get('site_tagline', 'معرفی پزشکان برتر ایران'),
        ]);
    }
}
