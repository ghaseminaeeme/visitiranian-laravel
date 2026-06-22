<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <div
        class="relative flex min-h-[12rem] flex-col justify-end bg-gradient-to-br from-teal-600 to-teal-800 p-6 text-white"
        @if($imageUrl)
            style="background-image: linear-gradient(to top, rgba(0,0,0,.55), rgba(0,0,0,.25)), url('{{ $imageUrl }}'); background-size: cover; background-position: center;"
        @endif
    >
        @if($title)
            <h3 class="text-xl font-bold">{{ $title }}</h3>
        @endif

        @if($subtitle)
            <p class="mt-1 text-sm text-teal-50">{{ $subtitle }}</p>
        @endif

        @if($ctaText)
            <span class="mt-4 inline-flex w-fit rounded-lg bg-white px-4 py-2 text-sm font-semibold text-teal-700">
                {{ $ctaText }}
            </span>
        @endif
    </div>

    @if($imageWidth && $imageHeight)
        <div class="border-t border-gray-100 px-4 py-2 text-xs text-gray-500 dark:border-gray-800 dark:text-gray-400">
            ابعاد تصویر: {{ $imageWidth }} × {{ $imageHeight }} پیکسل
        </div>
    @endif
</div>
