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
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            warmBg: '#fcfbf7',
            warmCard: '#f5f3ec',
            olive: { DEFAULT: '#3a6341', hover: '#2f5135', light: '#eaf0eb' }
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="<?= url('assets/css/styles.css') ?>">
</head>

<body class="min-h-screen flex">
  <a href="#contenido" class="skip-link">Saltar al contenido</a>
  <?php include RUTA_APP . '/includes/spinner.php'; ?>
