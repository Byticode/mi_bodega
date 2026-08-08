<?php
$page_title = 'Surtido';
$page_desc  = 'Historial de compras y entradas de mercancía.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

$inversion = array_sum(array_column($surtidos, 'surtido_costo_total'));
$ultimo    = !empty($surtidos) ? $surtidos[0]['surtido_fecha'] : null;
?>

<main id="contenido" class="app-main">
  <div class="app-wrap">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Surtido</h1>
        <p class="page-sub">Entradas de mercancía al inventario y su costo.</p>
      </div>
      <a href="<?= url('surtidos/crear') ?>" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Nuevo surtido
      </a>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <!-- Resumen -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="stat">
        <span class="stat-label">Surtidos</span>
        <span class="stat-value"><?= count($surtidos) ?></span>
      </div>
      <div class="stat">
        <span class="stat-label">Inversión acumulada</span>
        <span class="stat-value stat-value--money stat-value--accent"><?= money($inversion) ?></span>
      </div>
      <div class="stat">
        <span class="stat-label">Último surtido</span>
        <span class="stat-value"><?= $ultimo ? date('d/m/Y', strtotime($ultimo)) : '—' ?></span>
      </div>
    </div>

    <!-- Tabla -->
    <div class="card overflow-hidden">
      <div class="card-head">
        <h2 class="section-title">Historial de surtidos</h2>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Surtidos registrados con proveedor, número de productos, costo y fecha</caption>
          <thead>
            <tr>
              <th scope="col">Surtido</th>
              <th scope="col">Proveedor</th>
              <th scope="col" class="num">Productos</th>
              <th scope="col" class="num">Costo total</th>
              <th scope="col">Fecha</th>
              <th scope="col" class="col-actions">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($surtidos)): ?>
              <tr>
                <td colspan="6">
                  <div class="empty">
                    <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    <p class="empty-title">Todavía no hay surtidos</p>
                    <p class="empty-sub">Registra una entrada de mercancía para aumentar el stock y llevar el costo.</p>
                    <a href="<?= url('surtidos/crear') ?>" class="btn btn-primary mt-3">Nuevo surtido</a>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($surtidos as $surtido): ?>
                <tr>
                  <td class="font-mono text-xs font-semibold text-olive">#<?= (int) $surtido['surtido_id'] ?></td>
                  <td class="font-medium"><?= htmlspecialchars($surtido['proveedor_nombre'] ?? 'Sin proveedor') ?></td>
                  <td class="num text-ink-2"><?= (int) $surtido['total_productos'] ?></td>
                  <td class="num money"><?= money($surtido['surtido_costo_total']) ?></td>
                  <td class="text-ink-2 whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($surtido['surtido_fecha'])) ?></td>
                  <td class="col-actions">
                    <a href="<?= url('surtidos/ver/' . $surtido['surtido_id']) ?>"
                       class="btn-icon" aria-label="Ver detalle del surtido #<?= (int) $surtido['surtido_id'] ?>">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if (!empty($surtidos)): ?>
        <div class="card-foot">
          <span><?= count($surtidos) ?> surtidos registrados</span>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

<?php include RUTA_APP . '/includes/footer.php'; ?>
