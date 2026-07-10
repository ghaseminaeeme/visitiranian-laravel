@extends('layouts.app')

@section('content')
    @php
        $specialty = $doctor->primarySpecialty?->name ?? $doctor->specialties->first()?->name;
        $rating = $doctor->reviews->avg('rating');
        $reviewCount = $doctor->reviews->count();
        $primaryPhone = $doctor->contactPhones->first()?->phone;
        $dayNames = [
            0 => 'یکشنبه',
            1 => 'دوشنبه',
            2 => 'سه‌شنبه',
            3 => 'چهارشنبه',
            4 => 'پنجشنبه',
            5 => 'جمعه',
            6 => 'شنبه',
        ];
        $schedulesByDay = $doctor->schedules->groupBy('day_of_week')->sortKeys();
        $availabilityLabel = match ($availability ?? null) {
            'today' => 'نوبت امروز موجود است',
            'tomorrow' => 'نوبت فردا موجود است',
            default => null,
        };
    @endphp

    <article itemscope itemtype="https://schema.org/Physician" class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        <meta itemprop="url" content="{{ route('doctors.show', $doctor) }}">
        @if ($doctor->hasPhoto())
            <meta itemprop="image" content="{{ $doctor->photoUrl() }}">
        @endif

        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        {{-- Hero --}}
        <header class="doctor-hero mb-6">
            <div class="relative px-5 py-8 sm:px-8 sm:py-10">
                <div class="relative flex flex-col items-center gap-6 sm:flex-row sm:items-end sm:gap-8">
                    <div class="relative shrink-0">
                        <x-doctor-photo :doctor="$doctor" size="profile" class="ring-4 ring-white/25 shadow-2xl" itemprop="image" />
                        @if ($doctor->is_vip)
                            <span class="badge-vip absolute -top-2 start-1/2 -translate-x-1/2 shadow-lg">
                                <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                VIP
                            </span>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1 text-center sm:text-start">
                        <div class="mb-3 flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                            @if ($availabilityLabel)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/90 px-3 py-1 text-xs font-bold text-white shadow-sm">
                                    <span class="size-1.5 animate-pulse rounded-full bg-white"></span>
                                    {{ $availabilityLabel }}
                                </span>
                            @endif
                            @if ($specialty)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white backdrop-blur-sm" itemprop="medicalSpecialty">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ $specialty }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-2xl font-black tracking-tight text-white text-balance md:text-4xl" itemprop="name">{{ $doctor->name }}</h1>

                        @if ($doctor->city)
                            <p class="mt-3 flex items-center justify-center gap-1.5 text-sm text-white/80 sm:justify-start">
                                <svg class="size-4 shrink-0 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>
                                    <a href="{{ route('cities.show', $doctor->city) }}" class="font-medium text-white underline decoration-white/30 underline-offset-4 transition hover:decoration-white">{{ $doctor->city->name }}</a>
                                    @if ($doctor->city->province)
                                        <span class="text-white/60"> — {{ $doctor->city->province->name }}</span>
                                    @endif
                                </span>
                            </p>
                        @endif

                        @if ($rating)
                            <div class="mt-4 flex items-center justify-center gap-2 sm:justify-start" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                                <meta itemprop="ratingValue" content="{{ number_format($rating, 1) }}">
                                <meta itemprop="reviewCount" content="{{ $reviewCount }}">
                                <meta itemprop="bestRating" content="5">
                                <div class="flex items-center gap-0.5" aria-label="امتیاز {{ number_format($rating, 1) }} از ۵">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="size-5 {{ $i <= round($rating) ? 'text-accent-400' : 'text-white/25' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <span class="text-sm font-bold text-white">{{ number_format($rating, 1) }}</span>
                                <a href="#reviews" class="text-sm text-white/65 underline decoration-white/25 underline-offset-2 hover:text-white">({{ $reviewCount }} نظر)</a>
                            </div>
                        @endif

                        <div class="mt-6 flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                            <a href="#booking" class="inline-flex items-center gap-2 rounded-xl bg-accent-500 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-accent-500/30 transition hover:-translate-y-0.5 hover:bg-accent-600">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                رزرو نوبت آنلاین
                            </a>
                            @if ($primaryPhone)
                                <a href="tel:{{ $primaryPhone }}" class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/20" dir="ltr" itemprop="telephone">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $primaryPhone }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick stats --}}
            <div class="grid grid-cols-3 divide-x divide-x-reverse divide-white/10 border-t border-white/10 bg-black/10">
                <div class="px-3 py-4 text-center">
                    <p class="text-xl font-black text-white">{{ $rating ? number_format($rating, 1) : '—' }}</p>
                    <p class="mt-0.5 text-xs text-white/60">امتیاز بیماران</p>
                </div>
                <div class="px-3 py-4 text-center">
                    <p class="text-xl font-black text-white">{{ $reviewCount }}</p>
                    <p class="mt-0.5 text-xs text-white/60">نظر ثبت‌شده</p>
                </div>
                <div class="px-3 py-4 text-center">
                    <p class="text-xl font-black text-white">{{ $schedulesByDay->count() ?: '—' }}</p>
                    <p class="mt-0.5 text-xs text-white/60">روز فعال در هفته</p>
                </div>
            </div>
        </header>

        {{-- Section nav --}}
        <nav class="doctor-nav-tabs" aria-label="بخش‌های پروفایل" x-data="{ active: 'about' }">
            <a href="#about" class="doctor-nav-tab" :class="active === 'about' && 'doctor-nav-tab-active'" @click="active = 'about'">درباره</a>
            @if ($schedulesByDay->isNotEmpty())
                <a href="#schedule" class="doctor-nav-tab" :class="active === 'schedule' && 'doctor-nav-tab-active'" @click="active = 'schedule'">ساعات کاری</a>
            @endif
            @if ($doctor->address || $doctor->contactPhones->isNotEmpty())
                <a href="#contact" class="doctor-nav-tab" :class="active === 'contact' && 'doctor-nav-tab-active'" @click="active = 'contact'">آدرس و تماس</a>
            @endif
            @if ($reviewCount > 0)
                <a href="#reviews" class="doctor-nav-tab" :class="active === 'reviews' && 'doctor-nav-tab-active'" @click="active = 'reviews'">نظرات</a>
            @endif
            <a href="#booking" class="doctor-nav-tab" :class="active === 'booking' && 'doctor-nav-tab-active'" @click="active = 'booking'">رزرو نوبت</a>
        </nav>

        <div class="grid gap-6 lg:grid-cols-3 lg:gap-8">
            <div class="space-y-6 lg:col-span-2">
                {{-- About --}}
                <section id="about" class="doctor-section scroll-mt-28">
                    <h2 class="doctor-section-title">
                        <span class="doctor-section-icon">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        درباره {{ $doctor->name }}
                    </h2>

                    @if ($doctor->bio)
                        <div class="prose-fa text-sm leading-7 sm:text-base" itemprop="description">{!! nl2br(e($doctor->bio)) !!}</div>
                    @else
                        <p class="text-sm leading-relaxed text-slate-500">
                            {{ $doctor->name }}@if($specialty)، {{ $specialty }}@endif@if($doctor->city) در {{ $doctor->city->name }}@endif.
                            برای رزرو نوبت آنلاین از فرم کنار صفحه استفاده کنید.
                        </p>
                    @endif

                    @if ($doctor->specialties->isNotEmpty())
                        <div class="mt-6">
                            <h3 class="mb-3 text-sm font-bold text-slate-800">تخصص‌ها</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($doctor->specialties as $spec)
                                    <a href="{{ route('specialties.show', $spec) }}" class="doctor-chip">
                                        <svg class="size-3.5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        {{ $spec->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($doctor->website)
                        <div class="mt-5">
                            <a href="{{ $doctor->website }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 transition hover:text-primary-800" itemprop="sameAs">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                وب‌سایت پزشک
                            </a>
                        </div>
                    @endif
                </section>

                {{-- Schedule --}}
                @if ($schedulesByDay->isNotEmpty())
                    <section id="schedule" class="doctor-section scroll-mt-28">
                        <h2 class="doctor-section-title">
                            <span class="doctor-section-icon-accent">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            ساعات کاری
                        </h2>
                        <p class="mb-5 text-sm text-slate-500">برنامه حضور در مطب — برای رزرو دقیق از فرم نوبت‌دهی استفاده کنید.</p>
                        <div class="space-y-2">
                            @foreach ($schedulesByDay as $day => $slots)
                                <div class="doctor-schedule-row">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-9 items-center justify-center rounded-lg bg-primary-100 text-xs font-black text-primary-700">
                                            {{ mb_substr($dayNames[$day] ?? '', 0, 1) }}
                                        </span>
                                        <span class="text-sm font-bold text-slate-800">{{ $dayNames[$day] ?? 'روز '.$day }}</span>
                                    </div>
                                    <div class="flex flex-wrap justify-end gap-2">
                                        @foreach ($slots as $slot)
                                            <span class="rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200" dir="ltr">
                                                {{ \Illuminate\Support\Str::of($slot->start_time)->substr(0, 5) }} – {{ \Illuminate\Support\Str::of($slot->end_time)->substr(0, 5) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Contact & address --}}
                @if ($doctor->address || $doctor->contactPhones->isNotEmpty() || $doctor->socialLinks->isNotEmpty() || $doctor->clinics->isNotEmpty())
                    <section id="contact" class="doctor-section scroll-mt-28" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                        <h2 class="doctor-section-title">
                            <span class="doctor-section-icon">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            آدرس و تماس
                        </h2>

                        @if ($doctor->address)
                            <div class="mb-6 rounded-2xl border border-primary-100 bg-primary-50/50 p-5">
                                <div class="mb-2 flex items-center gap-2 text-sm font-bold text-primary-800">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    آدرس مطب
                                </div>
                                <p class="text-sm leading-relaxed text-slate-700" itemprop="streetAddress">{{ $doctor->address }}</p>
                                @if ($doctor->city)
                                    <meta itemprop="addressLocality" content="{{ $doctor->city->name }}">
                                    @if ($doctor->city->province)
                                        <meta itemprop="addressRegion" content="{{ $doctor->city->province->name }}">
                                    @endif
                                @endif
                                <meta itemprop="addressCountry" content="IR">
                            </div>
                        @endif

                        @if ($doctor->contactPhones->isNotEmpty())
                            <h3 class="mb-3 text-sm font-bold text-slate-800">شماره‌های تماس</h3>
                            <ul class="mb-6 grid gap-3 sm:grid-cols-2">
                                @foreach ($doctor->contactPhones as $phone)
                                    <li>
                                        <a href="tel:{{ $phone->phone }}" class="doctor-contact-card" dir="ltr" @unless($loop->first && $primaryPhone) itemprop="telephone" @endunless>
                                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white shadow-md shadow-primary-600/20">
                                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            </span>
                                            <div class="min-w-0 text-start">
                                                <span class="block truncate font-bold text-slate-900">{{ $phone->phone }}</span>
                                                @if ($phone->label)
                                                    <span class="text-xs text-slate-400">{{ $phone->label }}</span>
                                                @endif
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($doctor->clinics->isNotEmpty())
                            <h3 class="mb-3 text-sm font-bold text-slate-800">کلینیک‌ها</h3>
                            <ul class="mb-6 space-y-2">
                                @foreach ($doctor->clinics as $clinic)
                                    <li class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                                        <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                        </span>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">{{ $clinic->name }}</p>
                                            @if ($clinic->address)
                                                <p class="mt-0.5 text-xs text-slate-500">{{ $clinic->address }}</p>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($doctor->socialLinks->isNotEmpty())
                            <h3 class="mb-3 text-sm font-bold text-slate-800">شبکه‌های اجتماعی</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($doctor->socialLinks as $link)
                                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="badge-soft transition hover:bg-primary-100" itemprop="sameAs">
                                        {{ $link->platform }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif

                {{-- Reviews --}}
                @if ($doctor->reviews->isNotEmpty())
                    <section id="reviews" class="doctor-section scroll-mt-28">
                        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <h2 class="doctor-section-title !mb-0">
                                <span class="doctor-section-icon-accent">
                                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </span>
                                نظرات بیماران
                            </h2>
                            @if ($rating)
                                <div class="flex items-center gap-2 rounded-2xl bg-accent-400/10 px-4 py-2 ring-1 ring-accent-400/30">
                                    <svg class="size-5 text-accent-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span class="text-lg font-black text-slate-900">{{ number_format($rating, 1) }}</span>
                                    <span class="text-xs text-slate-500">از ۵ · {{ $reviewCount }} نظر</span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            @foreach ($doctor->reviews as $review)
                                <blockquote class="doctor-review-card" itemprop="review" itemscope itemtype="https://schema.org/Review">
                                    <div class="mb-3 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-1 text-accent-500" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                                            <meta itemprop="ratingValue" content="{{ $review->rating }}">
                                            <meta itemprop="bestRating" content="5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="size-4 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-200' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                        @if ($review->author_name)
                                            <cite class="text-xs font-semibold not-italic text-slate-400" itemprop="author">{{ $review->author_name }}</cite>
                                        @endif
                                    </div>
                                    <p class="text-sm leading-relaxed text-slate-700" itemprop="reviewBody">{{ $review->body }}</p>
                                </blockquote>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Related doctors (mobile/tablet below content) --}}
                @if ($relatedDoctors->isNotEmpty())
                    <section class="doctor-section lg:hidden">
                        <h2 class="doctor-section-title">
                            <span class="doctor-section-icon">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            پزشکان مرتبط
                        </h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($relatedDoctors as $related)
                                <x-doctor-card :doctor="$related" compact />
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-6 lg:pb-8">
                <div id="booking" class="scroll-mt-28 lg:sticky lg:top-24">
                    @include('appointments.partials.book', ['doctor' => $doctor])
                </div>

                @if ($relatedDoctors->isNotEmpty())
                    <div class="doctor-section hidden !p-5 lg:block">
                        <h2 class="mb-4 flex items-center gap-2 text-base font-extrabold text-slate-900">
                            <span class="flex size-8 items-center justify-center rounded-lg bg-primary-50 text-primary-700">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            پزشکان مرتبط
                        </h2>
                        <div class="space-y-4">
                            @foreach ($relatedDoctors as $related)
                                <x-doctor-card :doctor="$related" compact class="!shadow-none" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <x-ad.placement placementKey="doctor_sidebar" :limit="1" />
            </aside>
        </div>

        {{-- Mobile sticky CTA --}}
        <div class="doctor-mobile-bar">
            <div class="mx-auto flex max-w-lg gap-2">
                @if ($primaryPhone)
                    <a href="tel:{{ $primaryPhone }}" class="btn-secondary flex-1 !py-3" aria-label="تماس با مطب">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        تماس
                    </a>
                @endif
                <a href="#booking" class="btn-accent flex-[2] !py-3">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    رزرو نوبت
                </a>
            </div>
        </div>
        <div class="h-20 lg:hidden" aria-hidden="true"></div>
    </article>
@endsection
