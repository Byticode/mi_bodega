<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Usuarios</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            warmBg: '#fcfbf7',
            warmCard: '#f5f3ec',
            olive: {
              DEFAULT: '#3a6341',
              hover: '#2f5135',
              light: '#eaf0eb'
            }
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

  <!-- CONTENIDO PRINCIPAL -->
  <main class="flex-1 p-6 space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Usuarios</h2>
        <p class="text-xs text-gray-500">Administra los usuarios del sistema</p>
      </div>
    </div>

    <!-- REGISTRO -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-4">
      <h3 class="font-bold text-gray-900 text-sm">Nuevo Usuario</h3>

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

      <form class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3" action="<?= url('usuarios/crear') ?>" method="POST">
        <input type="text" name="nombre" placeholder="Nombre completo" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive" required>
        <input type="text" name="username" placeholder="Nombre de usuario" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive" required>
        <input type="password" name="clave" placeholder="Contraseña" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive" required>
        <select name="rol" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive bg-white" required>
          <option value="vendedor">Vendedor</option>
          <option value="admin">Administrador</option>
        </select>
        <button type="submit" class="bg-olive hover:bg-olive-hover text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors col-span-full sm:col-span-1">
          Registrar Usuario
        </button>
      </form>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="flex items-center gap-3 p-4 mb-6 text-sm text-green-800 border border-green-200/80 rounded-2xl bg-green-50/80 shadow-sm" role="alert">
        <i data-lucide="alert-circle" class="w-5 h-5 text-green-600 shrink-0"></i>
        <div class="flex-1">
          <span class="font-semibold">Correcto.</span> <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-green-800 p-1 rounded-lg hover:bg-green-100/60 transition-colors">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- TABLA -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <table class="w-full text-left text-xs">
        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase">
          <tr>
            <th class="p-3">Nombre</th>
            <th class="p-3">Usuario</th>
            <th class="p-3">Rol</th>
            <th class="p-3">Fecha Registro</th>
            <th class="p-3 text-right">Acción</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($usuarios as $usuario): ?>
            <tr class="hover:bg-gray-50">
              <td class="p-3 font-medium text-gray-900"><?= htmlspecialchars($usuario['usuario_nombre']) ?></td>
              <td class="p-3 font-mono"><?= htmlspecialchars($usuario['usuario_username']) ?></td>
              <td class="p-3">
                <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $usuario['usuario_rol'] == 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' ?>">
                  <?= $usuario['usuario_rol'] == 'admin' ? 'Administrador' : 'Vendedor' ?>
                </span>
              </td>
              <td class="p-3 text-gray-500"><?= date('d/m/Y H:i', strtotime($usuario['created_at'])) ?></td>
              <td class="p-3 text-right">
                <a href="<?= url('usuarios/editar/' . $usuario['usuario_id']) ?>" class="inline-block p-1.5 text-gray-500 hover:text-olive hover:bg-gray-100 rounded-md transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <?php
  include RUTA_APP . '/includes/sidebar.js';
  ?>
</body>

</html>