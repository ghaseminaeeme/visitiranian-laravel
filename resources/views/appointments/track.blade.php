@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6 text-center">
            <h1 class="section-title">پیگیری با کد رهگیری</h1>
            <p class="mt-2 text-sm text-slate-600">کد ۸ کاراکتری دریافتی پس از ثبت نوبت را وارد کنید.</p>
        </div>

        <div class="card p-6">
            <form action="{{ route('appointments.track.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="tracking_code" class="mb-1 block text-sm font-medium text-slate-700">کد رهگیری</label>
                    <input
                        type="text"
                        id="tracking_code"
                        name="tracking_code"
                        value="{{ old('tracking_code', $trackingCode ?? '') }}"
                        maxlength="8"
                        dir="ltr"
                        class="input-field text-center text-lg tracking-widest uppercase"
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
                <div class="card mt-6 p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="font-bold text-slate-900">جزئیات نوبت</h2>
                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-semibold',
                            'bg-green-100 text-green-800' => $appointment->status === 'confirmed',
                            'bg-yellow-100 text-yellow-800' => $appointment->status === 'pending',
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
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">کد رهگیری</dt>
                            <dd class="font-mono font-bold text-primary-800" dir="ltr">{{ $appointment->tracking_code }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">پزشک</dt>
                            <dd class="font-medium">{{ $appointment->doctor->name }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">تاریخ و ساعت</dt>
                            <dd>{{ \Morilog\Jalali\Jalalian::fromDateTime($appointment->starts_at)->format('Y/m/d H:i') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">بیمار</dt>
                            <dd>{{ $appointment->patient_name }}</dd>
                        </div>
                    </dl>
                    @if ($appointment->status === 'confirmed' && $appointment->starts_at->isFuture())
                        <form action="{{ route('appointments.cancel') }}" method="POST" class="mt-6 space-y-3 border-t border-slate-100 pt-4" onsubmit="return confirm('آیا از لغو نوبت مطمئن هستید؟')">
                            @csrf
                            <input type="hidden" name="tracking_code" value="{{ $appointment->tracking_code }}">
                            <label for="cancellation_reason" class="block text-sm font-medium text-slate-700">دلیل لغو (اختیاری)</label>
                            <textarea id="cancellation_reason" name="cancellation_reason" rows="2" class="input-field text-base"></textarea>
                            <button type="submit" class="btn-secondary w-full border-red-200 text-red-700 hover:bg-red-50">لغو نوبت</button>
                        </form>
                    @endif
                </div>
            @elseif ($trackingCode ?? old('tracking_code'))
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    نوبتی با این کد رهگیری یافت نشد.
                </div>
            @endif
        @endif
    </div>
@endsection
