<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidSku implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Expresión regular: Letras mayúsculas, números y guiones medios. Entre 3 y 20 caracteres.
        if (!preg_match('/^PROD-\d{3}$/', $value)) {
            $fail('El SKU debe tener el formato PROD-000.');
        }
    }
}
