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
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Nueva venta
      </a>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <!-- Resumen del día -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="stat">
        <span class="stat-label">Vendido hoy</span>
        <span class="stat-value stat-value--money stat-value--accent"><?= money($total_hoy) ?></span>
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
      <div class="card-head">
        <h2 class="section-title">Historial</h2>
        <?php if (!empty($ventas)): ?>
          <div class="search w-full sm:w-64">
            <label for="filtroVentas" class="sr-only">Filtrar ventas por número, cliente o método de pago</label>
            <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <input type="search" id="filtroVentas" class="input" placeholder="Filtrar…" autocomplete="off">
          </div>
        <?php endif; ?>
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
                  <div class="empty">
                    <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    <p class="empty-title">Todavía no hay ventas</p>
                    <p class="empty-sub">Registra la primera desde el punto de venta.</p>
                    <a href="<?= url('pos') ?>" class="btn btn-primary mt-3">Abrir punto de venta</a>
                  </div>
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
                  <td class="num money"><?= money($venta['venta_total']) ?></td>
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
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>
                    <?php if ($venta['venta_estado'] === 'pendiente'): ?>
                      <a href="<?= url('ventas/editar/' . $venta['venta_id']) ?>"
                         class="btn-icon" aria-label="Cobrar la venta #<?= (int) $venta['venta_id'] ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
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

      <?php if (!empty($ventas)): ?>
        <div class="card-foot">
          <span id="conteoVentas" role="status"><?= count($ventas) ?> ventas registradas</span>
          <span>Actualizado <?= date('d/m/Y H:i') ?></span>
        </div>
      <?php endif; ?>
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
