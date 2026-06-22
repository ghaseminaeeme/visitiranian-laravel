<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Helpers\PersianTextHelper;
use App\Models\Doctor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class DoctorSearchService
{
    public function normalizeQuery(string $query): string
    {
        return PersianTextHelper::normalize($query);
    }

    public function buildSearchText(Doctor $doctor): string
    {
        $parts = array_filter([
            $doctor->name,
            $doctor->bio,
            $doctor->address,
            $doctor->city?->name,
            $doctor->primarySpecialty?->name,
        ]);

        foreach ($doctor->specialties as $specialty) {
            $parts[] = $specialty->name;
        }

        return PersianTextHelper::normalize(implode(' ', $parts));
    }

    /**
     * @return LengthAwarePaginator<int, Doctor>
     */
    public function search(
        string $query,
        ?int $cityId = null,
        ?int $specialtyId = null,
        int $perPage = 24,
    ): LengthAwarePaginator {
        $normalizedQuery = $this->normalizeQuery($query);

        return Doctor::query()
            ->with(['city', 'primarySpecialty', 'specialties'])
            ->visible()
            ->published()
            ->when($cityId !== null, fn (Builder $builder): Builder => $builder->where('city_id', $cityId))
            ->when(
                $specialtyId !== null,
                fn (Builder $builder): Builder => $builder->where(function (Builder $builder) use ($specialtyId): void {
                    $builder->where('primary_specialty_id', $specialtyId)
                        ->orWhereHas(
                            'specialties',
                            fn (Builder $builder): Builder => $builder->where('specialties.id', $specialtyId),
                        );
                }),
            )
            ->when(
                $normalizedQuery !== '',
                fn (Builder $builder): Builder => $builder->where(function (Builder $builder) use ($normalizedQuery, $query): void {
                    $builder->where('name_normalized', 'like', '%'.$normalizedQuery.'%')
                        ->orWhere('search_text', 'like', '%'.$normalizedQuery.'%')
                        ->orWhere('name', 'like', '%'.$query.'%');

                    if (mb_strlen($normalizedQuery) >= 3) {
                        $builder->orWhereFullText(['name', 'search_text'], $normalizedQuery);
                    }
                }),
            )
            ->orderByDesc('is_vip')
            ->orderBy('name')
            ->paginate($perPage);
    }
}
