<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** @deprecated Use NationalCodeRule */
final class IranNationalCode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        (new NationalCodeRule())->validate($attribute, $value, $fail);
    }
}
