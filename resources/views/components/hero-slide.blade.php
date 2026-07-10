@props(['slide', 'index' => 0, 'variant' => 'default'])

@if ($variant === 'background')
    <div
        x-show="current === {{ $index }}"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-500"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0"
        @if ($index > 0) style="display: none;" @endif
    >
        @if ($slide->image_path)
            <img
                src="{{ str_starts_with($slide->image_path, 'http') ? $slide->image_path : asset('storage/'.$slide->image_path) }}"
                alt=""
                class="absolute inset-0 size-full object-cover opacity-25"
                @if ($index === 0) fetchpriority="high" @else loading="lazy" @endif
            >
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-primary-950/70 via-primary-900/50 to-primary-950/90"></div>
    </div>
@else
    <div
        x-show="current === {{ $index }}"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-x-8"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-8"
        class="absolute inset-0"
        @if ($index > 0) style="display: none;" @endif
    >
        <div class="relative flex h-full min-h-[420px] items-center overflow-hidden rounded-3xl md:min-h-[480px]" style="background: linear-gradient(135deg, var(--color-primary-950) 0%, var(--color-primary-800) 40%, var(--color-primary-700) 100%);">
            @if ($slide->image_path)
                <img
                    src="{{ str_starts_with($slide->image_path, 'http') ? $slide->image_path : asset('storage/'.$slide->image_path) }}"
                    alt="{{ $slide->title }}"
                    class="absolute inset-0 size-full object-cover opacity-30"
                    @if ($index === 0) fetchpriority="high" @else loading="lazy" @endif
                >
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-primary-950/85 via-primary-900/35 to-transparent"></div>
            <div class="relative z-10 mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    @if ($slide->subtitle)
                        <p class="mb-3 text-sm font-medium text-highlight-400">{{ $slide->subtitle }}</p>
                    @endif
                    <h2 class="mb-4 text-3xl font-bold leading-tight text-white md:text-5xl">{{ $slide->title }}</h2>
                    @if ($slide->cta_text && $slide->cta_url)
                        <a href="{{ $slide->cta_url }}" class="btn-accent mt-4">{{ $slide->cta_text }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
