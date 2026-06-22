@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <div class="mb-8">
            <h1 class="section-title">پزشک در {{ $city->name }}</h1>
            <p class="mt-2 text-slate-600">لیست پزشکان {{ $city->name }}@if($city->province) — {{ $city->province->name }}@endif</p>
        </div>

        @if ($specialties->isNotEmpty())
            <div class="mb-8">
                <h2 class="mb-3 text-sm font-semibold text-slate-700">فیلتر بر اساس تخصص</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($specialties as $specialty)
                        <a
                            href="{{ route('cities.specialty', [$city, $specialty]) }}"
                            class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 transition hover:border-primary-300 hover:bg-primary-50"
                        >
                            {{ $specialty->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($doctors->isEmpty())
            <div class="card p-12 text-center text-slate-500">پزشکی در این شهر یافت نشد.</div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($doctors as $doctor)
                    <x-doctor-card :doctor="$doctor" />
                @endforeach
            </div>
            <div class="mt-8">{{ $doctors->links() }}</div>
        @endif
    </div>
@endsection
