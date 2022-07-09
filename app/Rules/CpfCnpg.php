<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CpfCnpg implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // CPF
        if (preg_match('/^(\d{3}\.){2}\d{3}-\d{2}$/', $value)) {
            return true;
        }

        // CNPJ
        if (preg_match('/\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}/', $value)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The CPF or CNPF is invalid.';
    }
}
