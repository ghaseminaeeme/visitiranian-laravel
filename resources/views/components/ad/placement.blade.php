@props(['placementKey', 'limit' => 1])

@php
    $ads = app(\App\Services\Ads\AdService::class)->forPlacement($placementKey, $limit);
@endphp

@foreach ($ads as $ad)
    <aside class="card overflow-hidden">
        @if ($ad->image_path)
            <a href="{{ $ad->cta_url ?? '#' }}" @if($ad->cta_url) target="_blank" rel="noopener sponsored" @endif class="block">
                <img
                    src="{{ str_starts_with($ad->image_path, 'http') ? $ad->image_path : asset('storage/'.$ad->image_path) }}"
                    alt="{{ $ad->title ?? 'تبلیغ' }}"
                    class="w-full object-cover"
                    loading="lazy"
                >
            </a>
        @endif
        @if ($ad->title || $ad->subtitle)
            <div class="p-4">
                @if ($ad->title)
                    <h3 class="font-bold text-slate-900">{{ $ad->title }}</h3>
                @endif
                @if ($ad->subtitle)
                    <p class="mt-1 text-sm text-slate-600">{{ $ad->subtitle }}</p>
                @endif
                @if ($ad->cta_text && $ad->cta_url)
                    <a href="{{ $ad->cta_url }}" target="_blank" rel="noopener sponsored" class="btn-primary mt-3 text-xs">
                        {{ $ad->cta_text }}
                    </a>
                @endif
            </div>
        @endif
    </aside>
@endforeach
