<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Cpf implements Rule
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
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $value);

        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais (CPF inválido)
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Valida primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }
        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;

        if (intval($cpf[9]) != $digito1) {
            return false;
        }

        // Valida segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }
        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;

        if (intval($cpf[10]) != $digito2) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O CPF informado é inválido.';
    }
}

