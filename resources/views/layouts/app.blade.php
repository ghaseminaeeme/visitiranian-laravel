<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @isset($seo)
        <x-seo.meta-tags :seo="$seo" />
    @else
        <title>{{ config('visitiranian.site_name') }}</title>
    @endisset

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen flex flex-col" x-data="mobileNav">
    {{-- Header --}}
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="flex size-9 items-center justify-center rounded-xl bg-primary-700 text-sm font-bold text-white">VI</span>
                <span class="text-lg font-bold text-primary-900">{{ $siteName ?? config('visitiranian.site_name') }}</span>
            </a>

            <nav class="hidden items-center gap-6 md:flex" aria-label="منوی اصلی">
                <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 transition hover:text-primary-700">خانه</a>
                <a href="{{ route('doctors.index') }}" class="text-sm font-medium text-slate-600 transition hover:text-primary-700">پزشکان</a>
                <a href="{{ route('peygiri') }}" class="text-sm font-medium text-slate-600 transition hover:text-primary-700">پیگیری نوبت</a>
                <a href="{{ route('appointments.track') }}" class="text-sm font-medium text-slate-600 transition hover:text-primary-700">کد رهگیری</a>
            </nav>

            <div class="hidden md:block">
                <a href="{{ route('doctors.index') }}" class="btn-primary text-sm">جستجوی پزشک</a>
            </div>

            <button
                type="button"
                class="inline-flex items-center justify-center rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden"
                @click="toggle()"
                aria-label="باز کردن منو"
            >
                <svg x-show="!open" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" x-cloak class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Mobile nav --}}
        <div
            x-show="open"
            x-cloak
            x-transition
            class="border-t border-slate-100 bg-white md:hidden"
            @click.outside="close()"
        >
            <nav class="flex flex-col gap-1 px-4 py-3" aria-label="منوی موبایل">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-primary-50" @click="close()">خانه</a>
                <a href="{{ route('doctors.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-primary-50" @click="close()">پزشکان</a>
                <a href="{{ route('peygiri') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-primary-50" @click="close()">پیگیری نوبت</a>
                <a href="{{ route('appointments.track') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-primary-50" @click="close()">کد رهگیری</a>
            </nav>
        </div>
    </header>

    @if (session('success'))
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="alert">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-auto border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-3">
                <div>
                    <h3 class="text-lg font-bold text-primary-900">{{ $siteName ?? config('visitiranian.site_name') }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">معرفی پزشکان برتر ایران — جستجو بر اساس تخصص و شهر، رزرو نوبت آنلاین.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-900">دسترسی سریع</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        <li><a href="{{ route('doctors.index') }}" class="hover:text-primary-700">لیست پزشکان</a></li>
                        <li><a href="{{ route('peygiri') }}" class="hover:text-primary-700">پیگیری نوبت</a></li>
                        <li><a href="{{ route('appointments.track') }}" class="hover:text-primary-700">جستجو با کد رهگیری</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-900">ارتباط با ما</h4>
                    @if (($socialLinks ?? collect())->isNotEmpty())
                        <ul class="mt-3 flex flex-wrap gap-3">
                            @foreach ($socialLinks as $link)
                                <li>
                                    <a href="{{ $link->url }}" target="_blank" rel="noopener" class="text-sm text-primary-700 hover:underline">
                                        {{ $link->label ?: $link->platform }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-2 text-sm text-slate-500">به زودی…</p>
                    @endif
                </div>
            </div>
            <div class="mt-10 border-t border-slate-100 pt-6 text-center text-xs text-slate-400">
                &copy; {{ \Morilog\Jalali\Jalalian::now()->format('Y') }} {{ $siteName ?? config('visitiranian.site_name') }}. تمامی حقوق محفوظ است.
            </div>
        </div>
    </footer>

    @isset($seo)
        <x-seo.json-ld :schemas="$seo->jsonLd" />
    @endisset

    @stack('scripts')
    <style>[x-cloak]{display:none!important}</style>
</body>
</html>
