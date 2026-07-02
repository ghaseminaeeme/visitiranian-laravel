@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <div class="page-hero mb-8">
            <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <span class="eyebrow !bg-white/10 !text-white/90 !ring-white/20">{{ $specialty->name }} · {{ $city->name }}</span>
                    <h1 class="mt-4 text-3xl font-extrabold text-balance md:text-4xl">{{ $specialty->name }} در {{ $city->name }}</h1>
                    <p class="mt-3 text-base text-white/85">بهترین {{ $specialty->name }} در {{ $city->name }}</p>
                </div>
                @if ($doctors->total() > 0)
                    <span class="stat-pill shrink-0">{{ number_format($doctors->total()) }} پزشک</span>
                @endif
            </div>
        </div>

        @if ($doctors->isEmpty())
            <div class="card-elevated p-16 text-center">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-primary-50 text-primary-500">
                    <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <p class="text-lg font-semibold text-slate-700">پزشکی یافت نشد</p>
                <p class="mt-2 text-sm text-slate-500">پزشکی با این تخصص در {{ $city->name }} وجود ندارد.</p>
                <a href="{{ route('cities.show', $city) }}" class="btn-outline mt-6">سایر پزشکان {{ $city->name }}</a>
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
