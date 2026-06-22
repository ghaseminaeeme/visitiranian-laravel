<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Services\Seo\SeoBuilder;
use Illuminate\Contracts\View\View;

final class CityController extends Controller
{
    public function __construct(
        private readonly SeoBuilder $seo,
    ) {}

    public function show(City $city): View
    {
        $city->load('province');

        $doctors = Doctor::query()
            ->with(['city', 'primarySpecialty'])
            ->visible()
            ->published()
            ->where('city_id', $city->id)
            ->orderByDesc('is_vip')
            ->orderBy('name')
            ->paginate(24);

        $specialties = Specialty::query()
            ->ordered()
            ->whereHas('doctors', fn ($q) => $q->visible()->published()->where('city_id', $city->id))
            ->limit(20)
            ->get();

        return view('cities.show', [
            'seo' => $this->seo->forCity($city, $doctors->currentPage()),
            'city' => $city,
            'doctors' => $doctors,
            'specialties' => $specialties,
        ]);
    }

    public function specialty(City $city, Specialty $specialty): View
    {
        $city->load('province');

        $doctors = Doctor::query()
            ->with(['city', 'primarySpecialty'])
            ->visible()
            ->published()
            ->where('city_id', $city->id)
            ->where(function ($q) use ($specialty): void {
                $q->where('primary_specialty_id', $specialty->id)
                    ->orWhereHas('specialties', fn ($q) => $q->where('specialties.id', $specialty->id));
            })
            ->orderByDesc('is_vip')
            ->orderBy('name')
            ->paginate(24);

        return view('cities.specialty', [
            'seo' => $this->seo->forCitySpecialty($city, $specialty, $doctors->currentPage()),
            'city' => $city,
            'specialty' => $specialty,
            'doctors' => $doctors,
        ]);
    }
}
