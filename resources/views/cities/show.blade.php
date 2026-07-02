@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <div class="page-hero mb-8">
            <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <span class="eyebrow !bg-white/10 !text-white/90 !ring-white/20">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        شهر
                    </span>
                    <h1 class="mt-4 text-3xl font-extrabold text-balance md:text-4xl">پزشکان {{ $city->name }}</h1>
                    <p class="mt-3 text-base text-white/85">لیست پزشکان {{ $city->name }}@if($city->province) — {{ $city->province->name }}@endif</p>
                </div>
                @if ($doctors->total() > 0)
                    <span class="stat-pill shrink-0">{{ number_format($doctors->total()) }} پزشک</span>
                @endif
            </div>
        </div>

        @if ($specialties->isNotEmpty())
            <div class="filter-panel mb-8">
                <h2 class="mb-3 text-sm font-semibold text-slate-700">فیلتر بر اساس تخصص</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($specialties as $specialty)
                        <a href="{{ route('cities.specialty', [$city, $specialty]) }}" class="city-chip">
                            {{ $specialty->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($doctors->isEmpty())
            <div class="card-elevated p-16 text-center">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-primary-50 text-primary-500">
                    <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <p class="text-lg font-semibold text-slate-700">پزشکی یافت نشد</p>
                <p class="mt-2 text-sm text-slate-500">در این شهر پزشکی ثبت نشده است.</p>
                <a href="{{ route('doctors.index') }}" class="btn-outline mt-6">مشاهده همه پزشکان</a>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($doctors as $doctor)
                    <x-doctor-card :doctor="$doctor" />
                @endforeach
            </div>
            <div class="mt-10">{{ $doctors->links() }}</div>
        @endif
    </div>
@endsection
