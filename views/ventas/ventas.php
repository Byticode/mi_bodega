<?php 
$page_title = 'Historial de Ventas';
include RUTA_APP . '/includes/head.php'; 
?>

  <!-- SIDEBAR -->
  <?php 
  include RUTA_APP . '/includes/sidebar.php';
  ?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="flex-1 p-6 space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Ventas</h2>
        <p class="text-xs text-gray-500">Movimientos de mostrador de los últimos días</p>
      </div>
      <div class="flex items-center space-x-2">
        <a href="<?= url('pos') ?>" class="bg-olive hover:bg-olive-hover text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Nueva Venta
        </a>
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

    <?php if (isset($_SESSION['success'])): ?>
      <div class="flex items-center gap-3 p-4 text-sm text-green-800 border border-green-200/80 rounded-2xl bg-green-50/80 shadow-sm" role="alert">
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

    <!-- TARJETAS METRICAS -->
    <?php 
    $total_hoy = 0;
    $ventas_hoy = 0;
    foreach ($ventas as $v) {
        if (date('Y-m-d', strtotime($v['venta_fecha'])) == date('Y-m-d')) {
            $total_hoy += $v['venta_total'];
            $ventas_hoy++;
        }
    }
    ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="bg-warmCard p-4 rounded-xl border border-gray-200">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">TOTAL EN VENTAS DE HOY</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">Bs <?= number_format($total_hoy, 2) ?></p>
      </div>
      <div class="bg-warmCard p-4 rounded-xl border border-gray-200">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">VENTAS DE HOY</span>
        <p class="text-2xl font-bold text-gray-900 mt-1"><?= $ventas_hoy ?></p>
      </div>
    </div>

    <!-- TABLA DE VENTAS -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
          <thead>
            <tr class="border-b border-gray-200 text-xs text-gray-400 uppercase tracking-wider">
              <th class="py-3 px-4 font-semibold">ID</th>
              <th class="py-3 px-4 font-semibold">FECHA</th>
              <th class="py-3 px-4 font-semibold">CLIENTE</th>
              <th class="py-3 px-4 font-semibold">PRODUCTOS</th>
              <th class="py-3 px-4 font-semibold">TOTAL</th>
              <th class="py-3 px-4 font-semibold">MÉTODO</th>
              <th class="py-3 px-4 font-semibold">ESTADO</th>
              <th class="py-3 px-4 font-semibold text-right">ACCIÓN</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php if (empty($ventas)): ?>
              <tr>
                <td colspan="8" class="py-8 text-center text-gray-500">
                  <p class="font-medium">No hay ventas registradas</p>
                  <p class="text-xs">Haz clic en "Nueva Venta" para comenzar</p>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($ventas as $venta): ?>
                <tr class="hover:bg-gray-50">
                  <td class="py-3 px-4 font-mono text-xs text-olive font-bold">#<?= $venta['venta_id'] ?></td>
                  <td class="py-3 px-4 text-gray-500"><?= date('d/m/Y H:i', strtotime($venta['venta_fecha'])) ?></td>
                  <td class="py-3 px-4 font-medium text-gray-900">
                    <?= $venta['cliente_nombre'] ? htmlspecialchars($venta['cliente_nombre'] . ' ' . ($venta['cliente_apellido'] ?? '')) : 'Consumidor final' ?>
                  </td>
                  <td class="py-3 px-4">
                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                      <?= $venta['total_productos'] ?? 0 ?> productos
                    </span>
                  </td>
                  <td class="py-3 px-4 font-bold text-gray-900">Bs <?= number_format($venta['venta_total'], 2) ?></td>
                  <td class="py-3 px-4">
                    <?php if ($venta['venta_metodo_pago']): ?>
                      <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded capitalize">
                        <?= str_replace('_', ' ', $venta['venta_metodo_pago']) ?>
                      </span>
                    <?php else: ?>
                      <span class="text-gray-400 text-xs">-</span>
                    <?php endif; ?>
                  </td>
                  <td class="py-3 px-4">
                    <?php if ($venta['venta_estado'] == 'completada'): ?>
                      <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">Completada</span>
                    <?php elseif ($venta['venta_estado'] == 'pendiente'): ?>
                      <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">Pendiente</span>
                    <?php else: ?>
                      <span class="px-2 py-1 bg-rose-100 text-rose-700 text-xs rounded-full font-medium">Cancelada</span>
                    <?php endif; ?>
                  </td>
                  <td class="py-3 px-4 text-right">
                    <a href="<?= url('ventas/ver/' . $venta['venta_id']) ?>" class="inline-block p-1.5 text-gray-500 hover:text-olive hover:bg-gray-100 rounded-md transition-colors" title="Ver detalles">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </a>
                    <?php if ($venta['venta_estado'] == 'pendiente'): ?>
                      <a href="<?= url('ventas/editar/' . $venta['venta_id']) ?>" class="inline-block p-1.5 text-gray-500 hover:text-olive hover:bg-gray-100 rounded-md transition-colors" title="Editar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500">
        <span><?= count($ventas) ?> ventas registradas</span>
        <span>Última actualización: <?= date('d/m/Y H:i') ?></span>
      </div>
    </div>
  </main>

  <?php 
  include RUTA_APP . '/includes/sidebar.js';
  ?>
</body>
</html>