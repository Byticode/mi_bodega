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