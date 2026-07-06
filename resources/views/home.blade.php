@extends('layouts.app')

@section('content')
    {{-- Slider (only when admin has configured slides) --}}
    @if ($sliders->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
            <div
                x-data="heroSlider({{ $sliders->count() }})"
                x-init="start()"
                @mouseenter="stop()"
                @mouseleave="start()"
                class="relative"
            >
                <div class="relative overflow-hidden rounded-3xl shadow-lg shadow-primary-900/15">
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
    @endif

    {{-- Hero with integrated search --}}
    <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <div class="page-hero">
            <div class="relative z-10 grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div>
                    <span class="stat-pill mb-5">
                        <span class="relative flex size-2">
                            <span class="absolute inline-flex size-full animate-ping rounded-full bg-accent-400 opacity-75"></span>
                            <span class="relative inline-flex size-2 rounded-full bg-accent-400"></span>
                        </span>
                        نوبت‌دهی آنلاین ۲۴ ساعته
                    </span>
                    <h1 class="text-3xl font-extrabold leading-tight text-balance md:text-5xl">{{ $siteTagline }}</h1>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-white/85 md:text-lg">
                        جستجو در میان صدها پزشک متخصص در سراسر ایران، مشاهده نظرات واقعی بیماران و رزرو آنلاین نوبت، تنها در چند ثانیه.
                    </p>

                    {{-- Search --}}
                    <form action="{{ route('doctors.index') }}" method="GET" class="mt-7">
                        <div class="flex flex-col gap-2 rounded-2xl bg-white/95 p-2 shadow-xl shadow-primary-950/25 backdrop-blur-sm sm:flex-row sm:items-center">
                            <div class="relative flex-1">
                                <svg class="pointer-events-none absolute start-4 top-1/2 size-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input
                                    type="search"
                                    name="q"
                                    placeholder="نام پزشک، تخصص یا شهر…"
                                    class="w-full border-0 bg-transparent py-3 pe-4 ps-12 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-0"
                                    autocomplete="off"
                                >
                            </div>
                            <button type="submit" class="btn-primary shrink-0 !py-3">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                جستجوی پزشک
                            </button>
                        </div>
                    </form>

                    @if ($specialties->isNotEmpty())
                        <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                            <span class="text-white/60">پرجستجو:</span>
                            @foreach ($specialties->take(5) as $specialty)
                                <a href="{{ route('specialties.show', $specialty) }}" class="rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-white/90 ring-1 ring-white/15 transition hover:bg-white/20">{{ $specialty->name }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur-sm transition hover:bg-white/15">
                        <p class="text-3xl font-extrabold">{{ $doctorCount }}+</p>
                        <p class="mt-1 text-sm text-white/75">پزشک فعال</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur-sm transition hover:bg-white/15">
                        <p class="text-3xl font-extrabold">{{ $specialties->count() }}</p>
                        <p class="mt-1 text-sm text-white/75">تخصص پزشکی</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur-sm transition hover:bg-white/15">
                        <p class="text-3xl font-extrabold">{{ $cities->count() }}+</p>
                        <p class="mt-1 text-sm text-white/75">شهر تحت پوشش</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur-sm transition hover:bg-white/15">
                        <p class="text-3xl font-extrabold">۲۴/۷</p>
                        <p class="mt-1 text-sm text-white/75">رزرو آنلاین</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Trust bar --}}
    <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <div class="grid gap-3 sm:grid-cols-3">
            <div class="flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-white p-4 shadow-sm shadow-slate-900/[0.03]">
                <span class="icon-badge">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-slate-900">پزشکان تأییدشده</p>
                    <p class="text-xs text-slate-500">اطلاعات معتبر و بررسی‌شده</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-white p-4 shadow-sm shadow-slate-900/[0.03]">
                <span class="icon-badge">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-slate-900">رزرو سریع نوبت</p>
                    <p class="text-xs text-slate-500">ثبت نوبت در کمتر از یک دقیقه</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-white p-4 shadow-sm shadow-slate-900/[0.03]">
                <span class="icon-badge">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-slate-900">نظرات واقعی بیماران</p>
                    <p class="text-xs text-slate-500">تجربه‌ی مراجعان پیشین</p>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <span class="eyebrow">ساده و سریع</span>
            <h2 class="section-title mt-3">در سه گام نوبت بگیرید</h2>
            <p class="section-subtitle mx-auto">فرآیند رزرو نوبت آنلاین بدون تماس تلفنی و اتلاف وقت</p>
        </div>
        <div class="relative grid gap-6 md:grid-cols-3">
            <div class="info-tile flex-col items-start !p-6">
                <span class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-primary-600 text-lg font-black text-white shadow-md shadow-primary-600/25">۱</span>
                <h3 class="text-base font-bold text-slate-900">جستجوی پزشک</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">بر اساس نام، تخصص یا شهر، پزشک مناسب خود را پیدا کنید.</p>
            </div>
            <div class="info-tile flex-col items-start !p-6">
                <span class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-primary-600 text-lg font-black text-white shadow-md shadow-primary-600/25">۲</span>
                <h3 class="text-base font-bold text-slate-900">بررسی پروفایل</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">نظرات بیماران، آدرس مطب و اطلاعات تماس پزشک را مشاهده کنید.</p>
            </div>
            <div class="info-tile flex-col items-start !p-6">
                <span class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-accent-500 text-lg font-black text-white shadow-md shadow-accent-500/25">۳</span>
                <h3 class="text-base font-bold text-slate-900">رزرو نوبت</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">تاریخ و ساعت دلخواه را انتخاب و نوبت خود را آنلاین ثبت کنید.</p>
            </div>
        </div>
    </section>

    {{-- Featured doctors --}}
    @if ($featuredDoctors->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="eyebrow">منتخب سردبیر</span>
                    <h2 class="section-title mt-3">پزشکان برتر</h2>
                    <p class="section-subtitle">پزشکان ویژه و پرطرفدار با بالاترین امتیاز بیماران</p>
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
        <section class="border-y border-slate-200/70 bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 text-center">
                    <span class="eyebrow">تخصص‌ها</span>
                    <h2 class="section-title mt-3">تخصص‌های پزشکی</h2>
                    <p class="section-subtitle mx-auto">انتخاب تخصص و مشاهده پزشکان مرتبط</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($specialties as $specialty)
                        <a href="{{ route('specialties.show', $specialty) }}" class="group specialty-chip">
                            <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-primary-50 text-primary-700 ring-1 ring-primary-100 transition group-hover:bg-primary-600 group-hover:text-white group-hover:ring-primary-600">
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-8">
                <span class="eyebrow">شهرها</span>
                <h2 class="section-title mt-3">جستجو بر اساس شهر</h2>
                <p class="section-subtitle">پزشکان در شهرهای مختلف ایران</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @foreach ($cities as $city)
                    <a href="{{ route('cities.show', $city) }}" class="city-chip inline-flex items-center gap-1.5">
                        <svg class="size-3.5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $city->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="page-hero flex flex-col items-center gap-6 text-center md:flex-row md:justify-between md:text-start">
            <div class="relative z-10">
                <h2 class="text-2xl font-extrabold text-balance md:text-3xl">همین حالا نوبت پزشک خود را رزرو کنید</h2>
                <p class="mt-3 max-w-xl text-white/85">بدون تماس تلفنی، بدون معطلی. پزشک مناسب را پیدا کنید و آنلاین نوبت بگیرید.</p>
            </div>
            <a href="{{ route('doctors.index') }}" class="btn-accent relative z-10 shrink-0 !px-7 !py-3.5 text-base">شروع کنید</a>
        </div>
    </section>

    {{-- Ad placement --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-ad.placement placementKey="home_sidebar" :limit="1" />
    </section>
@endsection
