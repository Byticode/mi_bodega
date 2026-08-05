<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Productos Base</title>
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
</head>
<body class="bg-warmBg text-gray-800 font-sans min-h-screen flex">
  <!-- SIDEBAR -->
<?php 

include '../../includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="ml-64 flex-1 p-6 space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Productos Base</h2>
        <p class="text-xs text-gray-500">Catálogo general de artículos registrados</p>
      </div>
    </div>

    <!-- REGISTRO -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-4">
      <h3 class="font-bold text-gray-900 text-sm">Nuevo Producto Base</h3>
      <form class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3" onsubmit="event.preventDefault();">
        <input type="text" placeholder="Código de Barras" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive">
        <input type="text" placeholder="Nombre Producto" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive">
        <select class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive bg-white">
          <option value="">Categoría...</option>
          <option>Abarrotes</option>
        </select>
        <button type="submit" class="bg-olive hover:bg-olive-hover text-white text-xs font-bold rounded-lg py-2 transition-colors">
          Guardar Producto
        </button>
      </form>
    </div>

    <!-- TABLA DE LISTADO -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <table class="w-full text-left text-xs">
        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase">
          <tr>
            <th class="p-3">Código</th>
            <th class="p-3">Nombre</th>
            <th class="p-3">Categoría</th>
            <th class="p-3 text-right">Acción</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr class="hover:bg-gray-50">
            <td class="p-3 font-mono">7501000123456</td>
            <td class="p-3 font-medium text-gray-900">Harina de Trigo 1kg</td>
            <td class="p-3">Abarrotes</td>
            <td class="p-3 text-right">
              <a href="productos-base-editar.html?id=1" class="inline-block p-1.5 text-gray-500 hover:text-olive hover:bg-gray-100 rounded-md transition-colors" title="Editar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

   <!-- SIDEBAR -->
<?php 

include '../../includes/sidebar.js';
?>
</body>
</html>