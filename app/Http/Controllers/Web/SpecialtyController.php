<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Services\Seo\SeoBuilder;
use Illuminate\Contracts\View\View;

final class SpecialtyController extends Controller
{
    public function __construct(
        private readonly SeoBuilder $seo,
    ) {}

    public function show(Specialty $specialty): View
    {
        $doctors = Doctor::query()
            ->with(['city', 'primarySpecialty'])
            ->visible()
            ->published()
            ->where(function ($q) use ($specialty): void {
                $q->where('primary_specialty_id', $specialty->id)
                    ->orWhereHas('specialties', fn ($q) => $q->where('specialties.id', $specialty->id));
            })
            ->orderByDesc('is_vip')
            ->orderBy('name')
            ->paginate(24);

        return view('specialties.show', [
            'seo' => $this->seo->forSpecialty($specialty, $doctors->currentPage()),
            'specialty' => $specialty,
            'doctors' => $doctors,
        ]);
    }
}
