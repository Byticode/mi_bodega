<?php
$page_title = 'Venta #' . $venta['venta_id'];
$page_desc  = 'Detalle completo de la venta.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

$cliente = $venta['cliente_nombre']
    ? trim($venta['cliente_nombre'] . ' ' . ($venta['cliente_apellido'] ?? ''))
    : 'Consumidor final';
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--mid">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <nav class="breadcrumb" aria-label="Ruta de navegación">
          <a href="<?= url('ventas') ?>">Ventas</a>
          <span aria-hidden="true">/</span>
          <span>#<?= (int) $venta['venta_id'] ?></span>
        </nav>
        <h1 class="page-title">Venta #<?= (int) $venta['venta_id'] ?></h1>
        <p class="page-sub"><?= date('d/m/Y \a \l\a\s H:i', strtotime($venta['venta_fecha'])) ?></p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <a href="<?= url('ventas') ?>" class="btn btn-secondary">Volver</a>
        <?php if ($venta['venta_estado'] === 'pendiente'): ?>
          <a href="<?= url('ventas/editar/' . $venta['venta_id']) ?>" class="btn btn-primary">Cobrar venta</a>
        <?php endif; ?>
      </div>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <!-- Datos de la venta -->
    <div class="card p-5">
      <dl class="grid grid-cols-2 md:grid-cols-4 gap-5">
        <div class="dl-item">
          <dt class="dl-label">Cliente</dt>
          <dd class="dl-value"><?= htmlspecialchars($cliente) ?></dd>
        </div>
        <div class="dl-item">
          <dt class="dl-label">Estado</dt>
          <dd class="dl-value">
            <?php if ($venta['venta_estado'] === 'completada'): ?>
              <span class="badge badge-success"><span class="badge-dot"></span>Completada</span>
            <?php elseif ($venta['venta_estado'] === 'pendiente'): ?>
              <span class="badge badge-warn"><span class="badge-dot"></span>Pendiente</span>
            <?php else: ?>
              <span class="badge badge-danger"><span class="badge-dot"></span>Cancelada</span>
            <?php endif; ?>
          </dd>
        </div>
        <div class="dl-item">
          <dt class="dl-label">Método de pago</dt>
          <dd class="dl-value capitalize">
            <?= $venta['venta_metodo_pago'] ? htmlspecialchars(str_replace('_', ' ', $venta['venta_metodo_pago'])) : '—' ?>
          </dd>
        </div>
        <div class="dl-item">
          <dt class="dl-label">Total</dt>
          <dd class="dl-value money text-olive text-base"><?= money($venta['venta_total']) ?></dd>
        </div>
        <?php if ($venta['venta_numero_pago']): ?>
          <div class="dl-item">
            <dt class="dl-label">Referencia</dt>
            <dd class="dl-value font-mono"><?= htmlspecialchars($venta['venta_numero_pago']) ?></dd>
          </div>
        <?php endif; ?>
      </dl>
    </div>

    <!-- Productos -->
    <div class="card overflow-hidden">
      <div class="card-head">
        <h2 class="section-title">Productos</h2>
        <span class="text-xs text-ink-3 tnum"><?= count($detalles) ?> líneas</span>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Productos incluidos en la venta con cantidad, precio unitario y subtotal</caption>
          <thead>
            <tr>
              <th scope="col" class="w-10">#</th>
              <th scope="col">Producto</th>
              <th scope="col" class="num">Cantidad</th>
              <th scope="col" class="num">Precio unit.</th>
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
                <td class="num money text-ink-2"><?= money($detalle['detalle_precio_unitario']) ?></td>
                <td class="num money"><?= money($detalle['detalle_subtotal']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="4" class="num">Total</td>
              <td class="num money text-olive"><?= money($venta['venta_total']) ?></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

  </div>
</main>

<?php include RUTA_APP . '/includes/footer.php'; ?>
