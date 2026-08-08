<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Crear Surtido</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Geist', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            display: ['Fraunces', 'Georgia', 'serif'],
            mono: ['Geist Mono', 'ui-monospace', 'monospace']
          },
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
  include ruta . '/includes/sidebar.php';
  ?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="flex-1 p-6 space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center space-x-3">
      <a href="index.php?controller=surtidosController&action=listar" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-100">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </a>
      <div>
        <h2 class="font-display text-2xl font-semibold text-gray-900">Nuevo Surtido</h2>
        <p class="text-xs text-gray-500">Registra la entrada de productos al inventario</p>
      </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="flex items-center gap-3 p-4 text-sm text-rose-800 border border-rose-200/80 rounded-2xl bg-rose-50/80 shadow-sm" role="alert">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
        <div class="flex-1">
          <span class="font-semibold">Ha ocurrido un error.</span> <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-rose-800 p-1 rounded-lg hover:bg-rose-100/60 transition-colors">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form id="surtidoForm" action="index.php?controller=surtidosController&action=crear" method="POST" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-6">
      
      <!-- Datos del surtido -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Proveedor *</label>
          <select name="proveedor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive bg-white" required>
            <option value="">Seleccionar proveedor...</option>
            <?php foreach ($proveedores as $proveedor): ?>
              <option value="<?= $proveedor['proveedor_id'] ?>"><?= htmlspecialchars($proveedor['proveedor_nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Productos -->
      <div>
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-bold text-gray-900 text-sm">Productos del Surtido</h3>
          <button type="button" onclick="agregarProducto()" class="text-olive hover:text-olive-hover text-xs font-semibold flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Agregar Producto
          </button>
        </div>

        <div id="productosContainer" class="space-y-3">
          <!-- Fila de producto template -->
          <div class="producto-row grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Producto</label>
              <select name="producto_id[]" class="producto-select w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive bg-white" required>
                <option value="">Seleccionar producto...</option>
                <?php foreach ($productos as $producto): ?>
                  <option value="<?= $producto['producto_id'] ?>" data-stock="<?= $producto['producto_stock'] ?>" data-precio="<?= $producto['producto_precio_venta'] ?>">
                    <?= htmlspecialchars($producto['producto_nombre']) ?> (Stock: <?= intval($producto['producto_stock']) ?> <?= $producto['unidad_abreviatura'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Cantidad *</label>
              <input type="number" step="1" min="1" name="cantidad[]" placeholder="0" class="producto-cantidad w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive" required>
              <p class="text-xs text-gray-400 mt-1">Solo números enteros</p>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Precio Costo C/U *</label>
              <input type="number" step="0.01" min="0.01" name="precio_costo[]" placeholder="0.00" class="producto-precio w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive" required>
            </div>
            <div class="flex items-end gap-2">
              <button type="button" onclick="eliminarProducto(this)" class="text-rose-600 hover:text-rose-800 p-2 rounded-lg hover:bg-rose-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Resumen -->
      <div class="border-t border-gray-200 pt-4">
        <div class="flex justify-end">
          <div class="text-right">
            <p class="text-xs text-gray-500">Costo Total del Surtido</p>
            <p id="totalCosto" class="text-2xl font-bold text-olive">Bs. 0.00</p>
          </div>
        </div>
      </div>

      <!-- Botones -->
      <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
        <a href="index.php?controller=surtidosController&action=listar" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancelar</a>
        <button type="submit" class="px-5 py-2 bg-olive hover:bg-olive-hover text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Registrar Surtido
        </button>
      </div>
    </form>
  </main>

  <script>
    // Contador para IDs únicos
    let rowCount = 0;

    // Agregar producto
    function agregarProducto() {
      const container = document.getElementById('productosContainer');
      const firstRow = container.querySelector('.producto-row');
      const newRow = firstRow.cloneNode(true);
      
      // Limpiar valores
      newRow.querySelector('.producto-select').value = '';
      newRow.querySelector('.producto-cantidad').value = '';
      newRow.querySelector('.producto-precio').value = '';
      
      // Agregar listeners
      const select = newRow.querySelector('.producto-select');
      const cantidad = newRow.querySelector('.producto-cantidad');
      const precio = newRow.querySelector('.producto-precio');
      
      select.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option && option.dataset.precio) {
          precio.placeholder = 'Precio: ' + option.dataset.precio;
        }
        calcularTotal();
      });
      
      cantidad.addEventListener('input', calcularTotal);
      precio.addEventListener('input', calcularTotal);
      
      // Mostrar el botón de eliminar si hay más de una fila
      const rows = container.querySelectorAll('.producto-row');
      if (rows.length >= 1) {
        const deleteBtns = container.querySelectorAll('.producto-row:last-child .text-rose-600');
        deleteBtns.forEach(btn => btn.style.display = 'block');
      }
      
      container.appendChild(newRow);
      rowCount++;
      calcularTotal();
    }

    // Eliminar producto
    function eliminarProducto(btn) {
      const container = document.getElementById('productosContainer');
      const rows = container.querySelectorAll('.producto-row');
      
      if (rows.length <= 1) {
        alert('Debe haber al menos un producto');
        return;
      }
      
      const row = btn.closest('.producto-row');
      row.remove();
      calcularTotal();
    }

    // Calcular total
    function calcularTotal() {
      const rows = document.querySelectorAll('.producto-row');
      let total = 0;
      
      rows.forEach(row => {
        const cantidad = parseInt(row.querySelector('.producto-cantidad').value) || 0;
        const precio = parseFloat(row.querySelector('.producto-precio').value) || 0;
        total += cantidad * precio;
      });
      
      document.getElementById('totalCosto').textContent = 'Bs. ' + total.toFixed(2);
    }

    // Event listeners para calcular total
    document.addEventListener('DOMContentLoaded', function() {
      // Agregar listeners a la primera fila
      const firstRow = document.querySelector('.producto-row');
      if (firstRow) {
        const select = firstRow.querySelector('.producto-select');
        const cantidad = firstRow.querySelector('.producto-cantidad');
        const precio = firstRow.querySelector('.producto-precio');
        
        select.addEventListener('change', function() {
          const option = this.options[this.selectedIndex];
          if (option && option.dataset.precio) {
            precio.placeholder = 'Precio: ' + option.dataset.precio;
          }
          calcularTotal();
        });
        
        cantidad.addEventListener('input', calcularTotal);
        precio.addEventListener('input', calcularTotal);
      }
      
      // Ocultar botón de eliminar en la primera fila si solo hay una
      const firstDeleteBtn = document.querySelector('.producto-row .text-rose-600');
      if (firstDeleteBtn) {
        firstDeleteBtn.style.display = 'none';
      }
    });

    // Validar antes de enviar
    document.getElementById('surtidoForm').addEventListener('submit', function(e) {
      const rows = document.querySelectorAll('.producto-row');
      let valid = true;
      
      rows.forEach(row => {
        const producto = row.querySelector('.producto-select').value;
        const cantidad = row.querySelector('.producto-cantidad').value;
        const precio = row.querySelector('.producto-precio').value;
        
        if (!producto || !cantidad || !precio) {
          valid = false;
        }
      });
      
      if (!valid) {
        e.preventDefault();
        alert('Complete todos los campos de los productos');
      }
    });
  </script>

  <?php
  include ruta . '/includes/sidebar.js';
  ?>
</body>
</html>