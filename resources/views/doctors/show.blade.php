@extends('layouts.app')

@section('content')
    @php
        $specialty = $doctor->primarySpecialty?->name ?? $doctor->specialties->first()?->name;
        $rating = $doctor->reviews->avg('rating');
        $reviewCount = $doctor->reviews->count();
        $primaryPhone = $doctor->contactPhones->first()?->phone;
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                {{-- Profile header --}}
                <div class="card-elevated overflow-hidden">
                    <div class="relative px-6 py-8 sm:px-8" style="background: linear-gradient(135deg, var(--color-primary-900) 0%, var(--color-primary-700) 60%, var(--color-primary-600) 100%);">
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                        <div class="relative flex flex-col items-center gap-6 sm:flex-row sm:items-end">
                            <div class="relative shrink-0">
                                <x-doctor-photo :doctor="$doctor" size="profile" class="ring-4 ring-white/25 shadow-2xl" />
                                @if ($doctor->is_vip)
                                    <span class="badge-vip absolute -top-2 start-1/2 -translate-x-1/2 shadow-lg">
                                        <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        VIP
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 text-center sm:text-start">
                                <h1 class="text-2xl font-extrabold text-white md:text-3xl">{{ $doctor->name }}</h1>
                                @if ($specialty)
                                    <p class="mt-2 inline-flex rounded-full bg-white/15 px-4 py-1 text-sm font-semibold text-white backdrop-blur-sm">{{ $specialty }}</p>
                                @endif
                                @if ($doctor->city)
                                    <p class="mt-3 flex items-center justify-center gap-1.5 text-sm text-white/80 sm:justify-start">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        {{ $doctor->city->name }}@if($doctor->city->province) — {{ $doctor->city->province->name }}@endif
                                    </p>
                                @endif
                                @if ($rating)
                                    <div class="mt-3 flex items-center justify-center gap-2 sm:justify-start">
                                        <div class="flex items-center gap-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="rating-star {{ $i <= round($rating) ? 'text-accent-400' : 'text-white/25' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                        <span class="text-sm font-semibold text-white">{{ number_format($rating, 1) }}</span>
                                        <span class="text-sm text-white/60">({{ $reviewCount }} نظر)</span>
                                    </div>
                                @endif

                                {{-- Quick actions --}}
                                <div class="mt-5 flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                                    <a href="#booking" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-bold text-primary-700 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md lg:hidden">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        رزرو نوبت
                                    </a>
                                    @if ($primaryPhone)
                                        <a href="tel:{{ $primaryPhone }}" class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/20" dir="ltr">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            تماس
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick stats --}}
                    <div class="grid grid-cols-3 divide-x divide-x-reverse divide-slate-100 border-b border-slate-100 text-center">
                        <div class="px-3 py-4">
                            <p class="text-lg font-extrabold text-slate-900">{{ $rating ? number_format($rating, 1) : '—' }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">امتیاز</p>
                        </div>
                        <div class="px-3 py-4">
                            <p class="text-lg font-extrabold text-slate-900">{{ $reviewCount }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">نظر بیمار</p>
                        </div>
                        <div class="px-3 py-4">
                            <p class="text-lg font-extrabold text-slate-900">{{ $doctor->city?->name ?? '—' }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">شهر مطب</p>
                        </div>
                    </div>

                    @if ($doctor->bio)
                        <div class="border-t border-slate-100 px-6 py-7 sm:px-8">
                            <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-slate-900">
                                <span class="flex size-8 items-center justify-center rounded-lg bg-primary-50 text-primary-700">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </span>
                                درباره پزشک
                            </h2>
                            <div class="prose-fa text-sm leading-relaxed">{!! nl2br(e($doctor->bio)) !!}</div>
                        </div>
                    @endif

                    @if ($doctor->address)
                        <div class="border-t border-slate-100 px-6 py-7 sm:px-8">
                            <h2 class="mb-3 flex items-center gap-2 text-lg font-bold text-slate-900">
                                <span class="flex size-8 items-center justify-center rounded-lg bg-primary-50 text-primary-700">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                </span>
                                آدرس مطب
                            </h2>
                            <p class="rounded-xl bg-slate-50 p-4 text-sm leading-relaxed text-slate-600">{{ $doctor->address }}</p>
                        </div>
                    @endif

                    @if ($doctor->contactPhones->isNotEmpty())
                        <div class="border-t border-slate-100 px-6 py-7 sm:px-8">
                            <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-slate-900">
                                <span class="flex size-8 items-center justify-center rounded-lg bg-primary-50 text-primary-700">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </span>
                                تماس
                            </h2>
                            <ul class="grid gap-3 sm:grid-cols-2">
                                @foreach ($doctor->contactPhones as $phone)
                                    <li>
                                        <a href="tel:{{ $phone->phone }}" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 transition hover:border-primary-200 hover:bg-primary-50/50" dir="ltr">
                                            <span class="flex size-10 items-center justify-center rounded-xl bg-primary-100 text-primary-700">
                                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            </span>
                                            <div class="text-start">
                                                <span class="block font-semibold text-slate-900">{{ $phone->phone }}</span>
                                                @if ($phone->label)
                                                    <span class="text-xs text-slate-400">{{ $phone->label }}</span>
                                                @endif
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($doctor->reviews->isNotEmpty())
                        <div class="border-t border-slate-100 px-6 py-7 sm:px-8">
                            <h2 class="mb-5 flex items-center gap-2 text-lg font-bold text-slate-900">
                                <span class="flex size-8 items-center justify-center rounded-lg bg-primary-50 text-primary-700">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </span>
                                نظرات بیماران
                            </h2>
                            <div class="space-y-4">
                                @foreach ($doctor->reviews as $review)
                                    <blockquote class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5">
                                        <div class="mb-3 flex items-center gap-1 text-accent-500">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="size-4 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-200' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                        <p class="text-sm leading-relaxed text-slate-700">{{ $review->body }}</p>
                                        @if ($review->author_name)
                                            <footer class="mt-3 text-xs font-medium text-slate-400">— {{ $review->author_name }}</footer>
                                        @endif
                                    </blockquote>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <aside class="space-y-6">
                <div id="booking" class="lg:sticky lg:top-24">
                    @include('appointments.partials.book', ['doctor' => $doctor])
                </div>

                @if ($relatedDoctors->isNotEmpty())
                    <div class="card-elevated p-5">
                        <h3 class="mb-4 font-bold text-slate-900">پزشکان مرتبط</h3>
                        <div class="space-y-4">
                            @foreach ($relatedDoctors as $related)
                                <x-doctor-card :doctor="$related" compact class="!shadow-none" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <x-ad.placement placementKey="doctor_sidebar" :limit="1" />
            </aside>
        </div>
    </div>
@endsection
