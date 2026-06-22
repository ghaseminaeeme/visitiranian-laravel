@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6 text-center">
            <h1 class="section-title">پیگیری نوبت</h1>
            <p class="mt-2 text-sm text-slate-600">با شماره موبایل و کد ملی، نوبت‌های فعال خود را مشاهده کنید.</p>
        </div>

        <div class="card p-6">
            <form action="{{ route('peygiri.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="patient_phone" class="mb-1 block text-sm font-medium text-slate-700">شماره موبایل</label>
                    <input
                        type="tel"
                        id="patient_phone"
                        name="patient_phone"
                        value="{{ old('patient_phone', $patientPhone ?? '') }}"
                        placeholder="09123456789"
                        dir="ltr"
                        class="input-field"
                        required
                    >
                    @error('patient_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="patient_national_code" class="mb-1 block text-sm font-medium text-slate-700">کد ملی</label>
                    <input
                        type="text"
                        id="patient_national_code"
                        name="patient_national_code"
                        value="{{ old('patient_national_code', $patientNationalCode ?? '') }}"
                        maxlength="10"
                        dir="ltr"
                        class="input-field"
                        required
                    >
                    @error('patient_national_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-primary w-full">جستجو</button>
            </form>
        </div>

        @if ($appointments->isNotEmpty())
            <div class="mt-6 space-y-4">
                @foreach ($appointments as $appointment)
                    <div class="card p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="font-bold text-slate-900">{{ $appointment->doctor->name }}</h2>
                                <p class="mt-1 text-sm text-primary-700">{{ $appointment->doctor->primarySpecialty?->name }}</p>
                            </div>
                            <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">فعال</span>
                        </div>
                        <dl class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-slate-500">تاریخ</dt>
                                <dd>{{ \Morilog\Jalali\Jalalian::fromDateTime($appointment->starts_at)->format('Y/m/d H:i') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-slate-500">کد رهگیری</dt>
                                <dd class="font-mono font-bold text-primary-800" dir="ltr">{{ $appointment->tracking_code }}</dd>
                            </div>
                        </dl>
                    </div>
                @endforeach
            </div>
        @elseif (isset($patientPhone))
            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                نوبت فعالی یافت نشد.
            </div>
        @endif
    </div>
@endsection
