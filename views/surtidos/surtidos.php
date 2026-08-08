<?php
$page_title = 'Surtido';
$page_desc  = 'Historial de compras y entradas de mercancía.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';

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
      <a href="index.php?controller=surtidosController&action=crear" class="btn btn-primary">
        <i class="ti ti-plus text-base" aria-hidden="true"></i>
        Nuevo surtido
      </a>
    </div>

    <?php include ruta . '/includes/flash.php'; ?>

    <!-- Resumen -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="stat">
        <span class="stat-label">Surtidos</span>
        <span class="stat-value"><?= count($surtidos) ?></span>
      </div>
      <div class="stat">
        <span class="stat-label">Inversión acumulada</span>
        <span class="stat-value stat-value--money stat-value--accent"><?= money($inversion) ?></span>
        <?php if ($equiv = usd($inversion)): ?>
          <span class="stat-note"><?= $equiv ?> a tasa BCV</span>
        <?php endif; ?>
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
                    <i class="ti ti-truck-delivery empty-icon" aria-hidden="true"></i>
                    <p class="empty-title">Todavía no hay surtidos</p>
                    <p class="empty-sub">Registra una entrada de mercancía para aumentar el stock y llevar el costo.</p>
                    <a href="index.php?controller=surtidosController&action=crear" class="btn btn-primary mt-3">Nuevo surtido</a>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($surtidos as $surtido): ?>
                <tr>
                  <td class="font-mono text-xs font-semibold text-olive">#<?= (int) $surtido['surtido_id'] ?></td>
                  <td class="font-medium"><?= htmlspecialchars($surtido['proveedor_nombre'] ?? 'Sin proveedor') ?></td>
                  <td class="num text-ink-2"><?= (int) $surtido['total_productos'] ?></td>
                  <td class="num">
                    <span class="money block"><?= money($surtido['surtido_costo_total']) ?></span>
                    <?php if ($equiv = usd($surtido['surtido_costo_total'])): ?>
                      <span class="text-xs text-ink-3 tnum"><?= $equiv ?></span>
                    <?php endif; ?>
                  </td>
                  <td class="text-ink-2 whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($surtido['surtido_fecha'])) ?></td>
                  <td class="col-actions">
                    <a href="index.php?controller=surtidosController&action=ver&id=<?= (int) $surtido['surtido_id'] ?>"
                       class="btn-icon" aria-label="Ver detalle del surtido #<?= (int) $surtido['surtido_id'] ?>">
                      <i class="ti ti-eye text-base" aria-hidden="true"></i>
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

<?php include ruta . '/includes/footer.php'; ?>
