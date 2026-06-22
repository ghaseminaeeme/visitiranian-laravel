<?php

declare(strict_types=1);

namespace App\Services\ShortLink;

use App\Models\Appointment;
use App\Models\ShortLink;
use Carbon\Carbon;
use Illuminate\Support\Str;

final class ShortLinkService
{
    public function create(
        string $targetUrl,
        ?int $appointmentId = null,
        ?Carbon $expiresAt = null,
    ): ShortLink {
        do {
            $code = $this->generateCode();
        } while (ShortLink::query()->where('code', $code)->exists());

        return ShortLink::query()->create([
            'code' => $code,
            'target_url' => $targetUrl,
            'appointment_id' => $appointmentId,
            'expires_at' => $expiresAt,
        ]);
    }

    public function forAppointment(Appointment $appointment, ?Carbon $expiresAt = null): ShortLink
    {
        $existing = ShortLink::query()
            ->where('appointment_id', $appointment->id)
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        $targetUrl = url('/peygiri?code='.$appointment->tracking_code);

        return $this->create(
            targetUrl: $targetUrl,
            appointmentId: $appointment->id,
            expiresAt: $expiresAt ?? $appointment->starts_at->copy()->addDay(),
        );
    }

    public function resolve(string $code): ?ShortLink
    {
        $link = ShortLink::query()->where('code', $code)->first();

        if ($link === null) {
            return null;
        }

        if ($link->expires_at !== null && $link->expires_at->isPast()) {
            return null;
        }

        return $link;
    }

    public function recordClick(ShortLink $link): void
    {
        $link->increment('clicks_count');
    }

    private function generateCode(): string
    {
        return Str::lower(Str::random(8));
    }
}
