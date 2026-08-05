<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Editar Cliente</title>
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

include '../../includes/sidebar.php';
?>


  <main class="flex-1 p-6 space-y-6 max-w-2xl mx-auto">
    <div class="flex items-center space-x-3">
      <a href="clientes.html" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-100">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
      </a>
      <h2 class="text-2xl font-bold text-gray-900">Editar Cliente</h2>
    </div>

    <form class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4" onsubmit="event.preventDefault();">
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Nombre Completo</label>
        <input type="text" value="María Pérez" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Cédula / Identificación</label>
        <input type="text" value="V-12345678" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
      </div>

      <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
        <a href="clientes.html" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600">Cancelar</a>
        <button type="submit" class="px-5 py-2 bg-olive text-white text-xs font-bold rounded-lg">Guardar Cambios</button>
      </div>
    </form>
  </main>
  <!-- SIDEBAR -->
<?php 

include '../../includes/sidebar.js';
?>
</body>
</html>