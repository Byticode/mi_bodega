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
  <main class="flex-1 p-8 space-y-8 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Inventario</h2>
        <p class="text-xs text-gray-500">Gestión de productos y stock</p>
      </div>
      <a href="index.php?controller=productosController&action=crear" class="bg-olive hover:bg-olive-hover text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Nuevo Producto
      </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="flex items-center gap-3 p-4 mb-6 text-sm text-rose-800 border border-rose-200/80 rounded-2xl bg-rose-50/80 shadow-sm" role="alert">
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

    <?php if (isset($_SESSION['success'])): ?>
      <div class="flex items-center gap-3 p-4 mb-6 text-sm text-green-800 border border-green-200/80 rounded-2xl bg-green-50/80 shadow-sm" role="alert">
        <i data-lucide="alert-circle" class="w-5 h-5 text-green-600 shrink-0"></i>
        <div class="flex-1">
          <span class="font-semibold">Correcto.</span> <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-green-800 p-1 rounded-lg hover:bg-green-100/60 transition-colors">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- TABLA DE LISTADO -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase">
            <tr>
              <th class="p-3">Código</th>
              <th class="p-3">Nombre</th>
              <th class="p-3">Peso/Unidad</th>
              <th class="p-3">Categoría</th>
              <th class="p-3 text-right">Precio Venta</th>
              <th class="p-3 text-right">Stock</th>
              <th class="p-3 text-right">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php if (empty($productos)): ?>
              <tr>
                <td colspan="7" class="p-6 text-center text-gray-500">
                  <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                  </svg>
                  <p class="font-medium">No hay productos registrados</p>
                  <p class="text-xs">Haz clic en "Nuevo Producto" para comenzar</p>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($productos as $producto): ?>
                <tr class="hover:bg-gray-50">
                  <td class="p-3 font-mono"><?= htmlspecialchars($producto['producto_codigo'] ?? '-') ?></td>
                  <td class="p-3 font-medium text-gray-900"><?= htmlspecialchars($producto['producto_nombre']) ?></td>
                  <td class="p-3">
                    <?php if ($producto['producto_peso'] && $producto['unidad_abreviatura']): 
                        $peso_num = floatval($producto['producto_peso']);
                        $peso_formateado = ($peso_num == intval($peso_num)) ? intval($peso_num) : number_format($peso_num, 2);
                    ?>
                      <?= $peso_formateado ?> <?= htmlspecialchars($producto['unidad_abreviatura']) ?>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td class="p-3"><?= htmlspecialchars($producto['categorias_nombre'] ?? 'Sin categoría') ?></td>
                  <td class="p-3 text-right font-medium text-olive">Bs. <?= number_format($producto['producto_precio_venta'], 2) ?></td>
                  <td class="p-3 text-right <?= $producto['producto_stock'] <= 0 ? 'text-rose-600 font-semibold' : ($producto['producto_stock'] <= 10 ? 'text-amber-600' : '') ?>">
                    <?= intval($producto['producto_stock']) ?>
                  </td>
                  <td class="p-3 text-right">
                    <a href="index.php?controller=productosController&action=editar&id=<?= $producto['producto_id'] ?>" class="inline-block p-1.5 text-gray-500 hover:text-olive hover:bg-gray-100 rounded-md transition-colors" title="Editar">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Resumen rápido -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <p class="text-xs text-gray-500">Total Productos</p>
        <p class="text-2xl font-bold text-gray-900"><?= count($productos) ?></p>
      </div>
      <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <p class="text-xs text-gray-500">Productos con Stock Bajo</p>
        <p class="text-2xl font-bold text-amber-600">
          <?= count(array_filter($productos, function($p) { return $p['producto_stock'] <= 10 && $p['producto_stock'] > 0; })) ?>
        </p>
      </div>
      <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <p class="text-xs text-gray-500">Productos Agotados</p>
        <p class="text-2xl font-bold text-rose-600">
          <?= count(array_filter($productos, function($p) { return $p['producto_stock'] <= 0; })) ?>
        </p>
      </div>
    </div>
  </main>

  <?php 
  include ruta . '/includes/sidebar.js';
  ?>
</body>
</html>