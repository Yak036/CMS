<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FacebookUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Verifica si la URL es un enlace de Facebook válido
        if (!preg_match('/^(https?:\/\/)?(www\.)?facebook\.com\/.+$/', $value)) {
            $fail('Este campo debe ser una URL válida de Facebook.');
        }
    }
}