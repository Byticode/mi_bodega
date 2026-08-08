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
<body class="min-h-screen flex flex-col items-center justify-center p-6" style="background-color: var(--color-paper);">

  <div class="max-w-md w-full text-center space-y-6">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 shadow-sm mx-auto">
      <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.05a9 9 0 110 18 9 9 0 010-18zm-3.75 14.25h7.5" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
      </svg>
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
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Volver al Panel Principal
      </a>
    </div>
  </div>

</body>
</html>
