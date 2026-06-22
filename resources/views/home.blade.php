@extends('layouts.app')

@section('content')
    {{-- Hero slider --}}
    @if ($sliders->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
            <div
                x-data="heroSlider({{ $sliders->count() }})"
                x-init="start()"
                @mouseenter="stop()"
                @mouseleave="start()"
                class="relative"
            >
                <div class="relative overflow-hidden rounded-3xl">
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
                                :class="current === {{ $index }} ? 'bg-white scale-110' : 'bg-white/50'"
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
            <x-banner
                :title="$siteTagline"
                subtitle="جستجو در میان هزاران پزشک در سراسر ایران — رزرو نوبت آنلاین"
                variant="default"
            >
                <a href="{{ route('doctors.index') }}" class="btn-accent">شروع جستجو</a>
            </x-banner>
        </section>
    @endif

    {{-- Search --}}
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="card mx-auto max-w-3xl p-6 shadow-md">
            <h2 class="mb-4 text-center text-lg font-bold text-slate-900">جستجوی پزشک</h2>
            <form action="{{ route('doctors.index') }}" method="GET" class="flex flex-col gap-3 sm:flex-row">
                <input
                    type="search"
                    name="q"
                    placeholder="نام پزشک، تخصص یا شهر…"
                    class="input-field flex-1"
                    autocomplete="off"
                >
                <button type="submit" class="btn-primary shrink-0">جستجو</button>
            </form>
        </div>
    </section>

    {{-- Featured doctors --}}
    @if ($featuredDoctors->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-end justify-between">
                <h2 class="section-title">پزشکان برتر</h2>
                <a href="{{ route('doctors.index') }}" class="text-sm font-medium text-primary-700 hover:underline">مشاهده همه</a>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featuredDoctors as $doctor)
                    <x-doctor-card :doctor="$doctor" :availability="$availabilityBadges[$doctor->id] ?? null" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Specialties --}}
    @if ($specialties->isNotEmpty())
        <section class="bg-white py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="section-title mb-6">تخصص‌های پزشکی</h2>
                <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($specialties as $specialty)
                        <a
                            href="{{ route('specialties.show', $specialty) }}"
                            class="card flex items-center gap-3 p-4 transition hover:border-primary-200 hover:shadow-md"
                        >
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-700">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            <span class="font-medium text-slate-800">{{ $specialty->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Cities --}}
    @if ($cities->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <h2 class="section-title mb-6">شهرها</h2>
            <div class="flex flex-wrap gap-2">
                @foreach ($cities as $city)
                    <a
                        href="{{ route('cities.show', $city) }}"
                        class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-primary-300 hover:bg-primary-50 hover:text-primary-800"
                    >
                        {{ $city->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Ad placement --}}
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <x-ad.placement placementKey="home_sidebar" :limit="1" />
    </section>
@endsection
