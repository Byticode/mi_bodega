<?php 
$page_title = 'Punto de Venta (POS)';
include RUTA_APP . '/includes/head.php'; 
?>

  <!-- SIDEBAR -->
  <?php 
  include RUTA_APP . '/includes/sidebar.php';
  ?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="ml-64 flex-1 p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Selección de Productos -->
    <section class="lg:col-span-2 space-y-4 overflow-y-auto max-h-[calc(100vh-80px)]">
      <div class="flex items-center justify-between sticky top-0 bg-warmBg z-10 py-2">
        <h2 class="text-2xl font-bold text-gray-900">Punto de Venta (POS)</h2>
        <div class="flex space-x-2">
          <input type="text" id="buscarProducto" placeholder="Buscar producto..." class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm w-64 focus:outline-none focus:border-olive">
          <button onclick="buscarProducto()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Buscar</button>
        </div>
      </div>

      <!-- Grid de productos -->
      <div id="productosGrid" class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <?php foreach ($productos as $producto): ?>
          <div class="producto-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-olive hover:shadow-md cursor-pointer transition-all" 
               onclick="agregarAlCarrito(<?= $producto['producto_id'] ?>, '<?= addslashes($producto['producto_nombre']) ?>', <?= $producto['producto_precio_venta'] ?>, '<?= $producto['unidad_abreviatura'] ?>', <?= $producto['producto_stock'] ?>)"
               data-nombre="<?= strtolower($producto['producto_nombre']) ?>">
            <h4 class="font-bold text-sm"><?= htmlspecialchars($producto['producto_nombre']) ?></h4>
            <p class="text-xs text-gray-500">Stock: <?= intval($producto['producto_stock']) ?> unidades</p>
            <p class="text-olive font-bold mt-2">Bs <?= number_format($producto['producto_precio_venta'], 2) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Ticket de Compra -->
    <section class="bg-white p-6 rounded-xl border border-gray-200 flex flex-col h-[calc(100vh-80px)] sticky top-4">
      <div class="flex flex-col h-full">
        <div>
          <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">Ticket Actual</h3>
          
          <!-- SELECT DE CLIENTE -->
          <div class="mb-4">
            <label for="cliente" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Cliente</label>
            <select id="cliente_id" class="w-full px-3 py-2 bg-warmBg border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-olive">
              <option value="">Consumidor final</option>
              <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['cliente_id'] ?>"><?= htmlspecialchars($cliente['cliente_nombre'] . ' ' . $cliente['cliente_apellido']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- SELECT DE ESTADO -->
          <div class="mb-4">
            <label for="estado" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Estado</label>
            <select id="estado_venta" class="w-full px-3 py-2 bg-warmBg border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-olive">
              <option value="completada" selected>Completada</option>
              <option value="pendiente">Pendiente</option>
            </select>
          </div>

          <!-- SELECT DE MÉTODO DE PAGO -->
          <div class="mb-4" id="metodoPagoContainer">
            <label for="metodo_pago" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Método de Pago</label>
            <select id="metodo_pago" class="w-full px-3 py-2 bg-warmBg border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-olive">
              <option value="efectivo">Efectivo</option>
              <option value="transferencia">Transferencia</option>
              <option value="pago_movil">Pago Móvil</option>
              <option value="biopago">Biopago</option>
              <option value="cashea">Cashea</option>
            </select>
          </div>

          <!-- NÚMERO DE PAGO -->
          <div class="mb-4 hidden" id="numeroPagoContainer">
            <label for="numero_pago" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Número de Referencia</label>
            <input type="text" id="numero_pago" placeholder="Ingrese número de referencia..." class="w-full px-3 py-2 bg-warmBg border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-olive">
          </div>
        </div>

        <!-- Carrito de compras -->
        <div class="flex-1 overflow-y-auto">
          <div id="carritoContainer" class="space-y-3 text-sm min-h-[200px]">
            <p class="text-gray-400 text-center py-8">No hay productos en el carrito</p>
          </div>
        </div>

        <!-- Totales y botón -->
        <div class="border-t border-gray-200 pt-4 mt-4">
          <div class="flex justify-between text-lg font-bold mb-2">
            <span>Total:</span>
            <span id="totalCarrito" class="text-olive">Bs 0.00</span>
          </div>
          <div class="flex justify-between text-sm text-gray-500 mb-4">
            <span>Productos:</span>
            <span id="totalProductos">0</span>
          </div>
          <button onclick="procesarVenta()" class="w-full bg-olive hover:bg-olive-hover text-white py-3 rounded-lg font-bold text-center transition-colors">
            Cobrar Ticket
          </button>
        </div>
      </div>
    </section>

  </main>

  <!-- MODAL DE TICKET -->
  <div id="modalTicket" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full max-h-[90vh] overflow-y-auto p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-gray-900">🧾 Ticket de Venta</h3>
        <button onclick="cerrarTicket()" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      
      <div id="contenidoTicket" class="space-y-3 text-sm">
        <!-- Contenido generado por JavaScript -->
      </div>

      <div class="pt-4 border-t border-gray-100 flex justify-center space-x-3">
        <button onclick="cerrarTicket()" class="px-4 py-2 bg-olive hover:bg-olive-hover text-white text-xs font-bold rounded-lg">Cerrar</button>
      </div>
    </div>
  </div>

  <script>
    window.POS_ACTION_URL = '<?= url("ventas/crear") ?>';
  </script>
  <script src="<?= assets('scripts/pos.js') ?>"></script>

  <?php include RUTA_APP . '/includes/spinner.php'; ?>
  <?php include RUTA_APP . '/includes/sidebar.js'; ?>
</body>
</html>