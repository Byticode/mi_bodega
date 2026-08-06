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
  include ruta . '/includes/sidebar.php';
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
    let carrito = [];
    let totalCarrito = 0;

    // Buscar productos
    function buscarProducto() {
      const busqueda = document.getElementById('buscarProducto').value.toLowerCase();
      const cards = document.querySelectorAll('.producto-card');
      
      cards.forEach(card => {
        const nombre = card.dataset.nombre || '';
        if (nombre.includes(busqueda)) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }

    // Agregar al carrito
    function agregarAlCarrito(id, nombre, precio, unidad, stock) {
      // Verificar si ya existe en el carrito
      const index = carrito.findIndex(item => item.id === id);
      
      if (index !== -1) {
        // Si existe, aumentar cantidad
        if (carrito[index].cantidad < stock) {
          carrito[index].cantidad++;
          actualizarCarrito();
        } else {
          alert('No hay suficiente stock disponible');
        }
      } else {
        // Si no existe, agregar nuevo
        if (stock > 0) {
          carrito.push({
            id: id,
            nombre: nombre,
            precio: precio,
            unidad: unidad,
            cantidad: 1,
            stock: stock
          });
          actualizarCarrito();
        } else {
          alert('Producto sin stock');
        }
      }
    }

    // Actualizar carrito
    function actualizarCarrito() {
      const container = document.getElementById('carritoContainer');
      let html = '';
      totalCarrito = 0;
      let totalProductos = 0;

      if (carrito.length === 0) {
        html = '<p class="text-gray-400 text-center py-8">No hay productos en el carrito</p>';
      } else {
        carrito.forEach((item, index) => {
          const subtotal = item.cantidad * item.precio;
          totalCarrito += subtotal;
          totalProductos += item.cantidad;

          html += `
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
              <div class="flex-1">
                <p class="font-semibold text-gray-900 text-xs">${item.nombre}</p>
                <span class="text-xs text-gray-500">P. Unit: Bs ${item.precio.toFixed(2)}</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="flex items-center border border-gray-200 rounded">
                  <button onclick="cambiarCantidad(${index}, -1)" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600">-</button>
                  <span class="px-2 text-xs font-bold min-w-[30px] text-center">${item.cantidad}</span>
                  <button onclick="cambiarCantidad(${index}, 1)" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600">+</button>
                </div>
                <span class="font-bold min-w-[70px] text-right text-xs">Bs ${subtotal.toFixed(2)}</span>
                <button onclick="eliminarDelCarrito(${index})" class="text-rose-600 hover:text-rose-800">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>
          `;
        });
      }

      container.innerHTML = html;
      document.getElementById('totalCarrito').textContent = 'Bs ' + totalCarrito.toFixed(2);
      document.getElementById('totalProductos').textContent = totalProductos;
    }

    // Cambiar cantidad
    function cambiarCantidad(index, cambio) {
      const nuevaCantidad = carrito[index].cantidad + cambio;
      
      if (nuevaCantidad <= 0) {
        eliminarDelCarrito(index);
        return;
      }
      
      if (nuevaCantidad <= carrito[index].stock) {
        carrito[index].cantidad = nuevaCantidad;
        actualizarCarrito();
      } else {
        alert('No hay suficiente stock disponible');
      }
    }

    // Eliminar del carrito
    function eliminarDelCarrito(index) {
      carrito.splice(index, 1);
      actualizarCarrito();
    }

    // Procesar venta
    function procesarVenta() {
      if (carrito.length === 0) {
        alert('El carrito está vacío');
        return;
      }

      const cliente_id = document.getElementById('cliente_id').value;
      const estado = document.getElementById('estado_venta').value;
      const metodo_pago = document.getElementById('metodo_pago').value;
      const numero_pago = document.getElementById('numero_pago').value;

      // Validar que si está completada, tenga método de pago
      if (estado === 'completada' && !metodo_pago) {
        alert('Debe seleccionar un método de pago');
        return;
      }

      // Preparar datos
      const productos = carrito.map(item => ({
        id: item.id,
        cantidad: item.cantidad,
        precio: item.precio
      }));

      // Crear formulario para enviar
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'index.php?controller=ventasController&action=crear';

      const campos = {
        'cliente_id': cliente_id,
        'estado': estado,
        'metodo_pago': metodo_pago,
        'numero_pago': numero_pago,
        'productos': JSON.stringify(productos)
      };

      for (const [key, value] of Object.entries(campos)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
      }

      document.body.appendChild(form);
      form.submit();
    }

    // Mostrar ticket
    function mostrarTicket(datos) {
      const container = document.getElementById('contenidoTicket');
      let html = `
        <div class="text-center border-b pb-3">
          <p class="font-bold text-lg">🧾 Ticket de Venta</p>
          <p class="text-xs text-gray-500">#${datos.venta_id}</p>
          <p class="text-xs text-gray-500">${new Date().toLocaleString()}</p>
        </div>
        <div class="space-y-1 py-3">
      `;

      datos.productos.forEach(item => {
        html += `
          <div class="flex justify-between text-xs">
            <span>${item.nombre} x ${item.cantidad}</span>
            <span>Bs ${(item.cantidad * item.precio).toFixed(2)}</span>
          </div>
        `;
      });

      html += `
        </div>
        <div class="border-t pt-3">
          <div class="flex justify-between font-bold text-sm">
            <span>TOTAL</span>
            <span class="text-olive">Bs ${datos.total.toFixed(2)}</span>
          </div>
          <div class="flex justify-between text-xs text-gray-500 mt-1">
            <span>Cliente:</span>
            <span>${datos.cliente || 'Consumidor final'}</span>
          </div>
          <div class="flex justify-between text-xs text-gray-500">
            <span>Estado:</span>
            <span class="${datos.estado === 'completada' ? 'text-green-600' : 'text-amber-600'}">${datos.estado}</span>
          </div>
          ${datos.metodo_pago ? `
            <div class="flex justify-between text-xs text-gray-500">
              <span>Método:</span>
              <span>${datos.metodo_pago}</span>
            </div>
          ` : ''}
          ${datos.numero_pago ? `
            <div class="flex justify-between text-xs text-gray-500">
              <span>Referencia:</span>
              <span>${datos.numero_pago}</span>
            </div>
          ` : ''}
        </div>
      `;

      container.innerHTML = html;
      document.getElementById('modalTicket').classList.remove('hidden');
    }

    function cerrarTicket() {
      document.getElementById('modalTicket').classList.add('hidden');
    }

    // Mostrar/ocultar campos según estado
    document.getElementById('estado_venta').addEventListener('change', function() {
      const metodoContainer = document.getElementById('metodoPagoContainer');
      const numeroContainer = document.getElementById('numeroPagoContainer');
      
      if (this.value === 'completada') {
        metodoContainer.style.display = 'block';
        numeroContainer.style.display = 'block';
      } else {
        metodoContainer.style.display = 'none';
        numeroContainer.style.display = 'none';
      }
    });

    // Inicializar estado
    document.getElementById('estado_venta').dispatchEvent(new Event('change'));

    // Buscar al escribir
    document.getElementById('buscarProducto').addEventListener('keyup', buscarProducto);

    // Limpiar carrito si se recarga la página
    window.addEventListener('beforeunload', function() {
      carrito = [];
    });
  </script>

  <?php 
  include ruta . '/includes/sidebar.js';
  ?>
</body>
</html>