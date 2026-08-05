<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Surtido</title>
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
        <h2 class="text-2xl font-bold text-gray-900">Surtido</h2>
        <p class="text-xs text-gray-500">Entradas de mercancía y reabastecimiento de inventario</p>
      </div>
      <div class="flex items-center space-x-2">
        <input type="text" placeholder="Buscar mercancía..." class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm w-64 focus:outline-none focus:border-olive">
        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Filtrar</button>
        <button class="px-4 py-2 bg-olive hover:bg-olive-hover text-white text-sm font-bold rounded-lg">+ Registrar Surtido</button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="bg-warmCard p-4 rounded-xl border border-gray-200">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">ÚLTIMO REABASTECIMIENTO</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">Hoy, 10:30 AM</p>
      </div>
      <div class="bg-warmCard p-4 rounded-xl border border-gray-200">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">ITEMS RECIBIDOS (MES)</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">450 Unidades</p>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
      <table class="w-full text-left border-collapse text-sm">
        <thead>
          <tr class="border-b border-gray-200 text-xs text-gray-400 uppercase tracking-wider">
            <th class="py-3 px-4 font-semibold">FECHA</th>
            <th class="py-3 px-4 font-semibold">PROVEEDOR</th>
            <th class="py-3 px-4 font-semibold">PRODUCTOS</th>
            <th class="py-3 px-4 font-semibold">UNID. ENTRANTE</th>
            <th class="py-3 px-4 font-semibold">TOTAL COMPRA</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr>
            <td class="py-3 px-4 text-gray-500">03/08/2026</td>
            <td class="py-3 px-4 font-bold text-gray-900">Distribuidora Polaris</td>
            <td class="py-3 px-4"><span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">2 productos</span></td>
            <td class="py-3 px-4 font-bold text-emerald-700">+50</td>
            <td class="py-3 px-4 font-bold text-gray-900">Bs 1.050,00</td>
          </tr>
          <tr>
            <td class="py-3 px-4 text-gray-500">01/08/2026</td>
            <td class="py-3 px-4 font-bold text-gray-900">Alimentos del Centro</td>
            <td class="py-3 px-4"><span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">4 productos</span></td>
            <td class="py-3 px-4 font-bold text-emerald-700">+24</td>
            <td class="py-3 px-4 font-bold text-gray-900">Bs 1.440,00</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

   <!-- SIDEBAR -->
<?php 

include '../../includes/sidebar.php';
?>
</body>
</html>