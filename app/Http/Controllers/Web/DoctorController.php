<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Services\Booking\AvailabilityBadgeService;
use App\Services\Search\DoctorSearchService;
use App\Services\Seo\SeoBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class DoctorController extends Controller
{
    public function __construct(
        private readonly SeoBuilder $seo,
        private readonly DoctorSearchService $search,
        private readonly AvailabilityBadgeService $availability,
    ) {}

    public function index(Request $request): View
    {
        $query = trim((string) $request->input('q', ''));
        $cityId = $request->integer('city') ?: null;
        $specialtyId = $request->integer('specialty') ?: null;

        $doctors = $this->search->search(
            query: $query,
            cityId: $cityId,
            specialtyId: $specialtyId,
        );

        return view('doctors.index', [
            'seo' => $this->seo->forDoctorsIndex($query ?: null, $doctors->currentPage()),
            'doctors' => $doctors,
            'availabilityBadges' => $this->availability->badgesFor($doctors->getCollection()),
            'query' => $query,
            'cityId' => $cityId,
            'specialtyId' => $specialtyId,
            'cities' => City::query()->ordered()->get(),
            'specialties' => Specialty::query()->ordered()->get(),
        ]);
    }

    public function show(Doctor $doctor): View
    {
        abort_unless($doctor->is_published && $doctor->is_active, 404);

        $doctor->load([
            'city.province',
            'primarySpecialty',
            'specialties',
            'contactPhones' => fn ($q) => $q->visible()->ordered(),
            'socialLinks',
            'clinics',
            'schedules' => fn ($q) => $q->active(),
            'reviews' => fn ($q) => $q->approved()->latest()->limit(10),
        ]);

        $relatedDoctors = Doctor::query()
            ->with(['city', 'primarySpecialty'])
            ->withAvg(['reviews as reviews_avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->visible()
            ->published()
            ->where('id', '!=', $doctor->id)
            ->when($doctor->city_id, fn ($q) => $q->where('city_id', $doctor->city_id))
            ->when($doctor->primary_specialty_id, fn ($q) => $q->where('primary_specialty_id', $doctor->primary_specialty_id))
            ->orderByDesc('is_vip')
            ->limit(4)
            ->get();

        return view('doctors.show', [
            'seo' => $this->seo->forDoctor($doctor),
            'doctor' => $doctor,
            'relatedDoctors' => $relatedDoctors,
            'availability' => $this->availability->badgeFor($doctor),
        ]);
    }
}
