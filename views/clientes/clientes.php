<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Clientes</title>
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

  <main class="flex-1 p-6 space-y-6 max-w-4xl mx-auto">
    <div>
      <h2 class="text-2xl font-bold text-gray-900">Clientes</h2>
      <p class="text-xs text-gray-500">Gestión de datos de clientes frecuentes</p>
    </div>

    <!-- REGISTRO -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-4">
      <h3 class="font-bold text-gray-900 text-sm">Nuevo Cliente</h3>
      <form class="grid grid-cols-1 sm:grid-cols-3 gap-3" onsubmit="event.preventDefault();">
        <input type="text" placeholder="Nombre completo" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive">
        <input type="text" placeholder="Cédula / ID" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive">
        <button type="submit" class="bg-olive hover:bg-olive-hover text-white text-xs font-bold py-2 rounded-lg">
          Registrar Cliente
        </button>
      </form>
    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <table class="w-full text-left text-xs">
        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase">
          <tr>
            <th class="p-3">Nombre</th>
            <th class="p-3">Cédula / ID</th>
            <th class="p-3 text-right">Acción</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr class="hover:bg-gray-50">
            <td class="p-3 font-medium text-gray-900">María Pérez</td>
            <td class="p-3 font-mono">V-12345678</td>
            <td class="p-3 text-right">
              <a href="clientes-editar.html?id=1" class="inline-block p-1.5 text-gray-500 hover:text-olive hover:bg-gray-100 rounded-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>
<?php 

include './includes/sidebar.js';

?>
</body>
</html>