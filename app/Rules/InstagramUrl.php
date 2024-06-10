<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InstagramUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^(https?:\/\/)?(www\.)?instagram\.com\/.+$/', $value)) {
            $fail('Esta campo debe ser una URL válida de Instagram.');
        }
    }
}