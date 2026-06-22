<?php

declare(strict_types=1);

namespace App\Services\Seo;

final readonly class SeoData
{
    /**
     * @param  list<array{label: string, url: string|null}>  $breadcrumbs
     * @param  list<array<string, mixed>>  $jsonLd
     */
    public function __construct(
        public string $title,
        public string $description,
        public string $canonical,
        public string $robots = 'index,follow',
        public ?string $ogImage = null,
        public string $ogType = 'website',
        public array $breadcrumbs = [],
        public array $jsonLd = [],
    ) {}

    public function hasBreadcrumbs(): bool
    {
        return count($this->breadcrumbs) > 1;
    }
}
