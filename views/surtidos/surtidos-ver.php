<?php
$page_title = 'Surtido #' . $surtido['surtido_id'];
$page_desc  = 'Detalle completo del surtido.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--mid">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <nav class="breadcrumb" aria-label="Ruta de navegación">
          <a href="<?= url('surtidos') ?>">Surtido</a>
          <span aria-hidden="true">/</span>
          <span>#<?= (int) $surtido['surtido_id'] ?></span>
        </nav>
        <h1 class="page-title">Surtido #<?= (int) $surtido['surtido_id'] ?></h1>
        <p class="page-sub"><?= date('d/m/Y \a \l\a\s H:i', strtotime($surtido['surtido_fecha'])) ?></p>
      </div>
      <a href="<?= url('surtidos') ?>" class="btn btn-secondary">Volver</a>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <!-- Datos del surtido -->
    <div class="card p-5">
      <dl class="grid grid-cols-2 md:grid-cols-3 gap-5">
        <div class="dl-item">
          <dt class="dl-label">Proveedor</dt>
          <dd class="dl-value"><?= htmlspecialchars($surtido['proveedor_nombre'] ?? 'Sin proveedor') ?></dd>
        </div>
        <div class="dl-item">
          <dt class="dl-label">Líneas</dt>
          <dd class="dl-value tnum"><?= count($detalles) ?></dd>
        </div>
        <div class="dl-item">
          <dt class="dl-label">Costo total</dt>
          <dd class="dl-value money text-olive text-base"><?= money($surtido['surtido_costo_total']) ?></dd>
        </div>
      </dl>
    </div>

    <!-- Productos -->
    <div class="card overflow-hidden">
      <div class="card-head">
        <h2 class="section-title">Productos del surtido</h2>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Productos del surtido con cantidad, costo unitario y subtotal</caption>
          <thead>
            <tr>
              <th scope="col" class="w-10">#</th>
              <th scope="col">Producto</th>
              <th scope="col" class="num">Cantidad</th>
              <th scope="col" class="num">Costo unit.</th>
              <th scope="col" class="num">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($detalles as $index => $detalle): ?>
              <tr>
                <td class="text-ink-3 tnum"><?= $index + 1 ?></td>
                <td>
                  <span class="font-medium block"><?= htmlspecialchars($detalle['producto_nombre']) ?></span>
                  <span class="text-xs text-ink-3 font-mono">
                    <?= $detalle['producto_codigo'] ? htmlspecialchars($detalle['producto_codigo']) : 'Sin código' ?>
                  </span>
                </td>
                <td class="num"><?= qty($detalle['detalle_cantidad']) ?></td>
                <td class="num money text-ink-2"><?= money($detalle['detalle_precio_costo']) ?></td>
                <td class="num money"><?= money($detalle['detalle_subtotal']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="4" class="num">Total</td>
              <td class="num money text-olive"><?= money($surtido['surtido_costo_total']) ?></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

  </div>
</main>

<?php include RUTA_APP . '/includes/footer.php'; ?>
