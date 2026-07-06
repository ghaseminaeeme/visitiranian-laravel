@php
    $panelId = \Filament\Facades\Filament::getCurrentPanel()?->getId();
    $subtitle = $panelId === 'doctor'
        ? 'برای مدیریت پروفایل، نوبت‌ها و برنامه کاری خود وارد شوید.'
        : 'برای مدیریت پزشکان، نوبت‌ها و محتوای سایت وارد شوید.';
@endphp

<div class="-mt-2 mb-6 text-center">
    <p class="text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
    <div class="mt-4 flex items-center gap-3">
        <span class="h-px flex-1 bg-gray-200 dark:bg-white/10"></span>
        <span class="text-xs font-semibold text-primary-600">ورود امن</span>
        <span class="h-px flex-1 bg-gray-200 dark:bg-white/10"></span>
    </div>
</div>
