<?php $page_title = 'Acceso Denegado (403)'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>403 - Acceso Denegado · mi_bodega</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
  <link rel="preload" href="<?= assets('vendor/tabler/fonts/tabler-icons.woff2') ?>" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="<?= assets('vendor/tabler/tabler-icons.min.css') ?>">
  <link rel="stylesheet" href="<?= url('assets/css/styles.css') ?>">
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6" style="background-color: var(--color-paper);">

  <div class="max-w-md w-full text-center space-y-6">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 shadow-sm mx-auto">
      <i class="ti ti-alert-circle text-3xl" aria-hidden="true"></i>
    </div>

    <div>
      <span class="text-xs font-mono font-bold uppercase tracking-wider text-amber-700 bg-amber-100 px-3 py-1 rounded-full">Error 403 · Acceso Prohibido</span>
      <h1 class="text-2xl font-bold text-gray-900 mt-3">No tienes permisos para este recurso</h1>
      <p class="text-xs text-gray-500 mt-2 leading-relaxed">
        <?= !empty($errorMessage) ? htmlspecialchars($errorMessage) : 'Tu cuenta actual no cuenta con el rol requerido (Administrador) para acceder a este módulo.' ?>
      </p>
    </div>

    <div class="pt-2">
      <a href="<?= url("categorias") ?>" class="btn btn-primary px-5 py-2.5 text-xs font-semibold inline-flex items-center gap-2">
        <i class="ti ti-arrow-left text-base" aria-hidden="true"></i>
        Volver al Panel Principal
      </a>
    </div>
  </div>

</body>
</html>
