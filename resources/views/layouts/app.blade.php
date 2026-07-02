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
<body class="flex min-h-screen flex-col" x-data="mobileNav">
    <header class="glass-header sticky top-0 z-50">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="group flex items-center gap-3">
                <span class="logo-mark">VI</span>
                <div class="hidden sm:block">
                    <span class="block text-lg font-extrabold tracking-tight text-primary-900">{{ $siteName ?? config('visitiranian.site_name') }}</span>
                    <span class="block text-xs text-slate-500">معرفی پزشکان برتر ایران</span>
                </div>
            </a>

            <nav class="hidden items-center gap-1 md:flex" aria-label="منوی اصلی">
                <a href="{{ route('home') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('home')])>خانه</a>
                <a href="{{ route('doctors.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('doctors.*') || request()->routeIs('specialties.*') || request()->routeIs('cities.*')])>پزشکان</a>
                <a href="{{ route('peygiri') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('peygiri*')])>پیگیری نوبت</a>
                <a href="{{ route('appointments.track') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('appointments.track*')])>کد رهگیری</a>
            </nav>

            <div class="hidden items-center gap-3 md:flex">
                <form action="{{ route('doctors.index') }}" method="GET" class="relative">
                    <input
                        type="search"
                        name="q"
                        placeholder="جستجوی پزشک…"
                        class="w-48 rounded-xl border border-slate-200 bg-slate-50/80 py-2 pe-3 ps-9 text-sm transition focus:w-56 focus:border-primary-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-100 lg:w-56 lg:focus:w-64"
                        autocomplete="off"
                    >
                    <svg class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </form>
                <a href="{{ route('doctors.index') }}" class="btn-primary text-sm">رزرو نوبت</a>
            </div>

            <button
                type="button"
                class="inline-flex items-center justify-center rounded-xl p-2.5 text-slate-600 transition hover:bg-slate-100 md:hidden"
                @click="toggle()"
                aria-label="باز کردن منو"
            >
                <svg x-show="!open" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" x-cloak class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div
            x-show="open"
            x-cloak
            x-transition
            class="border-t border-slate-100 bg-white/95 backdrop-blur-xl md:hidden"
            @click.outside="close()"
        >
            <nav class="flex flex-col gap-1 px-4 py-4" aria-label="منوی موبایل">
                <form action="{{ route('doctors.index') }}" method="GET" class="mb-3">
                    <input type="search" name="q" placeholder="جستجوی پزشک…" class="input-field">
                </form>
                <a href="{{ route('home') }}" @class(['rounded-xl px-3 py-2.5 text-sm font-medium transition hover:bg-primary-50', 'bg-primary-50 text-primary-700' => request()->routeIs('home'), 'text-slate-700' => ! request()->routeIs('home')]) @click="close()">خانه</a>
                <a href="{{ route('doctors.index') }}" @class(['rounded-xl px-3 py-2.5 text-sm font-medium transition hover:bg-primary-50', 'bg-primary-50 text-primary-700' => request()->routeIs('doctors.*'), 'text-slate-700' => ! request()->routeIs('doctors.*')]) @click="close()">پزشکان</a>
                <a href="{{ route('peygiri') }}" @class(['rounded-xl px-3 py-2.5 text-sm font-medium transition hover:bg-primary-50', 'bg-primary-50 text-primary-700' => request()->routeIs('peygiri*'), 'text-slate-700' => ! request()->routeIs('peygiri*')]) @click="close()">پیگیری نوبت</a>
                <a href="{{ route('appointments.track') }}" @class(['rounded-xl px-3 py-2.5 text-sm font-medium transition hover:bg-primary-50', 'bg-primary-50 text-primary-700' => request()->routeIs('appointments.track*'), 'text-slate-700' => ! request()->routeIs('appointments.track*')]) @click="close()">کد رهگیری</a>
            </nav>
        </div>
    </header>

    @if (session('success'))
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8" x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-800 shadow-sm" role="alert">
                <svg class="mt-0.5 size-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="flex-1">{{ session('success') }}</span>
                <button type="button" @click="show = false" class="shrink-0 rounded-lg p-1 text-emerald-500 transition hover:bg-emerald-100" aria-label="بستن">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="footer-gradient mt-auto">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3">
                        <span class="logo-mark">VI</span>
                        <span class="text-xl font-extrabold text-primary-900">{{ $siteName ?? config('visitiranian.site_name') }}</span>
                    </div>
                    <p class="mt-4 max-w-md text-sm leading-relaxed text-slate-600">
                        بزرگ‌ترین پلتفرم معرفی پزشکان ایران — جستجو بر اساس تخصص و شهر، مشاهده نظرات بیماران و رزرو نوبت آنلاین.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="badge-soft">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            پزشکان تأییدشده
                        </span>
                        <span class="badge-soft">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            رزرو ۲۴ ساعته
                        </span>
                        <span class="badge-soft">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            نظرات واقعی بیماران
                        </span>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900">دسترسی سریع</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
                        <li><a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 transition hover:text-primary-700 hover:gap-2.5">خانه</a></li>
                        <li><a href="{{ route('doctors.index') }}" class="inline-flex items-center gap-1.5 transition hover:text-primary-700 hover:gap-2.5">لیست پزشکان</a></li>
                        <li><a href="{{ route('peygiri') }}" class="inline-flex items-center gap-1.5 transition hover:text-primary-700 hover:gap-2.5">پیگیری نوبت</a></li>
                        <li><a href="{{ route('appointments.track') }}" class="inline-flex items-center gap-1.5 transition hover:text-primary-700 hover:gap-2.5">جستجو با کد رهگیری</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900">ارتباط با ما</h4>
                    @if (($socialLinks ?? collect())->isNotEmpty())
                        <ul class="mt-4 flex flex-wrap gap-3">
                            @foreach ($socialLinks as $link)
                                <li>
                                    <a href="{{ $link->url }}" target="_blank" rel="noopener" class="badge-soft transition hover:bg-primary-100">
                                        {{ $link->label ?: $link->platform }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-4 text-sm text-slate-500">به زودی…</p>
                    @endif
                </div>
            </div>
            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-slate-200/80 pt-8 text-xs text-slate-400 sm:flex-row">
                <p>&copy; {{ \Morilog\Jalali\Jalalian::now()->format('Y') }} {{ $siteName ?? config('visitiranian.site_name') }}. تمامی حقوق محفوظ است.</p>
                <p class="flex items-center gap-1.5">
                    ساخته شده با
                    <svg class="size-4 text-accent-500" fill="currentColor" viewBox="0 0 20 20" aria-label="عشق"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                    برای سلامت ایرانیان
                </p>
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
