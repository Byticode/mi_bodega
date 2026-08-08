<?php $page_title = 'Página No Encontrada (404)'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 - No Encontrado · mi_bodega</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
  <link rel="preload" href="<?= assets('vendor/tabler/fonts/tabler-icons.woff2') ?>" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="<?= assets('vendor/tabler/tabler-icons.min.css') ?>">
  <link rel="stylesheet" href="<?= url('assets/css/styles.css') ?>">
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6" style="background-color: var(--color-paper);">

  <div class="max-w-md w-full text-center space-y-6">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gray-100 text-gray-600 border border-gray-200 shadow-sm mx-auto">
      <i class="ti ti-alert-triangle text-3xl" aria-hidden="true"></i>
    </div>

    <div>
      <span class="text-xs font-mono font-bold uppercase tracking-wider text-gray-700 bg-gray-200 px-3 py-1 rounded-full">Error 404 · No Encontrado</span>
      <h1 class="text-2xl font-bold text-gray-900 mt-3">Página o recurso inexistente</h1>
      <p class="text-xs text-gray-500 mt-2 leading-relaxed">
        <?= !empty($errorMessage) ? htmlspecialchars($errorMessage) : 'La ruta o acción a la que intentas acceder no existe o fue movida.' ?>
      </p>
    </div>

    <div class="pt-2">
      <a href="<?= url("categorias") ?>" class="btn btn-primary px-5 py-2.5 text-xs font-semibold inline-flex items-center gap-2">
        <i class="ti ti-arrow-left text-base" aria-hidden="true"></i>
        Regresar al Inicio
      </a>
    </div>
  </div>

</body>
</html>
