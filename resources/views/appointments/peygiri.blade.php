@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <span class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 text-white shadow-lg shadow-primary-700/30">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </span>
            <h1 class="section-title">پیگیری نوبت</h1>
            <p class="mt-2 text-sm text-slate-600">با شماره موبایل و کد ملی، نوبت‌های فعال خود را مشاهده کنید.</p>
        </div>

        <div class="card-elevated p-6 md:p-7">
            <form action="{{ route('peygiri.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="patient_phone" class="mb-1.5 block text-sm font-medium text-slate-700">شماره موبایل</label>
                    <input
                        type="tel"
                        id="patient_phone"
                        name="patient_phone"
                        value="{{ old('patient_phone', $patientPhone ?? '') }}"
                        placeholder="09123456789"
                        dir="ltr"
                        class="input-field text-start"
                        required
                    >
                    @error('patient_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="patient_national_code" class="mb-1.5 block text-sm font-medium text-slate-700">کد ملی</label>
                    <input
                        type="text"
                        id="patient_national_code"
                        name="patient_national_code"
                        value="{{ old('patient_national_code', $patientNationalCode ?? '') }}"
                        maxlength="10"
                        dir="ltr"
                        class="input-field text-start"
                        required
                    >
                    @error('patient_national_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-primary w-full">جستجوی نوبت‌ها</button>
            </form>
        </div>

        @if ($appointments->isNotEmpty())
            <div class="mt-8 space-y-4">
                <h2 class="text-sm font-semibold text-slate-500">{{ count($appointments) }} نوبت فعال</h2>
                @foreach ($appointments as $appointment)
                    <div class="card card-hover p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="icon-badge">
                                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </span>
                                <div>
                                    <h3 class="font-bold text-slate-900">{{ $appointment->doctor->name }}</h3>
                                    <p class="mt-0.5 text-sm text-primary-700">{{ $appointment->doctor->primarySpecialty?->name }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                فعال
                            </span>
                        </div>
                        <dl class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 text-sm">
                            <div>
                                <dt class="text-xs text-slate-400">تاریخ و ساعت</dt>
                                <dd class="mt-0.5 font-medium text-slate-800">{{ \Morilog\Jalali\Jalalian::fromDateTime($appointment->starts_at)->format('Y/m/d H:i') }}</dd>
                            </div>
                            <div class="text-end">
                                <dt class="text-xs text-slate-400">کد رهگیری</dt>
                                <dd class="mt-0.5 font-mono font-bold text-primary-800" dir="ltr">{{ $appointment->tracking_code }}</dd>
                            </div>
                        </dl>
                    </div>
                @endforeach
            </div>
        @elseif (isset($patientPhone))
            <div class="mt-8 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                <svg class="mt-0.5 size-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19H19a2 2 0 001.75-2.96l-6.93-12a2 2 0 00-3.48 0l-6.93 12A2 2 0 005.07 19z"/></svg>
                نوبت فعالی یافت نشد.
            </div>
        @endif
    </div>
@endsection
