<?php

function base_url($path = '') {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/' );
}

function redirect($path = '') {
    header('Location: ' . base_url($path));
    exit;
}

function set_flash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function flash($type) {
    if (!empty($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

function sanitize($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Formatea un monto con la convención local (1.234,56).
 * Úsalo en toda la UI para que las cifras no cambien de forma entre pantallas.
 */
function money($amount, $symbol = 'Bs') {
    $formatted = number_format((float) $amount, 2, ',', '.');
    return $symbol === '' ? $formatted : $symbol . ' ' . $formatted;
}

/** Cantidad sin decimales sobrantes: 2,5 kg pero 3 kg (no 3,00 kg). */
function qty($amount) {
    $n = (float) $amount;
    return $n == (int) $n
        ? number_format($n, 0, ',', '.')
        : rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',');
}