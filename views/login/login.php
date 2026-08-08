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
          <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
          <div class="flex-1 leading-relaxed">
            <span class="font-bold block text-rose-900 mb-0.5">Acceso Denegado</span>
            <?= htmlspecialchars($errorMessage) ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if (!empty($successMessage)): ?>
        <div class="flex items-start gap-3 p-3.5 text-xs text-emerald-800 border border-emerald-200/80 rounded-xl bg-emerald-50/90 shadow-sm" role="status">
          <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
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
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
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
              <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
          </div>
        </div>

        <button
          type="submit"
          class="bg-olive hover:bg-olive-hover text-white font-bold rounded-xl px-4 py-3 w-full shadow-md hover:shadow-lg active:scale-[0.99] transition-all cursor-pointer text-sm flex items-center justify-center gap-2"
        >
          <span>Ingresar al Sistema</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
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
            <svg class="w-4 h-4 text-olive shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
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
