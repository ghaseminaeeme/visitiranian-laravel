@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <div class="page-hero mb-8">
            <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <span class="eyebrow !bg-white/10 !text-white/90 !ring-white/20">تخصص پزشکی</span>
                    <h1 class="mt-4 text-3xl font-extrabold text-balance md:text-4xl">{{ $specialty->name }}</h1>
                    @if ($specialty->description)
                        <p class="mt-3 max-w-3xl text-base text-white/85">{{ $specialty->description }}</p>
                    @else
                        <p class="mt-3 text-base text-white/85">لیست پزشکان {{ $specialty->name }} در سراسر ایران</p>
                    @endif
                </div>
                @if ($doctors->total() > 0)
                    <span class="stat-pill shrink-0">{{ number_format($doctors->total()) }} پزشک</span>
                @endif
            </div>
        </div>

        @if ($doctors->isEmpty())
            <div class="card-elevated p-16 text-center">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-primary-50 text-primary-500">
                    <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-lg font-semibold text-slate-700">پزشکی ثبت نشده است</p>
                <p class="mt-2 text-sm text-slate-500">در حال حاضر پزشکی در این تخصص وجود ندارد.</p>
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
