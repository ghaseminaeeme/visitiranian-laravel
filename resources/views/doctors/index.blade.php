@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <div class="page-hero mb-8">
            <div class="relative z-10">
                <span class="eyebrow !bg-white/10 !text-white/90 !ring-white/20">دایرکتوری پزشکان</span>
                <h1 class="mt-4 text-3xl font-extrabold text-balance md:text-4xl">لیست پزشکان</h1>
                <p class="mt-3 max-w-2xl text-base text-white/85">
                    جستجو و فیلتر پزشکان بر اساس نام، تخصص و شهر — رزرو نوبت آنلاین
                </p>
                @if ($doctors->total() > 0)
                    <p class="stat-pill mt-5">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ number_format($doctors->total()) }} پزشک یافت شد
                    </p>
                @endif
            </div>
        </div>

        <form action="{{ route('doctors.index') }}" method="GET" class="filter-panel mb-6">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label for="q" class="mb-1.5 block text-sm font-semibold text-slate-700">جستجو</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute start-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="search" id="q" name="q" value="{{ $query }}" placeholder="نام پزشک…" class="input-field !ps-10">
                    </div>
                </div>
                <x-searchable-select name="city" id="city" label="شهر" search-placeholder="جستجوی شهر…">
                    <option value="">همه شهرها</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" @selected($cityId === $city->id)>{{ $city->name }}</option>
                    @endforeach
                </x-searchable-select>
                <x-searchable-select name="specialty" id="specialty" label="تخصص" search-placeholder="جستجوی تخصص…">
                    <option value="">همه تخصص‌ها</option>
                    @foreach ($specialties as $specialty)
                        <option value="{{ $specialty->id }}" @selected($specialtyId === $specialty->id)>{{ $specialty->name }}</option>
                    @endforeach
                </x-searchable-select>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <button type="submit" class="btn-primary">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.879a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/></svg>
                    اعمال فیلتر
                </button>
                @if ($query || $cityId || $specialtyId)
                    <a href="{{ route('doctors.index') }}" class="btn-ghost">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        پاک کردن فیلترها
                    </a>
                @endif
            </div>
        </form>

        @if ($doctors->isEmpty())
            <div class="card-elevated p-16 text-center">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-primary-50 text-primary-500">
                    <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <p class="text-lg font-semibold text-slate-700">پزشکی یافت نشد</p>
                <p class="mt-2 text-sm text-slate-500">عبارت جستجو یا فیلترها را تغییر دهید.</p>
                <a href="{{ route('doctors.index') }}" class="btn-outline mt-6">مشاهده همه پزشکان</a>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($doctors as $doctor)
                    <x-doctor-card :doctor="$doctor" :availability="$availabilityBadges[$doctor->id] ?? null" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $doctors->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
