<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TikTokUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^(https?:\/\/)?(www\.)?tiktok\.com\/.+$/', $value)) {
            $fail('Este campo debe ser una URL válida de TikTok.');
        }
    }
}