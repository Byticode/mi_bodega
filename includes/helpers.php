<?php

function base_url($path = '') {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function url($route = '') {
    return base_url($route);
}

function assets($path = '') {
    $cleanPath = ltrim($path, '/');
    if (strpos($cleanPath, 'assets/') === 0) {
        return url($cleanPath);
    }
    return url('assets/' . $cleanPath);
}

function redirect($path = '') {
    header('Location: ' . base_url($path));
    exit;
}

function set_flash($type, $message) {
    $_SESSION['flash'][$type] = $message;
    $_SESSION[$type] = $message;
}

function flash($type) {
    $message = $_SESSION['flash'][$type] ?? $_SESSION[$type] ?? null;
    unset($_SESSION['flash'][$type], $_SESSION[$type]);
    return $message;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function sanitize($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function old($key, $default = '') {
    return isset($_SESSION['old'][$key]) ? sanitize($_SESSION['old'][$key]) : $default;
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
 * Formatea un monto en dólares (USD).
 */
function usd($amount, $con_simbolo = true) {
    if ($amount === null || $amount === '') {
        return '';
    }
    $formatted = number_format((float) $amount, 2, ',', '.');
    return $con_simbolo ? '$ ' . $formatted : $formatted;
}

/**
 * Convierte un monto en dólares (USD) a bolívares (Bs) con la tasa oficial del BCV.
 */
function bs($amount_usd, $con_simbolo = true) {
    if ($amount_usd === null || $amount_usd === '') {
        return '';
    }
    $tasa = tasa_vigente();

    if (empty($tasa['tasa_usd']) || $tasa['tasa_usd'] <= 0) {
        return '';
    }

    $valor_bs = (float) $amount_usd * (float) $tasa['tasa_usd'];
    return money($valor_bs, $con_simbolo ? 'Bs' : '');
}

/** Cantidad sin decimales sobrantes: 2,5 kg pero 3 kg (no 3,00 kg). */
function qty($amount) {
    $n = (float) $amount;
    return $n == (int) $n
        ? number_format($n, 0, ',', '.')
        : rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',');
}