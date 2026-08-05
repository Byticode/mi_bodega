<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Credenciales de Acceso</title>
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

include '../../includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="ml-64 flex-1 p-6 space-y-6 max-w-4xl">
    <div>
      <h2 class="text-2xl font-bold text-gray-900">Credenciales de Acceso</h2>
      <p class="text-xs text-gray-500">Administra la clave de seguridad principal para ingresar al sistema de la bodega</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="md:col-span-2 bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-5">
        <div class="flex items-center space-x-3 border-b border-gray-100 pb-4">
          <div class="p-2.5 bg-olive-light text-olive rounded-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
          </div>
          <div>
            <h3 class="font-bold text-gray-900 text-base">Contraseña Master</h3>
            <p class="text-xs text-gray-500">Clave única requerida para iniciar sesión en la caja o administración</p>
          </div>
        </div>

        <form class="space-y-4" onsubmit="event.preventDefault();">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Contraseña Actual</label>
            <input type="password" placeholder="••••••••" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Nueva Contraseña</label>
              <input type="password" placeholder="Mínimo 6 caracteres" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
              <input type="password" placeholder="Repite la clave" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
            </div>
          </div>

          <div class="pt-2 flex justify-end">
            <button type="submit" class="px-5 py-2.5 bg-olive hover:bg-olive-hover text-white text-sm font-bold rounded-lg transition-colors">
              Actualizar Contraseña
            </button>
          </div>
        </form>
      </div>

      <div class="bg-warmCard p-5 rounded-xl border border-gray-200/60 shadow-sm space-y-4 h-fit">
        <h4 class="font-bold text-gray-900 text-sm flex items-center space-x-2">
          <svg class="w-4 h-4 text-olive" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <span>Acceso Directo</span>
        </h4>
        
        <p class="text-xs text-gray-600 leading-relaxed">
          El sistema está optimizado para inicio de sesión único. No requiere usuario ni correo electrónico para ingresar.
        </p>

        <div class="border-t border-gray-300/50 pt-3">
          <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block mb-1">Último cambio</span>
          <p class="text-xs font-medium text-gray-800">01 de Agosto de 2026</p>
        </div>
      </div>
    </div>
  </main>

  <!-- SIDEBAR -->
<?php 

include '../../includes/sidebar.js';
?>
</body>
</html>