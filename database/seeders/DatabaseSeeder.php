<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            ProvinceSeeder::class,
            CitySeeder::class,
            SpecialtySeeder::class,
            DisplayTemplateSeeder::class,
            AdPlacementSeeder::class,
            SettingSeeder::class,
            SmsTemplateSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
            PageSeeder::class,
        ]);
    }
}
