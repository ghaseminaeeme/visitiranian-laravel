<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $citiesByProvince = [
            'tehran' => [
                ['name' => 'تهران', 'slug' => 'tehran', 'sort_order' => 1],
                ['name' => 'کرج', 'slug' => 'karaj', 'sort_order' => 2],
                ['name' => 'شهریار', 'slug' => 'shahriar', 'sort_order' => 3],
                ['name' => 'ورامین', 'slug' => 'varamin', 'sort_order' => 4],
            ],
            'isfahan' => [
                ['name' => 'اصفهان', 'slug' => 'isfahan', 'sort_order' => 1],
                ['name' => 'کاشان', 'slug' => 'kashan', 'sort_order' => 2],
                ['name' => 'نجف‌آباد', 'slug' => 'najafabad', 'sort_order' => 3],
            ],
            'fars' => [
                ['name' => 'شیراز', 'slug' => 'shiraz', 'sort_order' => 1],
                ['name' => 'مرودشت', 'slug' => 'marvdasht', 'sort_order' => 2],
            ],
            'khorasan-razavi' => [
                ['name' => 'مشهد', 'slug' => 'mashhad', 'sort_order' => 1],
                ['name' => 'نیشابور', 'slug' => 'neyshabur', 'sort_order' => 2],
            ],
            'east-azerbaijan' => [
                ['name' => 'تبریز', 'slug' => 'tabriz', 'sort_order' => 1],
            ],
            'khuzestan' => [
                ['name' => 'اهواز', 'slug' => 'ahvaz', 'sort_order' => 1],
            ],
            'mazandaran' => [
                ['name' => 'ساری', 'slug' => 'sari', 'sort_order' => 1],
            ],
            'gilan' => [
                ['name' => 'رشت', 'slug' => 'rasht', 'sort_order' => 1],
            ],
        ];

        foreach ($citiesByProvince as $provinceSlug => $cities) {
            $province = Province::query()->where('slug', $provinceSlug)->first();

            if ($province === null) {
                continue;
            }

            foreach ($cities as $city) {
                City::query()->updateOrCreate(
                    ['slug' => $city['slug']],
                    [
                        ...$city,
                        'province_id' => $province->id,
                    ],
                );
            }
        }
    }
}
