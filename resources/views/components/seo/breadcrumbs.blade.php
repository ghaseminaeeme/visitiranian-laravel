@props(['items' => []])

@if (count($items) > 1)
    <nav aria-label="مسیر صفحه" class="mb-6">
        <ol class="flex flex-wrap items-center gap-1 text-sm text-slate-500">
            @foreach ($items as $index => $item)
                <li class="flex items-center gap-1">
                    @if ($index > 0)
                        <svg class="size-4 shrink-0 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    @endif
                    @if ($item['url'] && $index < count($items) - 1)
                        <a href="{{ $item['url'] }}" class="transition hover:text-primary-700">{{ $item['label'] }}</a>
                    @else
                        <span @class(['font-medium text-slate-800' => $index === count($items) - 1])>{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
