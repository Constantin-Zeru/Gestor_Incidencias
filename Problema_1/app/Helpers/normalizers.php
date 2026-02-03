<?php
// app/Helpers/normalizers.php

if (! function_exists('normalize_phone')) {
    /**
     * Normaliza teléfono: elimina todo excepto dígitos, devuelve cadena vacía si none.
     */
    function normalize_phone(?string $phone): string
    {
        if (empty($phone)) return '';
        return preg_replace('/\D+/', '', $phone);
    }
}

if (! function_exists('normalize_cif')) {
    /**
     * Normaliza CIF/CIF-like: mayúsculas y trim.
     */
    function normalize_cif(?string $cif): string
    {
        return strtoupper(trim((string)($cif ?? '')));
    }
}

if (! function_exists('normalize_text')) {
    function normalize_text(?string $s): string
    {
        return trim((string)($s ?? ''));
    }
}

if (! function_exists('normalize_email')) {
    function normalize_email(?string $email): string
    {
        return strtolower(trim((string)($email ?? '')));
    }
}
