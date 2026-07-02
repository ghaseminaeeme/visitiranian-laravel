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
                                class="size-2.5 rounded-full transition"
                                :class="current === {{ $index }} ? 'bg-white scale-125 shadow-lg' : 'bg-white/40 hover:bg-white/70'"
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
                        <span class="stat-pill mb-4">پلتفرم معتبر معرفی پزشکان</span>
                        <h1 class="text-3xl font-extrabold leading-tight md:text-5xl">{{ $siteTagline }}</h1>
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
                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm">
                                <p class="text-3xl font-extrabold">{{ $doctorCount }}+</p>
                                <p class="mt-1 text-sm text-white/75">پزشک فعال</p>
                            </div>
                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm">
                                <p class="text-3xl font-extrabold">{{ $specialties->count() }}</p>
                                <p class="mt-1 text-sm text-white/75">تخصص پزشکی</p>
                            </div>
                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm">
                                <p class="text-3xl font-extrabold">{{ $cities->count() }}+</p>
                                <p class="mt-1 text-sm text-white/75">شهر</p>
                            </div>
                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm">
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
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
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
        </div>
    </section>

    {{-- Featured doctors --}}
    @if ($featuredDoctors->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="section-title">پزشکان برتر</h2>
                    <p class="section-subtitle">پزشکان VIP و پرطرفدار با بالاترین امتیاز بیماران</p>
                </div>
                <a href="{{ route('doctors.index') }}" class="btn-outline shrink-0">مشاهده همه پزشکان</a>
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
        <section class="border-y border-slate-200/80 bg-white/60 py-14 backdrop-blur-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 text-center">
                    <h2 class="section-title">تخصص‌های پزشکی</h2>
                    <p class="section-subtitle mx-auto">انتخاب تخصص و مشاهده پزشکان مرتبط</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($specialties as $specialty)
                        <a href="{{ route('specialties.show', $specialty) }}" class="group specialty-chip">
                            <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-100 to-primary-200/80 text-primary-700 shadow-inner ring-1 ring-primary-200/50">
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            <span class="font-semibold text-slate-800 group-hover:text-primary-800">{{ $specialty->name }}</span>
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
                <h2 class="section-title">جستجو بر اساس شهر</h2>
                <p class="section-subtitle">پزشکان در شهرهای مختلف ایران</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @foreach ($cities as $city)
                    <a href="{{ route('cities.show', $city) }}" class="city-chip">
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
