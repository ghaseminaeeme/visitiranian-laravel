@extends('layouts.app')

@php
    $specialtyGradients = [
        'from-primary-500 to-primary-700',
        'from-cyan-500 to-teal-600',
        'from-emerald-500 to-teal-700',
        'from-sky-500 to-blue-600',
        'from-violet-500 to-purple-600',
        'from-rose-500 to-pink-600',
        'from-amber-500 to-orange-600',
        'from-lime-500 to-green-600',
    ];
@endphp

@section('content')
    {{-- Hero --}}
    <section class="home-hero">
        @if ($sliders->isNotEmpty())
            <div
                x-data="heroSlider({{ $sliders->count() }})"
                x-init="start()"
                @mouseenter="stop()"
                @mouseleave="start()"
                class="absolute inset-0"
            >
                @foreach ($sliders as $index => $slide)
                    <x-hero-slide :slide="$slide" :index="$index" variant="background" />
                @endforeach
                @if ($sliders->count() > 1)
                    <div class="absolute bottom-8 start-1/2 z-20 flex -translate-x-1/2 gap-2">
                        @foreach ($sliders as $index => $slide)
                            <button
                                type="button"
                                class="h-2 rounded-full transition-all duration-300"
                                :class="current === {{ $index }} ? 'w-7 bg-white shadow-lg' : 'w-2 bg-white/40 hover:bg-white/70'"
                                @click="goTo({{ $index }})"
                                aria-label="اسلاید {{ $index + 1 }}"
                            ></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="home-hero-grid pointer-events-none absolute inset-0"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 pb-20 pt-12 sm:px-6 sm:pt-16 lg:px-8 lg:pb-28 lg:pt-20">
            <div class="mx-auto max-w-4xl text-center">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white/90 backdrop-blur-md">
                    <span class="relative flex size-2">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-accent-400 opacity-75"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-accent-400"></span>
                    </span>
                    نوبت‌دهی آنلاین ۲۴ ساعته
                </div>

                <h1 class="text-4xl font-black leading-[1.15] tracking-tight text-white text-balance sm:text-5xl lg:text-6xl">
                    {{ $siteTagline }}
                </h1>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-white/80 sm:text-lg">
                    جستجو در میان صدها پزشک متخصص، مشاهده نظرات واقعی بیماران و رزرو آنلاین نوبت — سریع، آسان و بدون معطلی.
                </p>
            </div>

            {{-- Search --}}
            <div class="relative z-20 mx-auto mt-10 max-w-3xl">
                <form action="{{ route('doctors.index') }}" method="GET" class="home-search-card">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-stretch">
                        <div class="relative flex-1">
                            <svg class="pointer-events-none absolute start-4 top-1/2 size-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                type="search"
                                name="q"
                                placeholder="نام پزشک، تخصص یا شهر را جستجو کنید…"
                                class="home-search-input"
                                autocomplete="off"
                            >
                        </div>
                        <button type="submit" class="btn-primary shrink-0 !rounded-2xl !px-8 !py-4 text-base">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            جستجو
                        </button>
                    </div>
                </form>

                @if ($specialties->isNotEmpty())
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                        <span class="text-sm text-white/50">پرجستجو:</span>
                        @foreach ($specialties->take(6) as $specialty)
                            <a href="{{ route('specialties.show', $specialty) }}" class="home-tag">{{ $specialty->name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Stats --}}
            <div class="mx-auto mt-14 grid max-w-4xl grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                <div class="home-stat-card text-center">
                    <p class="text-3xl font-black text-white sm:text-4xl">{{ number_format($doctorCount) }}+</p>
                    <p class="mt-1 text-xs text-white/65 sm:text-sm">پزشک فعال</p>
                </div>
                <div class="home-stat-card text-center">
                    <p class="text-3xl font-black text-white sm:text-4xl">{{ $specialties->count() }}+</p>
                    <p class="mt-1 text-xs text-white/65 sm:text-sm">تخصص پزشکی</p>
                </div>
                <div class="home-stat-card text-center">
                    <p class="text-3xl font-black text-white sm:text-4xl">{{ $cities->count() }}+</p>
                    <p class="mt-1 text-xs text-white/65 sm:text-sm">شهر تحت پوشش</p>
                </div>
                <div class="home-stat-card text-center">
                    <p class="text-3xl font-black text-white sm:text-4xl">۲۴/۷</p>
                    <p class="mt-1 text-xs text-white/65 sm:text-sm">رزرو آنلاین</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Quick actions --}}
    <section class="relative z-20 -mt-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-4 sm:grid-cols-3">
            <a href="{{ route('doctors.index') }}" class="home-quick-card group">
                <span class="home-quick-card-icon">
                    <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">جستجوی پزشک</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-500">بر اساس نام، تخصص یا شهر پزشک مناسب را پیدا کنید.</p>
                </div>
                <span class="mt-auto inline-flex items-center gap-1 text-sm font-bold text-primary-600 transition group-hover:gap-2">
                    شروع جستجو
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            </a>
            <a href="{{ route('peygiri') }}" class="home-quick-card group">
                <span class="home-quick-card-icon !from-accent-400 !to-accent-600 !shadow-accent-500/25">
                    <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </span>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">پیگیری نوبت</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-500">وضعیت نوبت خود را با شماره موبایل بررسی کنید.</p>
                </div>
                <span class="mt-auto inline-flex items-center gap-1 text-sm font-bold text-accent-600 transition group-hover:gap-2">
                    پیگیری نوبت
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            </a>
            <a href="{{ route('appointments.track') }}" class="home-quick-card group">
                <span class="home-quick-card-icon !from-sky-500 !to-blue-600 !shadow-sky-500/25">
                    <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                </span>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">کد رهگیری</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-500">با کد رهگیری، جزئیات نوبت خود را مشاهده کنید.</p>
                </div>
                <span class="mt-auto inline-flex items-center gap-1 text-sm font-bold text-sky-600 transition group-hover:gap-2">
                    جستجو با کد
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            </a>
        </div>
    </section>

    {{-- Trust bar --}}
    <section class="mx-auto max-w-7xl px-4 pt-16 sm:px-6 lg:px-8">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="home-trust-item">
                <span class="icon-badge !size-12 !rounded-2xl">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </span>
                <div>
                    <p class="font-bold text-slate-900">پزشکان تأییدشده</p>
                    <p class="mt-1 text-sm text-slate-500">اطلاعات معتبر و بررسی‌شده توسط تیم ما</p>
                </div>
            </div>
            <div class="home-trust-item">
                <span class="icon-badge !size-12 !rounded-2xl">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                <div>
                    <p class="font-bold text-slate-900">رزرو سریع نوبت</p>
                    <p class="mt-1 text-sm text-slate-500">ثبت نوبت در کمتر از یک دقیقه، بدون تماس</p>
                </div>
            </div>
            <div class="home-trust-item">
                <span class="icon-badge !size-12 !rounded-2xl">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                </span>
                <div>
                    <p class="font-bold text-slate-900">نظرات واقعی بیماران</p>
                    <p class="mt-1 text-sm text-slate-500">تجربه مراجعان پیشین برای انتخاب بهتر</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured doctors --}}
    @if ($featuredDoctors->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="eyebrow">منتخب سردبیر</span>
                    <h2 class="section-title mt-3">پزشکان برتر</h2>
                    <p class="section-subtitle">پزشکان ویژه و پرطرفدار با بالاترین امتیاز بیماران</p>
                </div>
                <a href="{{ route('doctors.index') }}" class="btn-outline shrink-0">
                    مشاهده همه
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            </div>
            <div class="home-doctors-scroll">
                @foreach ($featuredDoctors as $doctor)
                    <x-doctor-card :doctor="$doctor" :availability="$availabilityBadges[$doctor->id] ?? null" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- How it works --}}
    <section class="bg-surface-100/80 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <span class="eyebrow">ساده و سریع</span>
                <h2 class="section-title mt-3">در سه گام نوبت بگیرید</h2>
                <p class="section-subtitle mx-auto">فرآیند رزرو نوبت آنلاین بدون تماس تلفنی و اتلاف وقت</p>
            </div>
            <div class="grid gap-6 md:grid-cols-3">
                <div class="home-step-card">
                    <div class="flex items-center gap-3">
                        <span class="home-step-number">۱</span>
                        <div class="h-px flex-1 bg-gradient-to-l from-primary-200 to-transparent"></div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">جستجوی پزشک</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">بر اساس نام، تخصص یا شهر، پزشک مناسب خود را پیدا کنید.</p>
                    </div>
                </div>
                <div class="home-step-card">
                    <div class="flex items-center gap-3">
                        <span class="home-step-number">۲</span>
                        <div class="h-px flex-1 bg-gradient-to-l from-primary-200 to-transparent"></div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">بررسی پروفایل</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">نظرات بیماران، آدرس مطب و اطلاعات تماس پزشک را مشاهده کنید.</p>
                    </div>
                </div>
                <div class="home-step-card">
                    <div class="flex items-center gap-3">
                        <span class="home-step-number !bg-accent-500 !shadow-accent-500/25">۳</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">رزرو نوبت</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">تاریخ و ساعت دلخواه را انتخاب و نوبت خود را آنلاین ثبت کنید.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Specialties --}}
    @if ($specialties->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="eyebrow">تخصص‌ها</span>
                    <h2 class="section-title mt-3">تخصص‌های پزشکی</h2>
                    <p class="section-subtitle">انتخاب تخصص و مشاهده پزشکان مرتبط</p>
                </div>
                <a href="{{ route('doctors.index') }}" class="btn-ghost shrink-0 text-primary-700">
                    همه تخصص‌ها
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($specialties as $index => $specialty)
                    <a href="{{ route('specialties.show', $specialty) }}" class="home-specialty-card group">
                        <span class="home-specialty-icon bg-gradient-to-br {{ $specialtyGradients[$index % count($specialtyGradients)] }}">
                            {{ mb_substr($specialty->name, 0, 1) }}
                        </span>
                        <div class="flex-1">
                            <h3 class="font-bold text-slate-900 transition group-hover:text-primary-700">{{ $specialty->name }}</h3>
                            @if ($specialty->doctors_count > 0)
                                <p class="mt-1 text-xs text-slate-500">{{ number_format($specialty->doctors_count) }} پزشک</p>
                            @endif
                        </div>
                        <svg class="size-5 text-slate-300 transition group-hover:-translate-x-1 group-hover:text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="home-section-divider mx-auto max-w-5xl"></div>

    {{-- Cities --}}
    @if ($cities->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="mb-10">
                <span class="eyebrow">شهرها</span>
                <h2 class="section-title mt-3">جستجو بر اساس شهر</h2>
                <p class="section-subtitle">پزشکان در شهرهای مختلف ایران</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($cities as $city)
                    <a href="{{ route('cities.show', $city) }}" class="home-city-card group">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-700 ring-1 ring-primary-100 transition group-hover:bg-primary-600 group-hover:text-white group-hover:ring-primary-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-bold text-slate-900 transition group-hover:text-primary-700">{{ $city->name }}</p>
                            @if ($city->province)
                                <p class="mt-0.5 text-xs text-slate-500">{{ $city->province->name }}</p>
                            @endif
                        </div>
                        @if ($city->doctors_count > 0)
                            <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 transition group-hover:bg-primary-100 group-hover:text-primary-700">
                                {{ number_format($city->doctors_count) }} پزشک
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- FAQ --}}
    <section class="bg-surface-100/80 py-20" x-data="{ open: null }">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <span class="eyebrow">سوالات متداول</span>
                <h2 class="section-title mt-3">پاسخ سوالات شما</h2>
                <p class="section-subtitle mx-auto">هر آنچه برای رزرو نوبت آنلاین نیاز دارید</p>
            </div>
            <div class="space-y-3">
                @foreach ([
                    ['q' => 'چگونه نوبت آنلاین بگیرم؟', 'a' => 'نام پزشک، تخصص یا شهر را جستجو کنید، پروفایل پزشک را مشاهده کنید و تاریخ و ساعت دلخواه را انتخاب نمایید.'],
                    ['q' => 'آیا رزرو نوبت رایگان است؟', 'a' => 'بله، جستجو و رزرو نوبت در ویزیت ایرانیان کاملاً رایگان است. هزینه ویزیت مطابق تعرفه مطب پزشک دریافت می‌شود.'],
                    ['q' => 'چگونه نوبت خود را پیگیری کنم؟', 'a' => 'از بخش «پیگیری نوبت» با شماره موبایل یا از «کد رهگیری» با کد دریافتی، وضعیت نوبت خود را بررسی کنید.'],
                    ['q' => 'آیا نظرات بیماران واقعی هستند؟', 'a' => 'بله، تمامی نظرات توسط بیمارانی ثبت شده که واقعاً از خدمات پزشک استفاده کرده‌اند و پس از تأیید نمایش داده می‌شوند.'],
                ] as $i => $faq)
                    <div class="home-faq-item">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-4 text-start"
                            @click="open = open === {{ $i }} ? null : {{ $i }}"
                            :aria-expanded="open === {{ $i }}"
                        >
                            <span class="font-bold text-slate-900">{{ $faq['q'] }}</span>
                            <svg class="size-5 shrink-0 text-primary-600 transition" :class="open === {{ $i }} && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div
                            x-show="open === {{ $i }}"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            x-cloak
                            class="mt-3 text-sm leading-relaxed text-slate-500"
                        >
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="home-cta-panel flex flex-col items-center gap-8 text-center md:flex-row md:justify-between md:text-start">
            <div class="relative z-10 max-w-xl">
                <h2 class="text-3xl font-black text-balance md:text-4xl">همین حالا نوبت پزشک خود را رزرو کنید</h2>
                <p class="mt-4 text-base leading-relaxed text-white/85">بدون تماس تلفنی، بدون معطلی. پزشک مناسب را پیدا کنید و آنلاین نوبت بگیرید.</p>
            </div>
            <div class="relative z-10 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('doctors.index') }}" class="btn-accent !px-8 !py-3.5 text-base">شروع جستجو</a>
                <a href="{{ route('peygiri') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/25 bg-white/10 px-8 py-3.5 text-base font-semibold text-white backdrop-blur-sm transition hover:bg-white/20">
                    پیگیری نوبت
                </a>
            </div>
        </div>
    </section>

    {{-- Ad placement --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        <x-ad.placement placementKey="home_sidebar" :limit="1" />
    </section>
@endsection
