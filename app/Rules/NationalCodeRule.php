<?php

declare(strict_types=1);

namespace App\Rules;

use App\Helpers\PersianTextHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class NationalCodeRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) && ! is_numeric($value)) {
            $fail('کد ملی نامعتبر است.');

            return;
        }

        $code = PersianTextHelper::toEnglishDigits(trim((string) $value));
        $code = str_pad($code, 10, '0', STR_PAD_LEFT);

        if (! preg_match('/^\d{10}$/', $code)) {
            $fail('کد ملی باید ۱۰ رقم باشد.');

            return;
        }

        if (preg_match('/^(\d)\1{9}$/', $code)) {
            $fail('کد ملی نامعتبر است.');

            return;
        }

        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $code[$i] * (10 - $i);
        }

        $remainder = $sum % 11;
        $checkDigit = (int) $code[9];
        $expected = $remainder < 2 ? $remainder : 11 - $remainder;

        if ($checkDigit !== $expected) {
            $fail('کد ملی نامعتبر است.');
        }
    }
}
