@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <div class="page-hero mb-10">
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold md:text-4xl">لیست پزشکان</h1>
                <p class="mt-3 max-w-2xl text-base text-white/85">
                    جستجو و فیلتر پزشکان بر اساس نام، تخصص و شهر — رزرو نوبت آنلاین
                </p>
                @if ($doctors->total() > 0)
                    <p class="stat-pill mt-5">{{ number_format($doctors->total()) }} پزشک یافت شد</p>
                @endif
            </div>
        </div>

        <form action="{{ route('doctors.index') }}" method="GET" class="filter-panel mb-10">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label for="q" class="mb-1.5 block text-sm font-semibold text-slate-700">جستجو</label>
                    <input type="search" id="q" name="q" value="{{ $query }}" placeholder="نام پزشک…" class="input-field">
                </div>
                <div>
                    <label for="city" class="mb-1.5 block text-sm font-semibold text-slate-700">شهر</label>
                    <select id="city" name="city" class="select-field">
                        <option value="">همه شهرها</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" @selected($cityId === $city->id)>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="specialty" class="mb-1.5 block text-sm font-semibold text-slate-700">تخصص</label>
                    <select id="specialty" name="specialty" class="select-field">
                        <option value="">همه تخصص‌ها</option>
                        @foreach ($specialties as $specialty)
                            <option value="{{ $specialty->id }}" @selected($specialtyId === $specialty->id)>{{ $specialty->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-3">
                <button type="submit" class="btn-primary">اعمال فیلتر</button>
                @if ($query || $cityId || $specialtyId)
                    <a href="{{ route('doctors.index') }}" class="btn-ghost">پاک کردن فیلترها</a>
                @endif
            </div>
        </form>

        @if ($doctors->isEmpty())
            <div class="card-elevated p-16 text-center">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-slate-100">
                    <svg class="size-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <p class="text-lg font-semibold text-slate-700">پزشکی یافت نشد</p>
                <p class="mt-2 text-sm text-slate-500">عبارت جستجو یا فیلترها را تغییر دهید.</p>
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
