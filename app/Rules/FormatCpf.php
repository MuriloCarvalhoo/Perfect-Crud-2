<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FormatCpf implements Rule
{

    public function passes($attribute, $value)
    {
        return preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $value) > 0;
    }

    public function message()
    {
        return 'O campo :attribute não possui o formato válido de CPF.';
    }
}
