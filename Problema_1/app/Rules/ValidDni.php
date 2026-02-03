<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidDni implements Rule
{
    /**
     * Comprueba si la cadena es un DNI/NIE válido (formato y letra).
     * Soporta DNI (8 dígitos + letra) y NIE (X/Y/Z + 7 dígitos + letra).
     */
    public function passes($attribute, $value)
    {
        if (!is_string($value) || $value === '') {
            return false;
        }

        $v = strtoupper(trim($value));
        // quitar espacios y guiones
        $v = preg_replace('/[\s\-]+/', '', $v);

        // NIE empieza por X/Y/Z -> sustituimos por 0/1/2 para el cálculo
        if (preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $v)) {
            $map = ['X' => '0', 'Y' => '1', 'Z' => '2'];
            $first = $v[0];
            $v = $map[$first] . substr($v, 1);
        }

        // ahora esperamos 8 cifras + letra
        if (!preg_match('/^([0-9]{8})([A-Z])$/', $v, $matches)) {
            return false;
        }

        $num = $matches[1];
        $letter = $matches[2];

        // tabla de letras del DNI
        $letters = "TRWAGMYFPDXBNJZSQVHLCKE";
        $index = intval($num) % 23;
        $expected = $letters[$index];

        return $letter === $expected;
    }

    public function message()
    {
        return 'El :attribute no tiene un DNI/NIE válido.';
    }
}
