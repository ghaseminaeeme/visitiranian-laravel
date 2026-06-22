@props(['doctor', 'availability' => null])

@php
    $photo = $doctor->photo_path
        ? (str_starts_with($doctor->photo_path, 'http') ? $doctor->photo_path : asset('storage/'.$doctor->photo_path))
        : null;
    $specialty = $doctor->primarySpecialty?->name ?? $doctor->specialties->first()?->name;
@endphp

<article {{ $attributes->merge(['class' => 'card group overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md']) }}>
    <a href="{{ route('doctors.show', $doctor) }}" class="flex h-full flex-col">
        <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-primary-50 to-primary-100">
            @if ($photo)
                <img src="{{ $photo }}" alt="تصویر {{ $doctor->name }}" class="size-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
            @else
                <div class="flex size-full items-center justify-center">
                    <svg class="size-16 text-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            @endif
            @if ($doctor->is_vip)
                <span class="badge-vip absolute start-3 top-3">
                    <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    VIP
                </span>
            @endif
            @if ($availability === 'today')
                <span class="absolute end-3 top-3 rounded-full bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white">نوبت خالی امروز</span>
            @elseif ($availability === 'tomorrow')
                <span class="absolute end-3 top-3 rounded-full bg-sky-600 px-2.5 py-1 text-xs font-semibold text-white">نوبت فردا</span>
            @endif
        </div>
        <div class="flex flex-1 flex-col gap-2 p-4">
            <h3 class="text-base font-bold text-slate-900 group-hover:text-primary-800">{{ $doctor->name }}</h3>
            @if ($specialty)
                <p class="text-sm text-primary-700">{{ $specialty }}</p>
            @endif
            @if ($doctor->city)
                <p class="mt-auto flex items-center gap-1 text-xs text-slate-500">
                    <svg class="size-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $doctor->city->name }}
                </p>
            @endif
        </div>
    </a>
</article>
