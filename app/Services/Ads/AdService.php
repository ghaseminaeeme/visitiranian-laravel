<?php

declare(strict_types=1);

namespace App\Services\Ads;

use App\Models\Advertisement;
use Illuminate\Support\Collection;

final class AdService
{
    /**
     * @return Collection<int, Advertisement>
     */
    public function forPlacement(string $key, int $limit = 1): Collection
    {
        return Advertisement::query()
            ->with('template')
            ->whereHas('placement', fn ($q) => $q->where('key', $key)->active())
            ->active()
            ->ordered()
            ->limit($limit)
            ->get();
    }
}
