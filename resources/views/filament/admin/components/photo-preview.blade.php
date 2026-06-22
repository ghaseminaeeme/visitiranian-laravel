@php
    $photo = $getRecord();
    $url = $photo?->file_path
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($photo->file_path)
        : null;
@endphp

@if($url)
    <img src="{{ $url }}" alt="پیش‌نمایش" class="max-h-64 rounded-lg border border-gray-200 dark:border-gray-700" />
@else
    <p class="text-sm text-gray-500">تصویری موجود نیست.</p>
@endif
