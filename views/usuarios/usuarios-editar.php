<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Editar Usuario</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            warmBg: '#fcfbf7',
            olive: { DEFAULT: '#3a6341', hover: '#2f5135', light: '#eaf0eb' }
          }
        }
      }
    }
  </script>
</head>
<body class="bg-warmBg text-gray-800 font-sans min-h-screen flex">

  <!-- SIDEBAR -->
  <?php 
  include RUTA_APP . '/includes/sidebar.php';
  ?>

  <main class="flex-1 p-6 space-y-6 max-w-2xl mx-auto">
    <div class="flex items-center space-x-3">
      <a href="<?= url('usuarios') ?>" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-100">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </a>
      <h2 class="text-2xl font-bold text-gray-900">Editar Usuario</h2>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="flex items-center gap-3 p-4 mb-6 text-sm text-rose-800 border border-rose-200/80 rounded-2xl bg-rose-50/80 shadow-sm" role="alert">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
        <div class="flex-1">
          <span class="font-semibold">Ha ocurrido un error.</span> <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-rose-800 p-1 rounded-lg hover:bg-rose-100/60 transition-colors">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="<?= url('usuarios/editar/' . $dato[0]['usuario_id']) ?>" method="POST" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Nombre Completo</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($dato[0]['usuario_nombre']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive" required>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Nombre de Usuario</label>
        <input type="text" name="username" value="<?= htmlspecialchars($dato[0]['usuario_username']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive" required>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Nueva Contraseña</label>
        <input type="password" name="clave" placeholder="Dejar en blanco para no cambiar" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
        <p class="text-xs text-gray-400 mt-1">Solo ingrese una contraseña si desea cambiarla</p>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Rol</label>
        <select name="rol" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive bg-white" required>
          <option value="vendedor" <?= $dato[0]['usuario_rol'] == 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
          <option value="admin" <?= $dato[0]['usuario_rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
      </div>

      <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
        <a href="<?= url('usuarios') ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600">Cancelar</a>
        <button type="submit" class="px-5 py-2 bg-olive text-white text-xs font-bold rounded-lg">Guardar</button>
      </div>
    </form>
  </main>

  <?php 
  include RUTA_APP . '/includes/sidebar.js';
  ?>
</body>
</html>