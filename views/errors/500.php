<?php $page_title = 'Error Interno del Servidor (500)'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>500 - Error Interno · mi_bodega</title>
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
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 shadow-sm mx-auto">
      <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
      </svg>
    </div>

    <div>
      <span class="text-xs font-mono font-bold uppercase tracking-wider text-rose-700 bg-rose-100 px-3 py-1 rounded-full">Error 500 · Error del Servidor</span>
      <h1 class="text-2xl font-bold text-gray-900 mt-3">Ocurrió un inconveniente inesperado</h1>
      <p class="text-xs text-gray-500 mt-2 leading-relaxed">
        <?= !empty($errorMessage) ? htmlspecialchars($errorMessage) : 'Se ha producido un error interno en el sistema. Por favor intenta de nuevo en unos momentos.' ?>
      </p>
    </div>

    <div class="pt-2">
      <a href="<?= url("categorias") ?>" class="btn btn-primary px-5 py-2.5 text-xs font-semibold inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        Reintentar / Volver al Inicio
      </a>
    </div>
  </div>

</body>
</html>
