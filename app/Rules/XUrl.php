<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class XUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
            // Verifica si la URL es un enlace de "X" válido
        if (!preg_match('/^(https?:\/\/)?(www\.)?x\.com\/.+$/', $value)) {
            $fail('Este campo debe ser una URL válida de "X".');
        }
    }
}