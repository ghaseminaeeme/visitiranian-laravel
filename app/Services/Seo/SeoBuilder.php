<?php

declare(strict_types=1);

namespace App\Services\Seo;

use App\Models\City;
use App\Models\Doctor;
use App\Models\Page;
use App\Models\Specialty;
use Illuminate\Support\Str;

final class SeoBuilder
{
    private string $siteName;

    private string $siteUrl;

    private ?string $defaultOgImage;

    public function __construct()
    {
        $this->siteName = (string) config('visitiranian.site_name', config('app.name', 'ویزیت ایرانیان'));
        $this->siteUrl = rtrim((string) config('app.url'), '/');
        $this->defaultOgImage = config('visitiranian.og_image');
    }

    public function forHome(): SeoData
    {
        $title = $this->siteName.' | معرفی پزشکان ایران';
        $description = 'جستجو و معرفی بهترین پزشکان ایران بر اساس تخصص و شهر. رزرو نوبت آنلاین و اطلاعات تماس پزشکان معتبر.';

        $jsonLd = [
            $this->organizationSchema(),
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $this->siteName,
                'url' => $this->siteUrl,
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => $this->siteUrl.route('doctors.index', [], false).'?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
        ];

        return new SeoData(
            title: $this->trimTitle($title),
            description: $this->trimDescription($description),
            canonical: route('home'),
            ogImage: $this->defaultOgImage,
            jsonLd: $jsonLd,
            breadcrumbs: [
                ['label' => 'خانه', 'url' => route('home')],
            ],
        );
    }

    public function forDoctor(Doctor $doctor): SeoData
    {
        $doctor->loadMissing([
            'city.province',
            'primarySpecialty',
            'specialties',
            'contactPhones' => fn ($q) => $q->visible()->ordered(),
            'reviews' => fn ($q) => $q->approved(),
        ]);

        $specialty = $doctor->primarySpecialty?->name
            ?? $doctor->specialties->first()?->name
            ?? 'پزشک';
        $city = $doctor->city?->name ?? '';

        $title = $doctor->meta_title
            ?: "{$doctor->name} | {$specialty} در {$city} | نوبت آنلاین";

        $shortBio = Str::limit(strip_tags((string) $doctor->bio), 120, '…');
        $description = $doctor->meta_description
            ?: "رزرو نوبت آنلاین {$doctor->name}، {$specialty} در {$city}. مشاهده آدرس مطب، نظرات بیماران و نوبت‌دهی. {$shortBio}";

        $url = route('doctors.show', $doctor);
        $image = $doctor->photo_path
            ? (Str::startsWith($doctor->photo_path, ['http://', 'https://']) ? $doctor->photo_path : asset('storage/'.$doctor->photo_path))
            : $this->defaultOgImage;

        $breadcrumbs = [
            ['label' => 'خانه', 'url' => route('home')],
            ['label' => 'پزشکان', 'url' => route('doctors.index')],
        ];

        if ($doctor->primarySpecialty) {
            $breadcrumbs[] = [
                'label' => $doctor->primarySpecialty->name,
                'url' => route('specialties.show', $doctor->primarySpecialty),
            ];
        }

        if ($doctor->city) {
            $breadcrumbs[] = [
                'label' => $doctor->city->name,
                'url' => route('cities.show', $doctor->city),
            ];
        }

        $breadcrumbs[] = ['label' => $doctor->name, 'url' => null];

        $physician = [
            '@context' => 'https://schema.org',
            '@type' => 'Physician',
            '@id' => $url.'#physician',
            'name' => $doctor->name,
            'url' => $url,
            'image' => $image,
            'medicalSpecialty' => $specialty,
            'description' => strip_tags((string) $doctor->bio) ?: "{$specialty} در {$city}",
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $doctor->address,
                'addressLocality' => $doctor->city?->name,
                'addressRegion' => $doctor->city?->province?->name,
                'addressCountry' => 'IR',
            ]),
        ];

        $phones = $doctor->contactPhones->pluck('phone')->filter()->values()->all();
        if ($phones !== []) {
            $physician['telephone'] = count($phones) === 1 ? $phones[0] : $phones;
        }

        if ($doctor->website) {
            $physician['sameAs'] = [$doctor->website];
        }

        $reviewCount = $doctor->reviews->count();
        $avgRating = $reviewCount > 0 ? round((float) $doctor->reviews->avg('rating'), 1) : null;
        if ($avgRating !== null && $reviewCount > 0) {
            $physician['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $avgRating,
                'reviewCount' => $reviewCount,
                'bestRating' => 5,
                'worstRating' => 1,
            ];
        }

        $jsonLd = [
            $this->organizationSchema(),
            $physician,
            $this->breadcrumbSchema($breadcrumbs),
        ];

        return new SeoData(
            title: $this->trimTitle($title),
            description: $this->trimDescription($description),
            canonical: $url,
            ogImage: $image,
            ogType: 'profile',
            breadcrumbs: $breadcrumbs,
            jsonLd: $jsonLd,
        );
    }

    public function forSpecialty(Specialty $specialty, ?int $page = null): SeoData
    {
        $title = $specialty->meta_title
            ?: "{$specialty->name} | معرفی پزشکان";
        $description = $specialty->meta_description
            ?: "لیست پزشکان {$specialty->name} در سراسر ایران. مشاهده پروفایل، آدرس مطب و رزرو نوبت آنلاین.";

        $url = route('specialties.show', $specialty);
        if ($page !== null && $page > 1) {
            $url = $url.'?page='.$page;
        }

        $breadcrumbs = [
            ['label' => 'خانه', 'url' => route('home')],
            ['label' => 'تخصص‌ها', 'url' => route('doctors.index')],
            ['label' => $specialty->name, 'url' => null],
        ];

        return new SeoData(
            title: $this->trimTitle($title),
            description: $this->trimDescription($description),
            canonical: $url,
            breadcrumbs: $breadcrumbs,
            jsonLd: [
                $this->organizationSchema(),
                $this->breadcrumbSchema($breadcrumbs),
            ],
        );
    }

    public function forCity(City $city, ?int $page = null): SeoData
    {
        $city->loadMissing('province');

        $title = $city->meta_title
            ?: "پزشک در {$city->name} | {$this->siteName}";
        $description = $city->meta_description
            ?: "لیست پزشکان {$city->name}. جستجو بر اساس تخصص، مشاهده آدرس مطب و رزرو نوبت آنلاین.";

        $url = route('cities.show', $city);
        if ($page !== null && $page > 1) {
            $url = $url.'?page='.$page;
        }

        $breadcrumbs = [
            ['label' => 'خانه', 'url' => route('home')],
            ['label' => 'شهرها', 'url' => route('doctors.index')],
            ['label' => $city->name, 'url' => null],
        ];

        return new SeoData(
            title: $this->trimTitle($title),
            description: $this->trimDescription($description),
            canonical: $url,
            breadcrumbs: $breadcrumbs,
            jsonLd: [
                $this->organizationSchema(),
                $this->breadcrumbSchema($breadcrumbs),
            ],
        );
    }

    public function forCitySpecialty(City $city, Specialty $specialty, ?int $page = null): SeoData
    {
        $title = "{$specialty->name} در {$city->name} | {$this->siteName}";
        $description = "بهترین {$specialty->name} در {$city->name}. مشاهده لیست پزشکان، آدرس مطب و رزرو نوبت آنلاین.";

        $url = route('cities.specialty', [$city, $specialty]);
        if ($page !== null && $page > 1) {
            $url = $url.'?page='.$page;
        }

        $breadcrumbs = [
            ['label' => 'خانه', 'url' => route('home')],
            ['label' => $city->name, 'url' => route('cities.show', $city)],
            ['label' => $specialty->name, 'url' => null],
        ];

        return new SeoData(
            title: $this->trimTitle($title),
            description: $this->trimDescription($description),
            canonical: $url,
            breadcrumbs: $breadcrumbs,
            jsonLd: [
                $this->organizationSchema(),
                $this->breadcrumbSchema($breadcrumbs),
            ],
        );
    }

    public function forDoctorsIndex(?string $query = null, ?int $page = null): SeoData
    {
        $title = $query
            ? "جستجوی «{$query}» | پزشکان"
            : "لیست پزشکان | {$this->siteName}";
        $description = $query
            ? "نتایج جستجو برای «{$query}» در میان پزشکان ثبت‌شده در {$this->siteName}."
            : 'جستجو و مشاهده لیست پزشکان بر اساس نام، تخصص و شهر.';

        $url = route('doctors.index', array_filter(['q' => $query, 'page' => $page && $page > 1 ? $page : null]));

        $breadcrumbs = [
            ['label' => 'خانه', 'url' => route('home')],
            ['label' => 'پزشکان', 'url' => null],
        ];

        return new SeoData(
            title: $this->trimTitle($title),
            description: $this->trimDescription($description),
            canonical: $url,
            breadcrumbs: $breadcrumbs,
            jsonLd: [
                $this->organizationSchema(),
                $this->breadcrumbSchema($breadcrumbs),
            ],
        );
    }

    public function forPage(Page $page): SeoData
    {
        $title = $page->meta_title ?: "{$page->title} | {$this->siteName}";
        $description = $page->meta_description
            ?: Str::limit(strip_tags((string) $page->body), 160, '…');

        $url = route('pages.show', $page);

        $breadcrumbs = [
            ['label' => 'خانه', 'url' => route('home')],
            ['label' => $page->title, 'url' => null],
        ];

        return new SeoData(
            title: $this->trimTitle($title),
            description: $this->trimDescription($description),
            canonical: $url,
            breadcrumbs: $breadcrumbs,
            jsonLd: [
                $this->organizationSchema(),
                $this->breadcrumbSchema($breadcrumbs),
            ],
        );
    }

    public function forAppointmentTrack(): SeoData
    {
        return new SeoData(
            title: $this->trimTitle("پیگیری نوبت | {$this->siteName}"),
            description: 'پیگیری وضعیت نوبت پزشکی با کد رهگیری.',
            canonical: route('appointments.track'),
            robots: 'noindex,nofollow',
        );
    }

    public function forAppointmentPeygiri(): SeoData
    {
        return new SeoData(
            title: $this->trimTitle("پیگیری نوبت با کد ملی | {$this->siteName}"),
            description: 'مشاهده نوبت‌های فعال با شماره موبایل و کد ملی.',
            canonical: route('peygiri'),
            robots: 'noindex,nofollow',
        );
    }

    /**
     * @param  list<array{label: string, url: string|null}>  $breadcrumbs
     * @return array<string, mixed>
     */
    private function breadcrumbSchema(array $breadcrumbs): array
    {
        $items = [];

        foreach ($breadcrumbs as $index => $crumb) {
            $item = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['label'],
            ];

            if ($crumb['url'] !== null) {
                $item['item'] = $crumb['url'];
            }

            $items[] = $item;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $this->siteName,
            'url' => $this->siteUrl,
            'logo' => $this->defaultOgImage,
        ];
    }

    private function trimTitle(string $title): string
    {
        return Str::limit($title, 60, '…');
    }

    private function trimDescription(string $description): string
    {
        $description = trim(preg_replace('/\s+/u', ' ', strip_tags($description)) ?? '');

        return Str::limit($description, 160, '…');
    }
}
