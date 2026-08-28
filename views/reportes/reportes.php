<?php
$page_title = 'Reportes';
$page_desc  = 'Resumen de ventas, productos más vendidos y alertas de stock.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

$totalVentas      = $reporte['total_ventas'] ?? 0;
$totalCompletadas = $reporte['total_completadas'] ?? 0;
$totalPendientes  = $reporte['total_pendientes'] ?? 0;
$totalCanceladas  = $reporte['total_canceladas'] ?? 0;

// Función helper para construir la URL preservando todos los GET params
function reporteUrl(array $override = []): string {
    $params = array_merge([
        'fecha_desde' => $_GET['fecha_desde'] ?? date('Y-m-01'),
        'fecha_hasta' => $_GET['fecha_hasta'] ?? date('Y-m-d'),
        'page_top'    => $_GET['page_top'] ?? 1,
        'page_stock'  => $_GET['page_stock'] ?? 1,
    ], $override);
    return url('reportes') . '?' . http_build_query($params);
}
?>

<main id="contenido" class="app-main">
  <div class="app-wrap">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Reportes</h1>
        <p class="page-sub">Resumen de ventas y estado del inventario.</p>
      </div>
      <form method="GET" action="<?= url('reportes') ?>" class="flex items-center gap-3">
        <div class="flex items-center gap-2">
          <label for="fecha_desde" class="sr-only">Desde</label>
          <input type="date" id="fecha_desde" name="fecha_desde" class="input" value="<?= htmlspecialchars($fecha_desde) ?>">
          <span class="text-ink-3">al</span>
          <label for="fecha_hasta" class="sr-only">Hasta</label>
          <input type="date" id="fecha_hasta" name="fecha_hasta" class="input" value="<?= htmlspecialchars($fecha_hasta) ?>">
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="ti ti-filter text-base" aria-hidden="true"></i>
          Filtrar
        </button>
        <a href="<?= url('reportes/exportarexcel?fecha_desde=' . urlencode($fecha_desde) . '&fecha_hasta=' . urlencode($fecha_hasta)) ?>"
           class="btn btn-primary" title="Exportar a Excel">
          <i class="ti ti-file-spreadsheet text-base" aria-hidden="true"></i>
          Excel
        </a>
        <a href="<?= url('reportes/exportarpdf?fecha_desde=' . urlencode($fecha_desde) . '&fecha_hasta=' . urlencode($fecha_hasta)) ?>"
           class="btn btn-primary" title="Exportar a PDF">
          <i class="ti ti-file-text text-base" aria-hidden="true"></i>
          PDF
        </a>
      </form>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <!-- Resumen de ventas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
      <div class="stat">
        <span class="stat-label">Total ventas</span>
        <span class="stat-value stat-value--accent"><?= $totalVentas ?></span>
      </div>
      <div class="stat">
        <span class="stat-label">Completadas</span>
        <span class="stat-value stat-value--accent stat-value--money"><?= money($totalCompletadas) ?></span>
      </div>
      <div class="stat">
        <span class="stat-label">Pendientes</span>
        <span class="stat-value <?= $totalPendientes > 0 ? 'stat-value--warn' : '' ?>"><?= money($totalPendientes) ?></span>
      </div>
      <div class="stat">
        <span class="stat-label">Canceladas</span>
        <span class="stat-value <?= $totalCanceladas > 0 ? 'stat-value--danger' : '' ?>"><?= money($totalCanceladas) ?></span>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- Top Productos -->
      <div class="card flex flex-col">
        <div class="card-head">
          <h2 class="section-title">
            <i class="ti ti-trophy text-base" aria-hidden="true"></i>
            Productos más vendidos
          </h2>
          <?php if ($totalTop > 0): ?>
            <span class="text-xs text-ink-3">
              Mostrando <strong><?= min(($pageTop - 1) * 10 + 1, $totalTop) ?>–<?= min($pageTop * 10, $totalTop) ?></strong> de <strong><?= $totalTop ?></strong>
            </span>
          <?php endif; ?>
        </div>
        <div class="table-wrap flex-1">
          <table class="table">
            <caption class="sr-only">Productos más vendidos en el período seleccionado</caption>
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Producto</th>
                <th scope="col" class="num">Cant.</th>
                <th scope="col" class="num">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($topProductos)): ?>
                <tr>
                  <td colspan="4">
                    <div class="empty">
                      <i class="ti ti-package empty-icon" aria-hidden="true"></i>
                      <p class="empty-title">Sin datos</p>
                      <p class="empty-sub">No hay ventas en el período seleccionado.</p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php
                  $offsetTop = ($pageTop - 1) * 10;
                  foreach ($topProductos as $i => $prod):
                ?>
                  <tr>
                    <td class="text-ink-3"><?= $offsetTop + $i + 1 ?></td>
                    <td class="font-medium"><?= htmlspecialchars($prod['producto_nombre']) ?></td>
                    <td class="num"><?= qty($prod['cantidad_vendida']) ?></td>
                    <td class="num"><span class="money"><?= money($prod['total_vendido']) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if ($totalPaginasTop > 1): ?>
          <div class="pagination">
            <a href="<?= reporteUrl(['page_top' => max(1, $pageTop - 1)]) ?>"
               class="pag-btn <?= $pageTop <= 1 ? 'opacity-40 pointer-events-none' : '' ?>"
               aria-label="Página anterior de top productos"
               <?= $pageTop <= 1 ? 'aria-disabled="true"' : '' ?>>
              <i class="ti ti-chevron-left text-base" aria-hidden="true"></i>
            </a>

            <?php
              $startTop = max(1, min($pageTop - 2, $totalPaginasTop - 4));
              $endTop   = min($totalPaginasTop, $startTop + 4);
              for ($p = $startTop; $p <= $endTop; $p++):
            ?>
              <a href="<?= reporteUrl(['page_top' => $p]) ?>"
                 class="pag-btn <?= $p === $pageTop ? 'pag-btn--active' : '' ?>"
                 aria-label="Página <?= $p ?> de top productos"
                 <?= $p === $pageTop ? 'aria-current="page"' : '' ?>>
                <?= $p ?>
              </a>
            <?php endfor; ?>

            <a href="<?= reporteUrl(['page_top' => min($totalPaginasTop, $pageTop + 1)]) ?>"
               class="pag-btn <?= $pageTop >= $totalPaginasTop ? 'opacity-40 pointer-events-none' : '' ?>"
               aria-label="Página siguiente de top productos"
               <?= $pageTop >= $totalPaginasTop ? 'aria-disabled="true"' : '' ?>>
              <i class="ti ti-chevron-right text-base" aria-hidden="true"></i>
            </a>
          </div>
        <?php endif; ?>
      </div>

      <!-- Métodos de pago -->
      <div class="card">
        <div class="card-head">
          <h2 class="section-title">
            <i class="ti ti-credit-card text-base" aria-hidden="true"></i>
            Ventas por método de pago
          </h2>
        </div>
        <div class="table-wrap">
          <table class="table">
            <caption class="sr-only">Ventas agrupadas por método de pago</caption>
            <thead>
              <tr>
                <th scope="col">Método</th>
                <th scope="col" class="num">Cant.</th>
                <th scope="col" class="num">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($metodosPago)): ?>
                <tr>
                  <td colspan="3">
                    <div class="empty">
                      <i class="ti ti-credit-card empty-icon" aria-hidden="true"></i>
                      <p class="empty-title">Sin datos</p>
                      <p class="empty-sub">No hay ventas completadas en el período.</p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($metodosPago as $metodo): ?>
                  <tr>
                    <td class="font-medium capitalize"><?= htmlspecialchars(str_replace('_', ' ', $metodo['metodo_pago'])) ?></td>
                    <td class="num"><?= $metodo['cantidad_ventas'] ?></td>
                    <td class="num"><span class="money"><?= money($metodo['total_ventas']) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- Alertas de stock bajo -->
    <div class="card flex flex-col">
      <div class="card-head">
        <h2 class="section-title">
          <i class="ti ti-alert-triangle text-base text-warn" aria-hidden="true"></i>
          Alertas de stock bajo
        </h2>
        <?php if ($totalStock > 0): ?>
          <span class="text-xs text-ink-3">
            Mostrando <strong><?= min(($pageStock - 1) * 10 + 1, $totalStock) ?>–<?= min($pageStock * 10, $totalStock) ?></strong> de <strong><?= $totalStock ?></strong> productos
          </span>
        <?php endif; ?>
      </div>
      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Productos con stock bajo (5 o menos unidades)</caption>
          <thead>
            <tr>
              <th scope="col">Código</th>
              <th scope="col">Producto</th>
              <th scope="col">Categoría</th>
              <th scope="col" class="num">Stock</th>
              <th scope="col" class="num">Precio venta</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($productosBajos)): ?>
              <tr>
                <td colspan="5">
                  <div class="empty">
                    <i class="ti ti-check-circle empty-icon" aria-hidden="true"></i>
                    <p class="empty-title">Todo en orden</p>
                    <p class="empty-sub">No hay productos con stock bajo.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($productosBajos as $prod): ?>
                <tr>
                  <td class="font-mono text-xs text-ink-2"><?= htmlspecialchars($prod['producto_codigo'] ?? '—') ?></td>
                  <td class="font-medium"><?= htmlspecialchars($prod['producto_nombre']) ?></td>
                  <td class="text-ink-2"><?= htmlspecialchars($prod['categorias_nombre'] ?? '—') ?></td>
                  <td class="num">
                    <?php if ($prod['producto_stock'] == 0): ?>
                      <span class="badge badge-danger"><span class="badge-dot"></span><?= qty($prod['producto_stock']) ?></span>
                    <?php else: ?>
                      <span class="badge badge-warn"><span class="badge-dot"></span><?= qty($prod['producto_stock']) ?></span>
                    <?php endif; ?>
                  </td>
                  <td class="num"><span class="money"><?= money($prod['producto_precio_venta']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($totalPaginasStock > 1): ?>
        <div class="pagination">
          <a href="<?= reporteUrl(['page_stock' => max(1, $pageStock - 1)]) ?>"
             class="pag-btn <?= $pageStock <= 1 ? 'opacity-40 pointer-events-none' : '' ?>"
             aria-label="Página anterior de stock bajo"
             <?= $pageStock <= 1 ? 'aria-disabled="true"' : '' ?>>
            <i class="ti ti-chevron-left text-base" aria-hidden="true"></i>
          </a>

          <?php
            $startStock = max(1, min($pageStock - 2, $totalPaginasStock - 4));
            $endStock   = min($totalPaginasStock, $startStock + 4);
            for ($p = $startStock; $p <= $endStock; $p++):
          ?>
            <a href="<?= reporteUrl(['page_stock' => $p]) ?>"
               class="pag-btn <?= $p === $pageStock ? 'pag-btn--active' : '' ?>"
               aria-label="Página <?= $p ?>"
               <?= $p === $pageStock ? 'aria-current="page"' : '' ?>>
              <?= $p ?>
            </a>
          <?php endfor; ?>

          <a href="<?= reporteUrl(['page_stock' => min($totalPaginasStock, $pageStock + 1)]) ?>"
             class="pag-btn <?= $pageStock >= $totalPaginasStock ? 'opacity-40 pointer-events-none' : '' ?>"
             aria-label="Página siguiente de stock bajo"
             <?= $pageStock >= $totalPaginasStock ? 'aria-disabled="true"' : '' ?>>
            <i class="ti ti-chevron-right text-base" aria-hidden="true"></i>
          </a>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

<?php include RUTA_APP . '/includes/footer.php'; ?>
