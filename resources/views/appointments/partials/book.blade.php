<div
    class="overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-lg shadow-primary-900/8"
    x-data="appointmentBooking('{{ route('appointments.slots', $doctor) }}')"
>
    <div class="relative overflow-hidden px-6 py-5 text-white" style="background: linear-gradient(135deg, var(--color-primary-800) 0%, var(--color-primary-600) 100%);">
        <div class="pointer-events-none absolute -end-8 -top-8 size-28 rounded-full bg-accent-400/20 blur-2xl"></div>
        <div class="relative flex items-center gap-3">
            <span class="flex size-11 items-center justify-center rounded-2xl bg-white/15 text-white backdrop-blur-sm ring-1 ring-white/20">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </span>
            <div>
                <h2 class="text-lg font-extrabold">رزرو نوبت آنلاین</h2>
                <p class="text-xs text-white/80">تاریخ و ساعت دلخواه را انتخاب کنید</p>
            </div>
        </div>
    </div>

    <form action="{{ route('appointments.book', $doctor) }}" method="POST" class="space-y-4 p-6">
        @csrf

        <div>
            <label for="appointment_date" class="mb-1.5 flex items-center gap-1.5 text-sm font-semibold text-slate-700">
                <svg class="size-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                تاریخ نوبت
            </label>
            <input
                type="date"
                id="appointment_date"
                x-model="date"
                @change="loadSlots()"
                min="{{ now()->toDateString() }}"
                class="input-field"
                required
            >
        </div>

        <div x-show="loading" class="flex items-center gap-2 text-sm text-slate-500">
            <svg class="size-4 animate-spin text-primary-600" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            در حال بارگذاری زمان‌ها…
        </div>

        <div x-show="slots.length > 0 && !loading">
            <label class="mb-2 flex items-center gap-1.5 text-sm font-semibold text-slate-700">
                <svg class="size-4 text-accent-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                ساعت نوبت
            </label>
            <div class="grid grid-cols-3 gap-2">
                <template x-for="slot in slots" :key="slot.value">
                    <label class="cursor-pointer">
                        <input type="radio" name="starts_at" :value="slot.value" x-model="selectedSlot" class="peer sr-only" required>
                        <span class="block rounded-xl border border-slate-200 px-2 py-2.5 text-center text-sm font-medium transition peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:font-bold peer-checked:text-primary-800 peer-checked:shadow-sm hover:border-primary-200" x-text="slot.label"></span>
                    </label>
                </template>
            </div>
        </div>

        <div x-show="date && !loading && slots.length === 0" class="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-sm text-amber-800">
            <svg class="mt-0.5 size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            در این تاریخ نوبت خالی وجود ندارد.
        </div>

        <div class="flex items-center gap-3 pt-1">
            <span class="h-px flex-1 bg-slate-100"></span>
            <span class="text-xs font-bold text-slate-400">مشخصات بیمار</span>
            <span class="h-px flex-1 bg-slate-100"></span>
        </div>

        <div>
            <label for="patient_name" class="mb-1.5 block text-sm font-semibold text-slate-700">نام و نام خانوادگی</label>
            <input type="text" id="patient_name" name="patient_name" value="{{ old('patient_name') }}" class="input-field" autocomplete="name" required>
            @error('patient_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="patient_phone" class="mb-1.5 block text-sm font-semibold text-slate-700">شماره موبایل</label>
            <input type="tel" id="patient_phone" name="patient_phone" value="{{ old('patient_phone') }}" placeholder="09123456789" dir="ltr" class="input-field text-start" autocomplete="tel" required>
            @error('patient_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="patient_national_code" class="mb-1.5 block text-sm font-semibold text-slate-700">کد ملی</label>
            <input type="text" id="patient_national_code" name="patient_national_code" value="{{ old('patient_national_code') }}" maxlength="10" dir="ltr" class="input-field text-start" inputmode="numeric" required>
            @error('patient_national_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        @error('starts_at')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

        <button type="submit" class="btn-accent w-full !py-3.5 text-base">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            ثبت نوبت
        </button>

        <p class="flex items-center justify-center gap-1.5 text-center text-xs text-slate-400">
            <svg class="size-3.5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            پس از ثبت، کد رهگیری برای پیگیری نوبت دریافت می‌کنید.
        </p>
    </form>
</div>
