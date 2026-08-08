<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Detalle Venta</title>
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
  <main class="flex-1 p-6 space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center space-x-3">
      <a href="index.php?controller=ventasController&action=listar" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-100">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </a>
      <div>
        <h2 class="font-display text-2xl font-semibold text-gray-900">Detalle de Venta #<?= $venta['venta_id'] ?></h2>
        <p class="text-xs text-gray-500">Información completa de la venta</p>
      </div>
    </div>

    <!-- Datos de la venta -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <p class="text-xs text-gray-500">Fecha</p>
          <p class="font-semibold text-gray-900"><?= date('d/m/Y H:i:s', strtotime($venta['venta_fecha'])) ?></p>
        </div>
        <div>
          <p class="text-xs text-gray-500">Cliente</p>
          <p class="font-semibold text-gray-900"><?= $venta['cliente_nombre'] ? htmlspecialchars($venta['cliente_nombre'] . ' ' . ($venta['cliente_apellido'] ?? '')) : 'Consumidor final' ?></p>
        </div>
        <div>
          <p class="text-xs text-gray-500">Estado</p>
          <p>
            <?php if ($venta['venta_estado'] == 'completada'): ?>
              <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">Completada</span>
            <?php elseif ($venta['venta_estado'] == 'pendiente'): ?>
              <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">Pendiente</span>
            <?php else: ?>
              <span class="px-2 py-1 bg-rose-100 text-rose-700 text-xs rounded-full font-medium">Cancelada</span>
            <?php endif; ?>
          </p>
        </div>
        <div>
          <p class="text-xs text-gray-500">Método de Pago</p>
          <p class="font-semibold text-gray-900">
            <?= $venta['venta_metodo_pago'] ? str_replace('_', ' ', ucfirst($venta['venta_metodo_pago'])) : '-' ?>
          </p>
        </div>
        <?php if ($venta['venta_numero_pago']): ?>
          <div>
            <p class="text-xs text-gray-500">Número de Referencia</p>
            <p class="font-mono text-sm font-semibold text-gray-900"><?= htmlspecialchars($venta['venta_numero_pago']) ?></p>
          </div>
        <?php endif; ?>
        <div>
          <p class="text-xs text-gray-500">Total</p>
          <p class="font-bold text-2xl text-olive">Bs <?= number_format($venta['venta_total'], 2) ?></p>
        </div>
      </div>
    </div>

    <!-- Detalles de productos -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="px-6 py-3 border-b border-gray-200">
        <h4 class="font-semibold text-gray-900 text-sm">Productos</h4>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase">
            <tr>
              <th class="p-3">#</th>
              <th class="p-3">Producto</th>
              <th class="p-3 text-right">Cantidad</th>
              <th class="p-3 text-right">Precio Unitario</th>
              <th class="p-3 text-right">Subtotal</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php foreach ($detalles as $index => $detalle): ?>
              <tr class="hover:bg-gray-50">
                <td class="p-3 text-gray-400"><?= $index + 1 ?></td>
                <td class="p-3 font-medium text-gray-900">
                  <?= htmlspecialchars($detalle['producto_nombre']) ?>
                  <span class="text-gray-400 text-xs block">Código: <?= htmlspecialchars($detalle['producto_codigo'] ?? 'N/A') ?></span>
                </td>
                <td class="p-3 text-right">
                  <?= intval($detalle['detalle_cantidad']) ?> 
                  unidades
                </td>
                <td class="p-3 text-right">Bs <?= number_format($detalle['detalle_precio_unitario'], 2) ?></td>
                <td class="p-3 text-right font-semibold">Bs <?= number_format($detalle['detalle_subtotal'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot class="bg-gray-50 border-t border-gray-200">
            <tr>
              <td colspan="4" class="p-3 text-right font-bold text-gray-900">TOTAL</td>
              <td class="p-3 text-right font-bold text-olive text-base">Bs <?= number_format($venta['venta_total'], 2) ?></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-end space-x-3">
      <a href="index.php?controller=ventasController&action=listar" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
        Volver al Listado
      </a>
      <?php if ($venta['venta_estado'] == 'pendiente'): ?>
        <a href="index.php?controller=ventasController&action=editar&id=<?= $venta['venta_id'] ?>" class="px-4 py-2 bg-olive hover:bg-olive-hover text-white text-xs font-bold rounded-lg transition-colors">
          Completar Venta
        </a>
      <?php endif; ?>
    </div>
  </main>

  <?php
  include ruta . '/includes/sidebar.js';
  ?>
</body>
</html>