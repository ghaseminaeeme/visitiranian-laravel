@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <div class="mb-8">
            <h1 class="section-title">لیست پزشکان</h1>
            <p class="mt-2 text-slate-600">جستجو و فیلتر پزشکان بر اساس نام، تخصص و شهر</p>
        </div>

        <form action="{{ route('doctors.index') }}" method="GET" class="card mb-8 flex flex-col gap-3 p-4 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="q" class="mb-1 block text-sm font-medium text-slate-700">جستجو</label>
                <input type="search" id="q" name="q" value="{{ $query }}" placeholder="نام پزشک…" class="input-field">
            </div>
            <button type="submit" class="btn-primary shrink-0">اعمال فیلتر</button>
        </form>

        @if ($doctors->isEmpty())
            <div class="card p-12 text-center">
                <p class="text-slate-500">پزشکی یافت نشد. عبارت جستجو را تغییر دهید.</p>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($doctors as $doctor)
                    <x-doctor-card :doctor="$doctor" :availability="$availabilityBadges[$doctor->id] ?? null" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $doctors->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
