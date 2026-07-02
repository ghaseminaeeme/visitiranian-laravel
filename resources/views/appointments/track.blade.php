@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <span class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 text-white shadow-lg shadow-primary-700/30">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
            <h1 class="section-title">پیگیری با کد رهگیری</h1>
            <p class="mt-2 text-sm text-slate-600">کد ۸ کاراکتری دریافتی پس از ثبت نوبت را وارد کنید.</p>
        </div>

        <div class="card-elevated p-6 md:p-7">
            <form action="{{ route('appointments.track.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="tracking_code" class="mb-1.5 block text-sm font-medium text-slate-700">کد رهگیری</label>
                    <input
                        type="text"
                        id="tracking_code"
                        name="tracking_code"
                        value="{{ old('tracking_code', $trackingCode ?? '') }}"
                        maxlength="8"
                        dir="ltr"
                        class="input-field text-center text-lg font-mono tracking-[0.3em] uppercase"
                        required
                        autofocus
                    >
                    @error('tracking_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-primary w-full">جستجو</button>
            </form>
        </div>

        @if (isset($appointment))
            @if ($appointment)
                <div class="card-elevated mt-8 overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-primary-50/40 px-6 py-4">
                        <h2 class="font-bold text-slate-900">جزئیات نوبت</h2>
                        <span @class([
                            'inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold',
                            'bg-emerald-100 text-emerald-800' => $appointment->status === 'confirmed',
                            'bg-amber-100 text-amber-800' => $appointment->status === 'pending',
                            'bg-red-100 text-red-800' => $appointment->status === 'cancelled',
                            'bg-slate-100 text-slate-700' => ! in_array($appointment->status, ['confirmed', 'pending', 'cancelled']),
                        ])>
                            @switch($appointment->status)
                                @case('confirmed') تأیید شده @break
                                @case('pending') در انتظار @break
                                @case('cancelled') لغو شده @break
                                @default {{ $appointment->status }}
                            @endswitch
                        </span>
                    </div>
                    <dl class="divide-y divide-slate-100 px-6 text-sm">
                        <div class="flex items-center justify-between gap-4 py-3.5">
                            <dt class="text-slate-500">کد رهگیری</dt>
                            <dd class="font-mono font-bold text-primary-800" dir="ltr">{{ $appointment->tracking_code }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3.5">
                            <dt class="text-slate-500">پزشک</dt>
                            <dd class="font-medium text-slate-800">{{ $appointment->doctor->name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3.5">
                            <dt class="text-slate-500">تاریخ و ساعت</dt>
                            <dd class="text-slate-800">{{ \Morilog\Jalali\Jalalian::fromDateTime($appointment->starts_at)->format('Y/m/d H:i') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3.5">
                            <dt class="text-slate-500">بیمار</dt>
                            <dd class="text-slate-800">{{ $appointment->patient_name }}</dd>
                        </div>
                    </dl>
                    @if ($appointment->status === 'confirmed' && $appointment->starts_at->isFuture())
                        <form action="{{ route('appointments.cancel') }}" method="POST" class="space-y-3 border-t border-slate-100 bg-red-50/30 px-6 py-5" onsubmit="return confirm('آیا از لغو نوبت مطمئن هستید؟')">
                            @csrf
                            <input type="hidden" name="tracking_code" value="{{ $appointment->tracking_code }}">
                            <label for="cancellation_reason" class="block text-sm font-medium text-slate-700">دلیل لغو (اختیاری)</label>
                            <textarea id="cancellation_reason" name="cancellation_reason" rows="2" class="input-field"></textarea>
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-5 py-2.5 text-sm font-semibold text-red-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-red-50 hover:shadow focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                لغو نوبت
                            </button>
                        </form>
                    @endif
                </div>
            @elseif ($trackingCode ?? old('tracking_code'))
                <div class="mt-8 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <svg class="mt-0.5 size-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19H19a2 2 0 001.75-2.96l-6.93-12a2 2 0 00-3.48 0l-6.93 12A2 2 0 005.07 19z"/></svg>
                    نوبتی با این کد رهگیری یافت نشد.
                </div>
            @endif
        @endif
    </div>
@endsection
