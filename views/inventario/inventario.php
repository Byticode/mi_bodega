<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Inventario</title>
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
  <main class="ml-64 flex-1 p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Inventario</h2>
        <p class="text-xs text-gray-500">Catálogo general de productos y stock disponible</p>
      </div>
      <div class="flex items-center space-x-2">
        <input type="text" placeholder="Buscar mercancía..." class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm w-64 focus:outline-none focus:border-olive">
        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Filtrar</button>
      </div>
    </div>

    <!-- TARJETAS DE RESUMEN -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-warmCard p-4 rounded-xl border border-gray-200">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">TOTAL PRODUCTOS</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">124</p>
      </div>
      <div class="bg-warmCard p-4 rounded-xl border border-gray-200">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">PRODUCTOS BAJO STOCK</span>
        <p class="text-2xl font-bold text-amber-600 mt-1">3</p>
      </div>
      <div class="bg-warmCard p-4 rounded-xl border border-gray-200">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">VALOR DEL INVENTARIO</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">Bs 14.250,00</p>
      </div>
    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
      <table class="w-full text-left border-collapse text-sm">
        <thead>
          <tr class="border-b border-gray-200 text-xs text-gray-400 uppercase tracking-wider">
            <th class="py-3 px-4 font-semibold">PRODUCTO</th>
            <th class="py-3 px-4 font-semibold">CATEGORÍA</th>
            <th class="py-3 px-4 font-semibold">STOCK</th>
            <th class="py-3 px-4 font-semibold">P. UNITARIO</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr>
            <td class="py-3 px-4 font-bold text-gray-900">Harina de maíz 1 kg</td>
            <td class="py-3 px-4 text-gray-600">Víveres</td>
            <td class="py-3 px-4 font-semibold">42</td>
            <td class="py-3 px-4 font-medium">Bs 28,50</td>
          </tr>
          <tr>
            <td class="py-3 px-4 font-bold text-gray-900">Arroz blanco 1 kg</td>
            <td class="py-3 px-4 text-gray-600">Víveres</td>
            <td class="py-3 px-4 font-semibold">18</td>
            <td class="py-3 px-4 font-medium">Bs 32,00</td>
          </tr>
          <tr>
            <td class="py-3 px-4 font-bold text-gray-900">Café molido 500 g</td>
            <td class="py-3 px-4 text-gray-600">Víveres</td>
            <td class="py-3 px-4 text-amber-600 font-bold">4</td>
            <td class="py-3 px-4 font-medium">Bs 95,00</td>
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