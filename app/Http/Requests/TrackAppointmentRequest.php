<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\PersianTextHelper;
use Illuminate\Foundation\Http\FormRequest;

final class TrackAppointmentRequest extends FormRequest
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
            'tracking_code' => ['required', 'string', 'size:8'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tracking_code.required' => 'کد رهگیری الزامی است.',
            'tracking_code.size' => 'کد رهگیری باید ۸ کاراکتر باشد.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('tracking_code')) {
            $this->merge([
                'tracking_code' => strtoupper(PersianTextHelper::toEnglishDigits((string) $this->input('tracking_code'))),
            ]);
        }
    }
}
