<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - POS</title>
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

  <!-- CONTENIDO PRINCIPAL (con margen izquierdo para dar espacio al sidebar) -->
  <main class="ml-64 flex-1 p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Selección de Productos / Búsqueda -->
    <section class="lg:col-span-2 space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Punto de Venta (POS)</h2>
        <div class="flex space-x-2">
          <input type="text" placeholder="Buscar mercancía..." class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm w-64 focus:outline-none focus:border-olive">
          <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Filtrar</button>
        </div>
      </div>

      <!-- Grid de productos rápidos -->
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-olive cursor-pointer">
          <h4 class="font-bold text-sm">Harina de maíz 1 kg</h4>
          <p class="text-xs text-gray-500">Stock: 42</p>
          <p class="text-olive font-bold mt-2">Bs 28,50</p>
        </div>
        <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-olive cursor-pointer">
          <h4 class="font-bold text-sm">Arroz blanco 1 kg</h4>
          <p class="text-xs text-gray-500">Stock: 18</p>
          <p class="text-olive font-bold mt-2">Bs 32,00</p>
        </div>
        <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-olive cursor-pointer">
          <h4 class="font-bold text-sm">Atún en aceite 140 g</h4>
          <p class="text-xs text-gray-500">Stock: 25</p>
          <p class="text-olive font-bold mt-2">Bs 22,50</p>
        </div>
        <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-olive cursor-pointer">
          <h4 class="font-bold text-sm">Aceite de maíz 1 L</h4>
          <p class="text-xs text-gray-500">Stock: 10</p>
          <p class="text-olive font-bold mt-2">Bs 76,00</p>
        </div>
        <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-olive cursor-pointer">
          <h4 class="font-bold text-sm">Azúcar refinada 1 kg</h4>
          <p class="text-xs text-gray-500">Stock: 50</p>
          <p class="text-olive font-bold mt-2">Bs 30,00</p>
        </div>
        <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-olive cursor-pointer">
          <h4 class="font-bold text-sm">Café molido 500 g</h4>
          <p class="text-xs text-gray-500">Stock: 8</p>
          <p class="text-olive font-bold mt-2">Bs 95,00</p>
        </div>
      </div>
    </section>

    <!-- Ticket de Compra -->
    <section class="bg-white p-6 rounded-xl border border-gray-200 flex flex-col justify-between">
      <div>
        <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">Ticket Actual</h3>
        
        <!-- SELECT DE CLIENTE -->
        <div class="mb-5">
          <label for="cliente" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Cliente</label>
          <select id="cliente" class="w-full px-3 py-2 bg-warmBg border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-olive">
            <option value="consumidor_final">Consumidor final</option>
            <option value="maria_bracho">María Bracho</option>
            <option value="luis_perez">Luis A. Pérez</option>
            <option value="yelitza_morales">Yelitza Morales</option>
            <option value="bodegon_el_trebol">Bodegón El Trébol</option>
          </select>
        </div>

        <div class="space-y-4 text-sm">
          <div class="flex justify-between items-center border-b border-gray-100 pb-3">
            <div>
              <p class="font-semibold text-gray-900">Harina de maíz 1 kg</p>
              <span class="text-xs text-gray-500">P. Unit: Bs 28,50</span>
            </div>
            <div class="flex items-center space-x-3">
              <div class="flex items-center border border-gray-200 rounded">
                <button class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600">-</button>
                <span class="px-2 text-xs font-bold">2</span>
                <button class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600">+</button>
              </div>
              <span class="font-bold min-w-[60px] text-right">Bs 57,00</span>
            </div>
          </div>

          <div class="flex justify-between items-center border-b border-gray-100 pb-3">
            <div>
              <p class="font-semibold text-gray-900">Arroz blanco 1 kg</p>
              <span class="text-xs text-gray-500">P. Unit: Bs 32,00</span>
            </div>
            <div class="flex items-center space-x-3">
              <div class="flex items-center border border-gray-200 rounded">
                <button class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600">-</button>
                <span class="px-2 text-xs font-bold">1</span>
                <button class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600">+</button>
              </div>
              <span class="font-bold min-w-[60px] text-right">Bs 32,00</span>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t border-gray-200 pt-4 mt-6">
        <div class="flex justify-between text-lg font-bold mb-4">
          <span>Total:</span>
          <span class="text-olive">Bs 89,00</span>
        </div>
        <button class="w-full bg-olive hover:bg-olive-hover text-white py-3 rounded-lg font-bold text-center">
          Cobrar Ticket
        </button>
      </div>
    </section>

  </main>

   <!-- SIDEBAR -->
<?php 

include '../../includes/sidebar.js';
?>
</body>
</html>