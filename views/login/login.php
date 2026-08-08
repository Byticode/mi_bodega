<?php 
$page_title = 'Iniciar Sesión'; 
$errorMessage = flash('error');
$successMessage = flash('success');
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?> · Mi Bodega</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
  <link rel="preload" href="<?= assets('vendor/tabler/fonts/tabler-icons.woff2') ?>" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="<?= assets('vendor/tabler/tabler-icons.min.css') ?>">
  <link rel="stylesheet" href="<?= url('assets/css/styles.css') ?>">
</head>

<body class="min-h-screen flex items-center justify-center p-4 bg-warmBg text-gray-800">

  <div class="w-full mx-auto space-y-6" style="max-width: 400px;">

    <!-- Logo / Brand -->
    <div class="flex flex-col items-center justify-center text-center space-y-2">
      <div class="w-20 h-20 bg-olive text-white font-bold text-lg rounded-2xl flex items-center justify-center shadow-md overflow-hidden shrink-0">
        <img src="<?= url('assets/images/logo.png') ?>" alt="Logo Mi Bodega" class="w-full h-full object-cover">
      </div>
      <div>
        <h1 class="font-bold text-2xl text-gray-900 leading-none tracking-tight">Mi Bodega</h1>
        <span class="text-xs text-gray-500 mt-1 block font-medium">Control de mercancía y ventas</span>
      </div>
    </div>

    <!-- Card Principal -->
    <div class="p-6 sm:p-7 space-y-5 bg-white border border-gray-200 rounded-2xl shadow-sm">

      <div>
        <h2 class="text-lg font-bold text-gray-900">Iniciar sesión</h2>
        <p class="text-xs text-gray-500 mt-0.5">Ingresa tus credenciales para acceder</p>
      </div>

      <!-- Alertas de Sesión -->
      <?php if (!empty($errorMessage)): ?>
        <div class="flex items-start gap-3 p-3.5 text-xs text-rose-800 border border-rose-200/80 rounded-xl bg-rose-50/90 shadow-sm" role="alert">
          <i class="ti ti-alert-triangle text-rose-600 shrink-0 mt-0.5 text-base" aria-hidden="true"></i>
          <div class="flex-1 leading-relaxed">
            <span class="font-bold block text-rose-900 mb-0.5">Acceso Denegado</span>
            <?= htmlspecialchars($errorMessage) ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if (!empty($successMessage)): ?>
        <div class="flex items-start gap-3 p-3.5 text-xs text-emerald-800 border border-emerald-200/80 rounded-xl bg-emerald-50/90 shadow-sm" role="status">
          <i class="ti ti-circle-check text-emerald-600 shrink-0 mt-0.5 text-base" aria-hidden="true"></i>
          <div class="flex-1 leading-relaxed">
            <span class="font-bold block text-emerald-900 mb-0.5">Notificación</span>
            <?= htmlspecialchars($successMessage) ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Formulario de Login -->
      <form method="POST" action="<?= url('login') ?>" class="space-y-4">
        <?= csrf_field() ?>

        <div>
          <label for="username" class="label">Usuario</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
              <i class="ti ti-user text-base" aria-hidden="true"></i>
            </div>
            <input
              type="text"
              id="username"
              name="username"
              class="w-full bg-gray-100/80 border border-gray-200 focus:bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-olive focus:border-olive rounded-xl pl-9 pr-3 py-2.5 text-sm transition-all"
              placeholder="Ej: admin"
              value="<?= old('username') ?>"
              autocomplete="username"
              autofocus
              required
            >
          </div>
        </div>

        <div>
          <label for="password" class="label">Contraseña</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
              <i class="ti ti-lock text-base" aria-hidden="true"></i>
            </div>
            <input
              type="password"
              id="password"
              name="password"
              class="w-full bg-gray-100/80 border border-gray-200 focus:bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-olive focus:border-olive rounded-xl pl-9 pr-10 py-2.5 text-sm transition-all"
              placeholder="••••••••"
              autocomplete="current-password"
              required
            >
            <button
              type="button"
              onclick="togglePasswordVisibility()"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
              title="Mostrar/Ocultar contraseña"
            >
              <i class="ti ti-eye text-base" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <button
          type="submit"
          class="bg-olive hover:bg-olive-hover text-white font-bold rounded-xl px-4 py-3 w-full shadow-md hover:shadow-lg active:scale-[0.99] transition-all cursor-pointer text-sm flex items-center justify-center gap-2"
        >
          <span>Ingresar al Sistema</span>
          <i class="ti ti-arrow-right text-base" aria-hidden="true"></i>
        </button>

      </form>

      <!-- Credenciales por defecto (Autorellenables al hacer clic) -->
      <div
        onclick="autoFillAdmin()"
        class="p-3 bg-gray-50 hover:bg-olive-light/60 border border-gray-200 hover:border-olive/40 rounded-xl text-xs space-y-1 transition-all cursor-pointer group"
        title="Haz clic para autorellenar datos predeterminados"
      >
        <div class="flex items-center justify-between font-semibold text-gray-700 group-hover:text-olive">
          <div class="flex items-center space-x-1.5">
            <i class="ti ti-info-circle text-olive shrink-0 text-base" aria-hidden="true"></i>
            <span>Acceso Predeterminado (Clic para autocompletar):</span>
          </div>
          <span class="text-[10px] uppercase font-bold text-olive opacity-0 group-hover:opacity-100 transition-opacity">Usar →</span>
        </div>
        <div class="pl-5 text-gray-600 font-mono text-[11px] space-y-0.5">
          <p>Usuario: <span class="font-bold text-gray-900">admin</span></p>
          <p>Contraseña: <span class="font-bold text-gray-900">admin123</span></p>
        </div>
      </div>

    </div>

    <!-- Footer -->
    <p class="text-center text-xs text-gray-400">
      Mi Bodega &copy; <?= date('Y') ?> · Todos los derechos reservados
    </p>

  </div>

  <?php include RUTA_APP . '/includes/spinner.php'; ?>
  <script src="<?= assets('scripts/ojito.js') ?>"></script>

</body>
</html>
