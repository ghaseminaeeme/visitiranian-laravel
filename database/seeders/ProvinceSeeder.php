<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            ['name' => 'تهران', 'slug' => 'tehran', 'sort_order' => 1],
            ['name' => 'اصفهان', 'slug' => 'isfahan', 'sort_order' => 2],
            ['name' => 'فارس', 'slug' => 'fars', 'sort_order' => 3],
            ['name' => 'خراسان رضوی', 'slug' => 'khorasan-razavi', 'sort_order' => 4],
            ['name' => 'آذربایجان شرقی', 'slug' => 'east-azerbaijan', 'sort_order' => 5],
            ['name' => 'خوزستان', 'slug' => 'khuzestan', 'sort_order' => 6],
            ['name' => 'مازندران', 'slug' => 'mazandaran', 'sort_order' => 7],
            ['name' => 'گیلان', 'slug' => 'gilan', 'sort_order' => 8],
        ];

        foreach ($provinces as $province) {
            Province::query()->updateOrCreate(
                ['slug' => $province['slug']],
                $province,
            );
        }
    }
}
