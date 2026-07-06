@props(['doctor', 'availability' => null, 'compact' => false])

@php
    $specialty = $doctor->primarySpecialty?->name ?? $doctor->specialties->first()?->name;
    $rating = isset($doctor->reviews_avg_rating) && $doctor->reviews_avg_rating
        ? round((float) $doctor->reviews_avg_rating, 1)
        : null;
@endphp

<article {{ $attributes->merge(['class' => 'card card-hover group overflow-hidden']) }}>
    <a href="{{ route('doctors.show', $doctor) }}" class="flex h-full flex-col">
        <div class="doctor-card-photo relative {{ $compact ? 'aspect-[3/2]' : 'aspect-[4/3]' }}">
            <x-doctor-photo :doctor="$doctor" :size="$compact ? 'sidebar' : 'card'" class="size-full transition duration-500 group-hover:scale-105" />

            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-slate-900/60 via-slate-900/10 to-transparent"></div>

            @if ($rating)
                <div class="absolute bottom-3 start-3 flex items-center gap-1 rounded-full bg-white/95 px-2.5 py-1 shadow-sm backdrop-blur-sm">
                    <svg class="size-3.5 text-accent-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span class="text-xs font-bold text-slate-800">{{ number_format($rating, 1) }}</span>
                </div>
            @endif

            @if ($doctor->is_vip)
                <span class="badge-vip absolute start-3 top-3">
                    <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    VIP
                </span>
            @endif

            @if ($availability === 'today')
                <span class="absolute end-3 top-3 inline-flex items-center gap-1 rounded-full bg-emerald-500 px-2.5 py-1 text-xs font-bold text-white shadow-sm shadow-emerald-500/30">
                    <span class="size-1.5 rounded-full bg-white"></span>نوبت امروز
                </span>
            @elseif ($availability === 'tomorrow')
                <span class="absolute end-3 top-3 rounded-full bg-primary-500 px-2.5 py-1 text-xs font-bold text-white shadow-sm shadow-primary-500/30">نوبت فردا</span>
            @endif
        </div>

        <div class="flex flex-1 flex-col gap-2 {{ $compact ? 'p-3' : 'p-5' }}">
            <h3 class="{{ $compact ? 'text-sm' : 'text-base' }} font-bold text-slate-900 transition group-hover:text-primary-700">{{ $doctor->name }}</h3>

            @if ($specialty)
                <p class="badge-soft w-fit">{{ $specialty }}</p>
            @endif

            @if ($doctor->city)
                <p class="flex items-center gap-1.5 text-xs text-slate-500">
                    <svg class="size-3.5 shrink-0 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $doctor->city->name }}
                </p>
            @endif

            @unless ($compact)
                <span class="mt-auto flex items-center justify-center gap-1.5 rounded-xl border border-primary-200 bg-primary-50/60 py-2 text-xs font-bold text-primary-700 transition group-hover:bg-primary-600 group-hover:text-white">
                    مشاهده پروفایل و رزرو
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            @endunless
        </div>
    </a>
</article>
