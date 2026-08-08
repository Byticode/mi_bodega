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

/**
 * Tasa de cambio vigente, memorizada por petición.
 * Una sola consulta aunque la llamen el sidebar, el POS y la tabla de precios.
 */
function tasa_vigente(): array {
    static $tasa = null;

    if ($tasa === null) {
        $tasa = (new TasaService())->vigente();
    }

    return $tasa;
}

/**
 * Convierte un monto en bolívares a dólares con la tasa oficial del BCV.
 * Devuelve cadena vacía si todavía no hay tasa conocida: es preferible no
 * mostrar nada a mostrar una conversión inventada.
 */
function usd($amount, $con_simbolo = true) {
    $tasa = tasa_vigente();

    if (empty($tasa['tasa_usd']) || $tasa['tasa_usd'] <= 0) {
        return '';
    }

    $valor = number_format((float) $amount / (float) $tasa['tasa_usd'], 2, ',', '.');

    return $con_simbolo ? '$ ' . $valor : $valor;
}

/** Cantidad sin decimales sobrantes: 2,5 kg pero 3 kg (no 3,00 kg). */
function qty($amount) {
    $n = (float) $amount;
    return $n == (int) $n
        ? number_format($n, 0, ',', '.')
        : rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',');
}