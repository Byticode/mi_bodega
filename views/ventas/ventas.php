<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Ventas</title>
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
        <h2 class="text-2xl font-bold text-gray-900">Ventas</h2>
        <p class="text-xs text-gray-500">Movimientos de mostrador de los últimos días</p>
      </div>
      <div class="flex items-center space-x-2">
        <input type="text" placeholder="Buscar mercancía..." class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm w-64 focus:outline-none focus:border-olive">
        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Filtrar</button>
      </div>
    </div>

    <!-- TARJETAS METRICAS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="bg-warmCard p-4 rounded-xl border border-gray-200">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">TOTAL EN VENTAS DE HOY</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">Bs 267,00</p>
      </div>
      <div class="bg-warmCard p-4 rounded-xl border border-gray-200">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">VENTAS DE HOY</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">2</p>
      </div>
    </div>

    <!-- TABLA DE VENTAS -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
      <table class="w-full text-left border-collapse text-sm">
        <thead>
          <tr class="border-b border-gray-200 text-xs text-gray-400 uppercase tracking-wider">
            <th class="py-3 px-4 font-semibold">FECHA</th>
            <th class="py-3 px-4 font-semibold">CLIENTE</th>
            <th class="py-3 px-4 font-semibold">PRODUCTOS</th>
            <th class="py-3 px-4 font-semibold">TOTAL</th>
            <th class="py-3 px-4 font-semibold">MÉTODO</th>
            <th class="py-3 px-4 font-semibold">ESTADO</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr>
            <td class="py-3 px-4 text-gray-500">03/08/2026</td>
            <td class="py-3 px-4 font-bold text-gray-900">María Bracho</td>
            <td class="py-3 px-4"><span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">2 productos</span></td>
            <td class="py-3 px-4 font-bold text-gray-900">Bs 171,00</td>
            <td class="py-3 px-4"><span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">Efectivo</span></td>
            <td class="py-3 px-4"><span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">Completada</span></td>
          </tr>
          <tr>
            <td class="py-3 px-4 text-gray-500">03/08/2026</td>
            <td class="py-3 px-4 font-bold text-gray-900">Luis A. Pérez</td>
            <td class="py-3 px-4"><span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">2 productos</span></td>
            <td class="py-3 px-4 font-bold text-gray-900">Bs 96,00</td>
            <td class="py-3 px-4"><span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">Tarjeta</span></td>
            <td class="py-3 px-4"><span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">Pendiente</span></td>
          </tr>
          <tr>
            <td class="py-3 px-4 text-gray-500">02/08/2026</td>
            <td class="py-3 px-4 font-bold text-gray-900">Consumidor final</td>
            <td class="py-3 px-4"><span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">2 productos</span></td>
            <td class="py-3 px-4 font-bold text-gray-900">Bs 270,00</td>
            <td class="py-3 px-4"><span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">Efectivo</span></td>
            <td class="py-3 px-4"><span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">Completada</span></td>
          </tr>
        </tbody>
      </table>

      <!-- Paginación Footer -->
      <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500">
        <span>6 ventas registradas esta semana</span>
        <div class="space-x-1">
          <button class="px-3 py-1 bg-white border border-gray-300 rounded hover:bg-gray-100">Anterior</button>
          <button class="px-3 py-1 bg-white border border-gray-300 rounded hover:bg-gray-100">Siguiente</button>
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