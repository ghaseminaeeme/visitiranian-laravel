<?php

declare(strict_types=1);

namespace App\Services\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

final class SettingService
{
    private const string CACHE_KEY = 'visitiranian.settings';

    private const int CACHE_TTL_SECONDS = 3600;

    public function get(string $key, mixed $default = null): mixed
    {
        $stored = $this->all()[$key] ?? null;

        if ($stored === null) {
            return $default;
        }

        if (is_array($stored) && array_key_exists('value', $stored)) {
            return $stored['value'];
        }

        return $stored;
    }

    public function set(string $key, mixed $value): void
    {
        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );

        $this->forgetCache();
    }

    public function forget(string $key): void
    {
        Setting::query()->where('key', $key)->delete();

        $this->forgetCache();
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        /** @var array<string, mixed> $settings */
        $settings = Cache::remember(
            self::CACHE_KEY,
            self::CACHE_TTL_SECONDS,
            static fn (): array => Setting::query()
                ->pluck('value', 'key')
                ->all(),
        );

        return $settings;
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
