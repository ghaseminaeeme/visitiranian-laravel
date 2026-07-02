@props([
    'doctor',
    'size' => 'card',
    'class' => '',
])

@php
    $sizes = [
        'card' => 'aspect-[4/3] w-full rounded-none',
        'profile' => 'size-44 rounded-2xl sm:size-48',
        'thumb' => 'size-16 rounded-xl',
        'sidebar' => 'aspect-[4/3] w-full rounded-xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['card'];
@endphp

<img
    src="{{ $doctor->photoUrl() }}"
    alt="تصویر {{ $doctor->name }}"
    {{ $attributes->merge(['class' => 'object-cover '.$sizeClass.' '.$class]) }}
    loading="lazy"
    decoding="async"
>
