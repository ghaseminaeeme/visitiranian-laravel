@props(['title' => null, 'subtitle' => null, 'variant' => 'default'])

@php
    $variants = [
        'default' => 'from-primary-700 to-primary-900',
        'accent' => 'from-accent-500 to-accent-600',
        'vip' => 'from-vip-500 to-vip-600',
    ];
    $gradient = $variants[$variant] ?? $variants['default'];
@endphp

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl bg-gradient-to-l '.$gradient.' px-6 py-8 text-white shadow-lg md:px-10 md:py-10']) }}>
    @if ($title)
        <h2 class="text-xl font-bold md:text-2xl">{{ $title }}</h2>
    @endif
    @if ($subtitle)
        <p class="mt-2 max-w-2xl text-sm text-white/90 md:text-base">{{ $subtitle }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</section>
