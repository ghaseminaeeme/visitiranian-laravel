@extends('layouts.app')

@section('content')
    @php
        $photo = $doctor->photo_path
            ? (str_starts_with($doctor->photo_path, 'http') ? $doctor->photo_path : asset('storage/'.$doctor->photo_path))
            : null;
        $specialty = $doctor->primarySpecialty?->name ?? $doctor->specialties->first()?->name;
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Profile --}}
            <div class="lg:col-span-2">
                <div class="card overflow-hidden">
                    <div class="flex flex-col gap-6 p-6 sm:flex-row sm:items-start">
                        <div class="relative mx-auto shrink-0 sm:mx-0">
                            @if ($photo)
                                <img src="{{ $photo }}" alt="تصویر {{ $doctor->name }}" class="size-40 rounded-2xl object-cover shadow-md">
                            @else
                                <div class="flex size-40 items-center justify-center rounded-2xl bg-primary-50">
                                    <svg class="size-20 text-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                            @endif
                            @if ($doctor->is_vip)
                                <span class="badge-vip absolute -top-2 start-1/2 -translate-x-1/2">VIP</span>
                            @endif
                        </div>
                        <div class="flex-1 text-center sm:text-start">
                            <h1 class="text-2xl font-bold text-slate-900 md:text-3xl">{{ $doctor->name }}</h1>
                            @if ($specialty)
                                <p class="mt-1 text-lg text-primary-700">{{ $specialty }}</p>
                            @endif
                            @if ($doctor->city)
                                <p class="mt-2 flex items-center justify-center gap-1 text-sm text-slate-500 sm:justify-start">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    {{ $doctor->city->name }}@if($doctor->city->province) — {{ $doctor->city->province->name }}@endif
                                </p>
                            @endif
                        </div>
                    </div>

                    @if ($doctor->bio)
                        <div class="border-t border-slate-100 px-6 py-6">
                            <h2 class="mb-3 text-lg font-bold text-slate-900">درباره پزشک</h2>
                            <div class="prose-fa text-sm leading-relaxed text-slate-700">{!! nl2br(e($doctor->bio)) !!}</div>
                        </div>
                    @endif

                    @if ($doctor->address)
                        <div class="border-t border-slate-100 px-6 py-6">
                            <h2 class="mb-2 text-lg font-bold text-slate-900">آدرس مطب</h2>
                            <p class="text-sm text-slate-600">{{ $doctor->address }}</p>
                        </div>
                    @endif

                    @if ($doctor->contactPhones->isNotEmpty())
                        <div class="border-t border-slate-100 px-6 py-6">
                            <h2 class="mb-3 text-lg font-bold text-slate-900">تماس</h2>
                            <ul class="space-y-2">
                                @foreach ($doctor->contactPhones as $phone)
                                    <li>
                                        <a href="tel:{{ $phone->phone }}" class="text-primary-700 hover:underline" dir="ltr">{{ $phone->phone }}</a>
                                        @if ($phone->label)
                                            <span class="text-xs text-slate-400">({{ $phone->label }})</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($doctor->reviews->isNotEmpty())
                        <div class="border-t border-slate-100 px-6 py-6">
                            <h2 class="mb-4 text-lg font-bold text-slate-900">نظرات بیماران</h2>
                            <div class="space-y-4">
                                @foreach ($doctor->reviews as $review)
                                    <blockquote class="rounded-xl bg-slate-50 p-4">
                                        <div class="mb-2 flex items-center gap-1 text-accent-500">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="size-4 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-200' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                        <p class="text-sm text-slate-700">{{ $review->body }}</p>
                                        @if ($review->author_name)
                                            <footer class="mt-2 text-xs text-slate-400">— {{ $review->author_name }}</footer>
                                        @endif
                                    </blockquote>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sidebar: booking --}}
            <aside class="space-y-6">
                @include('appointments.partials.book', ['doctor' => $doctor])

                @if ($relatedDoctors->isNotEmpty())
                    <div class="card p-4">
                        <h3 class="mb-4 font-bold text-slate-900">پزشکان مرتبط</h3>
                        <div class="space-y-3">
                            @foreach ($relatedDoctors as $related)
                                <x-doctor-card :doctor="$related" class="!shadow-none" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <x-ad.placement placementKey="doctor_sidebar" :limit="1" />
            </aside>
        </div>
    </div>
@endsection
