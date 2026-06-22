@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <div class="mb-8">
            <h1 class="section-title">{{ $specialty->name }} در {{ $city->name }}</h1>
            <p class="mt-2 text-slate-600">بهترین {{ $specialty->name }} در {{ $city->name }}</p>
        </div>

        @if ($doctors->isEmpty())
            <div class="card p-12 text-center text-slate-500">پزشکی با این تخصص در {{ $city->name }} یافت نشد.</div>
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
