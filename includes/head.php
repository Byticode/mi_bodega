<?php
// Cabecera HTML compartida. Cada vista define $page_title (y opcionalmente
// $page_desc) antes de incluir este archivo.
$page_title = $page_title ?? 'Panel';
$page_desc  = $page_desc  ?? 'Control de mercancía, ventas y surtido para tu bodega.';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta name="theme-color" content="#3a6341">
  <meta name="color-scheme" content="light">
  <title><?= htmlspecialchars($page_title) ?> · mi_bodega</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
  <!-- Iconos: Tabler Icons (webfont autoalojada). Se precarga para que no
       aparezca un hueco donde va cada icono en la primera pintada. -->
  <link rel="preload" href="assets/vendor/tabler/fonts/tabler-icons.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="assets/vendor/tabler/tabler-icons.min.css">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body class="min-h-screen">
  <a href="#contenido" class="skip-link">Saltar al contenido</a>
