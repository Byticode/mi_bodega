<?php
/**
 * Router para el servidor built-in de PHP (php -S).
 *
 * Uso:  php -S localhost:3000 server.php
 *
 * Reproduce el comportamiento del .htaccess de Apache:
 *  1. Si el archivo o directorio solicitado existe físicamente → lo sirve tal cual.
 *  2. En cualquier otro caso → delega a index.php (front-controller).
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Eliminar el prefijo /mi_bodega si existe (el servidor se ejecuta desde la raíz del proyecto)
$basePath = '/mi_bodega';
if (str_starts_with($uri, $basePath)) {
    $localPath = substr($uri, strlen($basePath));
} else {
    $localPath = $uri;
}

// Normalizar ruta vacía a /
if ($localPath === '' || $localPath === false) {
    $localPath = '/';
}

// Si la ruta apunta a un archivo real en disco → servirlo directamente
$realFile = __DIR__ . $localPath;
if ($localPath !== '/' && file_exists($realFile) && is_file($realFile)) {
    $ext = strtolower(pathinfo($realFile, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css'   => 'text/css; charset=UTF-8',
        'js'    => 'application/javascript; charset=UTF-8',
        'json'  => 'application/json; charset=UTF-8',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'webp'  => 'image/webp',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'map'   => 'application/json',
    ];

    $contentType = $mimeTypes[$ext] ?? mime_content_type($realFile);
    header('Content-Type: ' . $contentType);
    header('Content-Length: ' . filesize($realFile));
    readfile($realFile);
    return;
}

// Cualquier otra ruta → front-controller
$_GET['url'] = ltrim($localPath, '/');
require __DIR__ . '/index.php';
