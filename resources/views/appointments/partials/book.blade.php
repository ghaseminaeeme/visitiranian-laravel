<div
    class="card-elevated p-6"
    x-data="appointmentBooking('{{ route('appointments.slots', $doctor) }}')"
>
    <div class="mb-5 flex items-center gap-3">
        <span class="flex size-10 items-center justify-center rounded-xl bg-primary-100 text-primary-700">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </span>
        <h2 class="text-lg font-bold text-slate-900">رزرو نوبت</h2>
    </div>

    <form action="{{ route('appointments.book', $doctor) }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="appointment_date" class="mb-1 block text-sm font-medium text-slate-700">تاریخ</label>
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

        <div x-show="loading" class="text-sm text-slate-500">در حال بارگذاری زمان‌ها…</div>

        <div x-show="slots.length > 0 && !loading">
            <label class="mb-2 block text-sm font-medium text-slate-700">ساعت</label>
            <div class="grid grid-cols-3 gap-2">
                <template x-for="slot in slots" :key="slot.value">
                    <label class="cursor-pointer">
                        <input type="radio" name="starts_at" :value="slot.value" x-model="selectedSlot" class="peer sr-only" required>
                        <span class="block rounded-lg border border-slate-200 px-3 py-2 text-center text-sm transition peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:font-semibold peer-checked:text-primary-800" x-text="slot.label"></span>
                    </label>
                </template>
            </div>
        </div>

        <div x-show="date && !loading && slots.length === 0">
            <p class="text-sm text-slate-500">در این تاریخ نوبت خالی وجود ندارد.</p>
        </div>

        <div>
            <label for="patient_name" class="mb-1 block text-sm font-medium text-slate-700">نام و نام خانوادگی</label>
            <input type="text" id="patient_name" name="patient_name" value="{{ old('patient_name') }}" class="input-field" required>
            @error('patient_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="patient_phone" class="mb-1 block text-sm font-medium text-slate-700">شماره موبایل</label>
            <input type="tel" id="patient_phone" name="patient_phone" value="{{ old('patient_phone') }}" placeholder="09123456789" dir="ltr" class="input-field text-start" required>
            @error('patient_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="patient_national_code" class="mb-1 block text-sm font-medium text-slate-700">کد ملی</label>
            <input type="text" id="patient_national_code" name="patient_national_code" value="{{ old('patient_national_code') }}" maxlength="10" dir="ltr" class="input-field text-start" required>
            @error('patient_national_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        @error('starts_at')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

        <button type="submit" class="btn-primary w-full">ثبت نوبت</button>
    </form>
</div>
