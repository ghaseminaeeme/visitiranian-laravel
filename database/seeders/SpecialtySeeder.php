<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            ['name' => 'متخصص قلب و عروق', 'slug' => 'cardiologist', 'sort_order' => 1],
            ['name' => 'متخصص پوست و مو', 'slug' => 'dermatologist', 'sort_order' => 2],
            ['name' => 'متخصص اطفال', 'slug' => 'pediatrician', 'sort_order' => 3],
            ['name' => 'متخصص زنان و زایمان', 'slug' => 'gynecologist', 'sort_order' => 4],
            ['name' => 'متخصص داخلی', 'slug' => 'internist', 'sort_order' => 5],
            ['name' => 'متخصص ارتوپدی', 'slug' => 'orthopedist', 'sort_order' => 6],
            ['name' => 'متخصص چشم', 'slug' => 'ophthalmologist', 'sort_order' => 7],
            ['name' => 'متخصص گوش و حلق و بینی', 'slug' => 'ent-specialist', 'sort_order' => 8],
            ['name' => 'متخصص مغز و اعصاب', 'slug' => 'neurologist', 'sort_order' => 9],
            ['name' => 'متخصص روانپزشک', 'slug' => 'psychiatrist', 'sort_order' => 10],
            ['name' => 'متخصص دندانپزشک', 'slug' => 'dentist', 'sort_order' => 11],
            ['name' => 'متخصص اورولوژی', 'slug' => 'urologist', 'sort_order' => 12],
        ];

        foreach ($specialties as $specialty) {
            Specialty::query()->updateOrCreate(
                ['slug' => $specialty['slug']],
                $specialty,
            );
        }
    }
}
