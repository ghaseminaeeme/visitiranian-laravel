<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\PersianTextHelper;
use App\Rules\IranNationalCode;
use Illuminate\Foundation\Http\FormRequest;

final class BookAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'starts_at' => ['required', 'date', 'after:now'],
            'patient_name' => ['required', 'string', 'max:100'],
            'patient_phone' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'patient_national_code' => ['required', 'string', new IranNationalCode],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'starts_at.required' => 'زمان نوبت الزامی است.',
            'starts_at.after' => 'زمان نوبت باید در آینده باشد.',
            'patient_name.required' => 'نام بیمار الزامی است.',
            'patient_phone.required' => 'شماره موبایل الزامی است.',
            'patient_phone.regex' => 'شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم باشد.',
            'patient_national_code.required' => 'کد ملی الزامی است.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('patient_phone')) {
            $this->merge([
                'patient_phone' => PersianTextHelper::toEnglishDigits((string) $this->input('patient_phone')),
            ]);
        }

        if ($this->has('patient_national_code')) {
            $this->merge([
                'patient_national_code' => PersianTextHelper::toEnglishDigits((string) $this->input('patient_national_code')),
            ]);
        }
    }
}
