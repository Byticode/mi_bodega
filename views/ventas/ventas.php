<?php
$page_title = 'Ventas';
$page_desc  = 'Historial de ventas de mostrador.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

$hoy        = date('Y-m-d');
$total_hoy  = 0;
$ventas_hoy = 0;
$pendientes = 0;

foreach ($ventas as $v) {
    if (date('Y-m-d', strtotime($v['venta_fecha'])) === $hoy && $v['venta_estado'] !== 'cancelada') {
        $total_hoy += $v['venta_total'];
        $ventas_hoy++;
    }
    if ($v['venta_estado'] === 'pendiente') {
        $pendientes++;
    }
}
?>

<main id="contenido" class="app-main">
  <div class="app-wrap">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Ventas</h1>
        <p class="page-sub">Movimientos de mostrador, del más reciente al más antiguo.</p>
      </div>
      <a href="<?= url('pos') ?>" class="btn btn-primary">
        <i class="ti ti-plus text-base" aria-hidden="true"></i>
        Nueva venta
      </a>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <!-- Resumen del día -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="stat">
        <span class="stat-label">Vendido hoy</span>
        <span class="stat-value stat-value--money stat-value--accent"><?= money($total_hoy) ?></span>
        <?php if ($equiv = usd($total_hoy)): ?>
          <span class="stat-note"><?= $equiv ?> a tasa BCV</span>
        <?php endif; ?>
      </div>
      <div class="stat">
        <span class="stat-label">Ventas de hoy</span>
        <span class="stat-value"><?= $ventas_hoy ?></span>
      </div>
      <div class="stat">
        <span class="stat-label">Pendientes de cobro</span>
        <span class="stat-value <?= $pendientes ? 'stat-value--warn' : '' ?>"><?= $pendientes ?></span>
      </div>
    </div>

    <!-- Tabla -->
    <div class="card overflow-hidden">
      <div class="card-head flex-wrap gap-3">
        <h2 class="section-title">Historial</h2>
        <form method="GET" action="<?= url('ventas') ?>" class="search w-full sm:w-64 ml-auto relative flex items-center">
          <label for="filtroVentas" class="sr-only">Buscar por número, cliente o método de pago</label>
          <i class="ti ti-search search-icon" aria-hidden="true"></i>
          <input type="text" id="filtroVentas" name="q" value="<?= htmlspecialchars($search ?? '') ?>" class="input pl-9 pr-8" placeholder="Buscar venta, cliente…" autocomplete="off">
          <button type="button" class="btn-clear-search absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-3 hover:text-ink hover:bg-card-2 p-1 rounded-full flex items-center justify-center transition-colors" title="Limpiar búsqueda" aria-label="Limpiar búsqueda" <?= empty($search) ? 'style="display:none;"' : '' ?>>
            <i class="ti ti-x text-xs cursor-pointer text-red-500"></i>
          </button>
        </form>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Ventas registradas con fecha, cliente, total, método de pago y estado</caption>
          <thead>
            <tr>
              <th scope="col">Venta</th>
              <th scope="col">Fecha</th>
              <th scope="col">Cliente</th>
              <th scope="col" class="num">Artículos</th>
              <th scope="col" class="num">Total</th>
              <th scope="col">Método</th>
              <th scope="col">Estado</th>
              <th scope="col" class="col-actions">Acción</th>
            </tr>
          </thead>
          <tbody id="cuerpoVentas">
            <?php if (empty($ventas)): ?>
              <tr>
                <td colspan="8">
                  <?php if (!empty($search)): ?>
                    <div class="empty">
                      <i class="ti ti-search-off empty-icon" aria-hidden="true"></i>
                      <p class="empty-title">Sin coincidencias para «<?= htmlspecialchars($search) ?>»</p>
                      <p class="empty-sub">Ninguna venta coincide con el término buscado.</p>
                      <a href="<?= url('ventas') ?>" class="btn btn-secondary btn-sm mt-2">Ver todas las ventas</a>
                    </div>
                  <?php else: ?>
                    <div class="empty">
                      <i class="ti ti-shopping-cart empty-icon" aria-hidden="true"></i>
                      <p class="empty-title">Todavía no hay ventas</p>
                      <p class="empty-sub">Registra la primera desde el punto de venta.</p>
                      <a href="<?= url('pos') ?>" class="btn btn-primary mt-3">Abrir punto de venta</a>
                    </div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($ventas as $venta):
                $cliente = $venta['cliente_nombre']
                    ? trim($venta['cliente_nombre'] . ' ' . ($venta['cliente_apellido'] ?? ''))
                    : 'Consumidor final';
                $metodo = $venta['venta_metodo_pago'] ? str_replace('_', ' ', $venta['venta_metodo_pago']) : '';
              ?>
                <tr data-buscar="<?= htmlspecialchars(mb_strtolower('#' . $venta['venta_id'] . ' ' . $cliente . ' ' . $metodo . ' ' . $venta['venta_estado']), ENT_QUOTES) ?>">
                  <td class="font-mono text-xs font-semibold text-olive">#<?= (int) $venta['venta_id'] ?></td>
                  <td class="text-ink-2 whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($venta['venta_fecha'])) ?></td>
                  <td class="font-medium"><?= htmlspecialchars($cliente) ?></td>
                  <td class="num text-ink-2"><?= (int) ($venta['total_productos'] ?? 0) ?></td>
                  <td class="num">
                    <span class="money block"><?= money($venta['venta_total']) ?></span>
                    <?php if ($equiv = usd($venta['venta_total'])): ?>
                      <span class="text-xs text-ink-3 tnum"><?= $equiv ?></span>
                    <?php endif; ?>
                  </td>
                  <td class="text-ink-2 capitalize"><?= $metodo ? htmlspecialchars($metodo) : '—' ?></td>
                  <td>
                    <?php if ($venta['venta_estado'] === 'completada'): ?>
                      <span class="badge badge-success"><span class="badge-dot"></span>Completada</span>
                    <?php elseif ($venta['venta_estado'] === 'pendiente'): ?>
                      <span class="badge badge-warn"><span class="badge-dot"></span>Pendiente</span>
                    <?php else: ?>
                      <span class="badge badge-danger"><span class="badge-dot"></span>Cancelada</span>
                    <?php endif; ?>
                  </td>
                  <td class="col-actions">
                    <a href="<?= url('ventas/ver/' . $venta['venta_id']) ?>"
                       class="btn-icon" aria-label="Ver detalle de la venta #<?= (int) $venta['venta_id'] ?>">
                      <i class="ti ti-eye text-base" aria-hidden="true"></i>
                    </a>
                    <?php if ($venta['venta_estado'] === 'pendiente'): ?>
                      <a href="<?= url('ventas/editar/' . $venta['venta_id']) ?>"
                         class="btn-icon" aria-label="Cobrar la venta #<?= (int) $venta['venta_id'] ?>">
                        <i class="ti ti-pencil text-base" aria-hidden="true"></i>
                      </a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <tr id="filaSinResultados" hidden>
                <td colspan="8">
                  <div class="empty">
                    <p class="empty-title">Sin coincidencias</p>
                    <p class="empty-sub">Ninguna venta coincide con el filtro.</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php 
        $label_items = 'ventas';
        $ruta_paginacion = 'ventas';
        include RUTA_APP . '/includes/paginacion.php'; 
      ?>
    </div>

  </div>
</main>

<?php if (!empty($ventas)): ?>
<script>
  (function () {
    var filtro = document.getElementById('filtroVentas');
    var cuerpo = document.getElementById('cuerpoVentas');
    var vacio = document.getElementById('filaSinResultados');
    var conteo = document.getElementById('conteoVentas');
    if (!filtro) return;

    filtro.addEventListener('input', function () {
      var q = filtro.value.trim().toLowerCase();
      var visibles = 0;

      Array.prototype.forEach.call(cuerpo.querySelectorAll('tr[data-buscar]'), function (fila) {
        var coincide = !q || fila.dataset.buscar.indexOf(q) !== -1;
        fila.hidden = !coincide;
        if (coincide) visibles++;
      });

      if (vacio) vacio.hidden = visibles > 0;
      if (conteo) conteo.textContent = visibles === 1 ? '1 venta' : visibles + ' ventas';
    });
  })();
</script>
<?php endif; ?>

<?php include RUTA_APP . '/includes/footer.php'; ?>
