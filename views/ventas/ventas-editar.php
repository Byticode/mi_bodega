<?php
$page_title = 'Cobrar venta #' . $venta['venta_id'];
$page_desc  = 'Registra el pago de una venta pendiente.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';

$cliente = $venta['cliente_nombre']
    ? trim($venta['cliente_nombre'] . ' ' . ($venta['cliente_apellido'] ?? ''))
    : 'Consumidor final';
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--narrow">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <nav class="breadcrumb" aria-label="Ruta de navegación">
          <a href="index.php?controller=ventasController&action=listar">Ventas</a>
          <span aria-hidden="true">/</span>
          <a href="index.php?controller=ventasController&action=ver&id=<?= (int) $venta['venta_id'] ?>">#<?= (int) $venta['venta_id'] ?></a>
          <span aria-hidden="true">/</span>
          <span>Cobrar</span>
        </nav>
        <h1 class="page-title">Cobrar venta #<?= (int) $venta['venta_id'] ?></h1>
        <p class="page-sub">Al marcarla como completada se descuenta el stock de los productos.</p>
      </div>
    </div>

    <?php include ruta . '/includes/flash.php'; ?>

    <!-- Resumen -->
    <div class="card p-5">
      <dl class="grid grid-cols-2 gap-5">
        <div class="dl-item">
          <dt class="dl-label">Cliente</dt>
          <dd class="dl-value"><?= htmlspecialchars($cliente) ?></dd>
        </div>
        <div class="dl-item">
          <dt class="dl-label">Fecha</dt>
          <dd class="dl-value"><?= date('d/m/Y H:i', strtotime($venta['venta_fecha'])) ?></dd>
        </div>
        <div class="dl-item">
          <dt class="dl-label">Artículos</dt>
          <dd class="dl-value tnum"><?= count($detalles) ?></dd>
        </div>
        <div class="dl-item">
          <dt class="dl-label">Total a cobrar</dt>
          <dd class="dl-value money text-olive text-base"><?= money($venta['venta_total']) ?></dd>
        </div>
      </dl>
    </div>

    <form action="index.php?controller=ventasController&action=editar&id=<?= (int) $venta['venta_id'] ?>" method="POST" class="card p-5 flex flex-col gap-4">

      <div class="field">
        <label for="estado" class="label">Estado de la venta</label>
        <select id="estado" name="estado" class="select">
          <option value="completada" selected>Completada — cobrada y con stock descontado</option>
          <option value="pendiente">Pendiente — sigue sin cobrar</option>
          <option value="cancelada">Cancelada</option>
        </select>
      </div>

      <div class="field" id="metodoBox">
        <label for="metodo_pago" class="label">Método de pago <span class="req" aria-hidden="true">*</span></label>
        <select id="metodo_pago" name="metodo_pago" class="select">
          <option value="efectivo">Efectivo</option>
          <option value="transferencia">Transferencia</option>
          <option value="pago_movil">Pago móvil</option>
          <option value="biopago">Biopago</option>
          <option value="cashea">Cashea</option>
        </select>
      </div>

      <div class="field" id="referenciaBox">
        <label for="numero_pago" class="label">Número de referencia</label>
        <input type="text" id="numero_pago" name="numero_pago" class="input" placeholder="Opcional" autocomplete="off">
      </div>

      <div class="flex flex-wrap items-center justify-end gap-3 pt-3 border-t border-rule">
        <a href="index.php?controller=ventasController&action=listar" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary" id="guardarBtn">Cobrar venta</button>
      </div>
    </form>

  </div>
</main>

<script>
  (function () {
    var estado = document.getElementById('estado');
    var metodoBox = document.getElementById('metodoBox');
    var referenciaBox = document.getElementById('referenciaBox');
    var metodo = document.getElementById('metodo_pago');
    var referencia = document.getElementById('numero_pago');
    var guardar = document.getElementById('guardarBtn');

    function sincronizar() {
      var completada = estado.value === 'completada';
      metodoBox.hidden = !completada;
      referenciaBox.hidden = !completada;
      // Sin cobro no hay método de pago que guardar.
      metodo.disabled = !completada;
      referencia.disabled = !completada;
      guardar.textContent = completada ? 'Cobrar venta' : 'Actualizar estado';
    }

    estado.addEventListener('change', sincronizar);
    sincronizar();
  })();
</script>

<?php include ruta . '/includes/footer.php'; ?>
