@php
    $url = $record->file_path
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($record->file_path)
        : null;
@endphp

<div class="space-y-4">
    @if($url)
        <img src="{{ $url }}" alt="پیش‌نمایش عکس پزشک" class="mx-auto max-h-96 rounded-xl shadow" />
    @endif

    <dl class="grid grid-cols-2 gap-2 text-sm">
        <dt class="text-gray-500">پزشک</dt>
        <dd>{{ $record->doctor?->name }}</dd>
        <dt class="text-gray-500">ابعاد</dt>
        <dd>{{ $record->width }} × {{ $record->height }}</dd>
        <dt class="text-gray-500">وضعیت</dt>
        <dd>{{ $record->status }}</dd>
    </dl>
</div>
