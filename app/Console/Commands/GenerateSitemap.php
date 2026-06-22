<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Doctor;
use App\Models\Page;
use App\Models\Specialty;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

final class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate sitemap.xml for public SEO pages';

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        $sitemap->add(
            Url::create(route('home'))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0),
        );

        $sitemap->add(
            Url::create(route('doctors.index'))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9),
        );

        Specialty::query()->ordered()->each(function (Specialty $specialty) use ($sitemap): void {
            $sitemap->add(
                Url::create(route('specialties.show', $specialty))
                    ->setLastModificationDate($specialty->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8),
            );
        });

        City::query()->ordered()->each(function (City $city) use ($sitemap): void {
            $sitemap->add(
                Url::create(route('cities.show', $city))
                    ->setLastModificationDate($city->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8),
            );

            Specialty::query()
                ->whereHas('doctors', fn ($q) => $q->visible()->published()->where('city_id', $city->id))
                ->each(function (Specialty $specialty) use ($sitemap, $city): void {
                    $sitemap->add(
                        Url::create(route('cities.specialty', [$city, $specialty]))
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7),
                    );
                });
        });

        Doctor::query()
            ->visible()
            ->published()
            ->orderBy('id')
            ->chunk(500, function ($doctors) use ($sitemap): void {
                foreach ($doctors as $doctor) {
                    $sitemap->add(
                        Url::create(route('doctors.show', $doctor))
                            ->setLastModificationDate($doctor->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.9),
                    );
                }
            });

        Page::query()
            ->published()
            ->each(function (Page $page) use ($sitemap): void {
                $sitemap->add(
                    Url::create(route('pages.show', $page))
                        ->setLastModificationDate($page->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.5),
                );
            });

        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $this->info("Sitemap written to {$path}");

        return self::SUCCESS;
    }
}
