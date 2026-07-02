@extends('layouts.app')

@section('content')
    {{-- Hero --}}
    @if ($sliders->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
            <div
                x-data="heroSlider({{ $sliders->count() }})"
                x-init="start()"
                @mouseenter="stop()"
                @mouseleave="start()"
                class="relative"
            >
                <div class="relative overflow-hidden rounded-3xl shadow-2xl shadow-primary-900/20">
                    @foreach ($sliders as $index => $slide)
                        <x-hero-slide :slide="$slide" :index="$index" />
                    @endforeach
                </div>
                @if ($sliders->count() > 1)
                    <div class="absolute bottom-6 start-1/2 flex -translate-x-1/2 gap-2">
                        @foreach ($sliders as $index => $slide)
                            <button
                                type="button"
                                class="h-2.5 rounded-full transition-all duration-300"
                                :class="current === {{ $index }} ? 'w-8 bg-white shadow-lg' : 'w-2.5 bg-white/40 hover:bg-white/70'"
                                @click="goTo({{ $index }})"
                                aria-label="اسلاید {{ $index + 1 }}"
                            ></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @else
        <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
            <div class="page-hero">
                <div class="relative z-10 grid gap-8 lg:grid-cols-2 lg:items-center">
                    <div>
                        <span class="stat-pill mb-4">
                            <span class="size-2 rounded-full bg-accent-400"></span>
                            پلتفرم معتبر معرفی پزشکان
                        </span>
                        <h1 class="text-3xl font-extrabold leading-tight text-balance md:text-5xl">{{ $siteTagline }}</h1>
                        <p class="mt-4 max-w-xl text-base text-white/85 md:text-lg">
                            جستجو در میان صدها پزشک متخصص در سراسر ایران — رزرو نوبت آنلاین، مشاهده نظرات بیماران و آدرس مطب
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('doctors.index') }}" class="btn-accent">شروع جستجو</a>
                            <a href="{{ route('doctors.index') }}" class="btn-outline !border-white/30 !bg-white/10 !text-white hover:!bg-white/20">مشاهده پزشکان</a>
                        </div>
                    </div>
                    <div class="hidden lg:block">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm transition hover:bg-white/15">
                                <p class="text-3xl font-extrabold">{{ $doctorCount }}+</p>
                                <p class="mt-1 text-sm text-white/75">پزشک فعال</p>
                            </div>
                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm transition hover:bg-white/15">
                                <p class="text-3xl font-extrabold">{{ $specialties->count() }}</p>
                                <p class="mt-1 text-sm text-white/75">تخصص پزشکی</p>
                            </div>
                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm transition hover:bg-white/15">
                                <p class="text-3xl font-extrabold">{{ $cities->count() }}+</p>
                                <p class="mt-1 text-sm text-white/75">شهر</p>
                            </div>
                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm transition hover:bg-white/15">
                                <p class="text-3xl font-extrabold">۲۴/۷</p>
                                <p class="mt-1 text-sm text-white/75">رزرو آنلاین</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Search --}}
    <section class="mx-auto max-w-7xl px-4 pt-12 sm:px-6 lg:px-8">
        <div class="filter-panel mx-auto max-w-4xl">
            <div class="mb-5 text-center">
                <h2 class="text-xl font-bold text-slate-900">پزشک مورد نظرتان را پیدا کنید</h2>
                <p class="mt-1 text-sm text-slate-500">جستجو بر اساس نام، تخصص یا شهر</p>
            </div>
            <form action="{{ route('doctors.index') }}" method="GET" class="grid gap-3 sm:grid-cols-[1fr_auto]">
                <div class="relative">
                    <svg class="pointer-events-none absolute start-4 top-1/2 size-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="search"
                        name="q"
                        placeholder="نام پزشک، تخصص یا شهر…"
                        class="input-field !py-3 !ps-12"
                        autocomplete="off"
                    >
                </div>
                <button type="submit" class="btn-primary !py-3">جستجو</button>
            </form>
            @if ($specialties->isNotEmpty())
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-sm">
                    <span class="text-slate-400">جستجوی پرطرفدار:</span>
                    @foreach ($specialties->take(5) as $specialty)
                        <a href="{{ route('specialties.show', $specialty) }}" class="rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700 transition hover:bg-primary-100">{{ $specialty->name }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- How it works --}}
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <span class="eyebrow">ساده و سریع</span>
            <h2 class="section-title mt-3">در سه گام نوبت بگیرید</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            <div class="info-tile flex-col items-start !p-6">
                <span class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 text-lg font-black text-white shadow-lg shadow-primary-700/25">۱</span>
                <h3 class="text-base font-bold text-slate-900">جستجوی پزشک</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">بر اساس نام، تخصص یا شهر، پزشک مناسب خود را پیدا کنید.</p>
            </div>
            <div class="info-tile flex-col items-start !p-6">
                <span class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 text-lg font-black text-white shadow-lg shadow-primary-700/25">۲</span>
                <h3 class="text-base font-bold text-slate-900">بررسی پروفایل</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">نظرات بیماران، آدرس مطب و اطلاعات تماس پزشک را مشاهده کنید.</p>
            </div>
            <div class="info-tile flex-col items-start !p-6">
                <span class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-accent-400 to-accent-600 text-lg font-black text-primary-950 shadow-lg shadow-accent-500/25">۳</span>
                <h3 class="text-base font-bold text-slate-900">رزرو نوبت</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">تاریخ و ساعت دلخواه را انتخاب و نوبت خود را آنلاین ثبت کنید.</p>
            </div>
        </div>
    </section>

    {{-- Featured doctors --}}
    @if ($featuredDoctors->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-14 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="eyebrow">منتخب سردبیر</span>
                    <h2 class="section-title mt-3">پزشکان برتر</h2>
                    <p class="section-subtitle">پزشکان VIP و پرطرفدار با بالاترین امتیاز بیماران</p>
                </div>
                <a href="{{ route('doctors.index') }}" class="btn-outline shrink-0">
                    مشاهده همه پزشکان
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featuredDoctors as $doctor)
                    <x-doctor-card :doctor="$doctor" :availability="$availabilityBadges[$doctor->id] ?? null" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Specialties --}}
    @if ($specialties->isNotEmpty())
        <section class="border-y border-primary-100/60 bg-white/60 py-14 backdrop-blur-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 text-center">
                    <span class="eyebrow">تخصص‌ها</span>
                    <h2 class="section-title mt-3">تخصص‌های پزشکی</h2>
                    <p class="section-subtitle mx-auto">انتخاب تخصص و مشاهده پزشکان مرتبط</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($specialties as $specialty)
                        <a href="{{ route('specialties.show', $specialty) }}" class="group specialty-chip">
                            <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-100 to-primary-200/80 text-primary-700 shadow-inner ring-1 ring-primary-200/50 transition group-hover:from-primary-600 group-hover:to-primary-800 group-hover:text-white">
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            <span class="flex-1 font-semibold text-slate-800 group-hover:text-primary-800">{{ $specialty->name }}</span>
                            <svg class="size-4 text-slate-300 transition group-hover:-translate-x-1 group-hover:text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Cities --}}
    @if ($cities->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-8">
                <span class="eyebrow">شهرها</span>
                <h2 class="section-title mt-3">جستجو بر اساس شهر</h2>
                <p class="section-subtitle">پزشکان در شهرهای مختلف ایران</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @foreach ($cities as $city)
                    <a href="{{ route('cities.show', $city) }}" class="city-chip inline-flex items-center gap-1.5">
                        <svg class="size-3.5 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $city->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Ad placement --}}
    <section class="mx-auto max-w-7xl px-4 pb-14 sm:px-6 lg:px-8">
        <x-ad.placement placementKey="home_sidebar" :limit="1" />
    </section>
@endsection
