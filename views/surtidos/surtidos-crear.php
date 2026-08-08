<?php
$page_title = 'Nuevo surtido';
$page_desc  = 'Registra una entrada de mercancía al inventario.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

// Opciones de producto: se generan una vez y se reutilizan en cada fila nueva.
ob_start();
foreach ($productos as $producto) {
    printf(
        '<option value="%d" data-precio="%s">%s (stock: %d %s)</option>',
        (int) $producto['producto_id'],
        htmlspecialchars((string) $producto['producto_precio_venta'], ENT_QUOTES),
        htmlspecialchars($producto['producto_nombre']),
        (int) $producto['producto_stock'],
        htmlspecialchars($producto['unidad_abreviatura'] ?? 'u')
    );
}
$opciones_productos = ob_get_clean();

/**
 * Una fila de producto. La primera se emite desde el servidor para que el
 * formulario siga siendo usable sin JavaScript; las demás las clona el script.
 */
function fila_surtido(int $n, string $opciones): string
{
    ob_start(); ?>
    <div class="fila grid grid-cols-1 sm:grid-cols-[2fr_1fr_1fr_auto] gap-3 items-start pb-4 border-b border-rule last:border-b-0 last:pb-0">
      <div class="field">
        <label class="label" for="surtido-producto-<?= $n ?>">Producto <span class="req" aria-hidden="true">*</span></label>
        <select id="surtido-producto-<?= $n ?>" name="producto_id[]" class="select fila-producto" required>
          <option value="">Seleccionar producto…</option>
          <?= $opciones ?>
        </select>
      </div>
      <div class="field">
        <label class="label" for="surtido-cantidad-<?= $n ?>">Cantidad <span class="req" aria-hidden="true">*</span></label>
        <input type="number" step="1" min="1" id="surtido-cantidad-<?= $n ?>" name="cantidad[]" class="input input--num fila-cantidad" placeholder="0" required>
      </div>
      <div class="field">
        <label class="label" for="surtido-precio-<?= $n ?>">Costo unitario <span class="req" aria-hidden="true">*</span></label>
        <input type="number" step="0.01" min="0.01" id="surtido-precio-<?= $n ?>" name="precio_costo[]" class="input input--num fila-precio" placeholder="0,00" required>
      </div>
      <div class="field">
        <span class="label" aria-hidden="true">&nbsp;</span>
        <button type="button" class="btn-icon btn-icon--danger fila-quitar" aria-label="Quitar el producto <?= $n ?> del surtido">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
        </button>
      </div>
    </div>
    <?php return ob_get_clean();
}
?>

<main id="contenido" class="app-main">
  <div class="app-wrap">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <nav class="breadcrumb" aria-label="Ruta de navegación">
          <a href="<?= url('surtidos') ?>">Surtido</a>
          <span aria-hidden="true">/</span>
          <span>Nuevo surtido</span>
        </nav>
        <h1 class="page-title">Nuevo surtido</h1>
        <p class="page-sub">Las cantidades que registres se suman al stock de cada producto.</p>
      </div>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <?php if (empty($productos)): ?>
      <div class="card">
        <div class="empty">
          <p class="empty-title">No hay productos para surtir</p>
          <p class="empty-sub">Crea al menos un producto en el inventario antes de registrar un surtido.</p>
          <a href="<?= url('productos/crear') ?>" class="btn btn-primary mt-3">Crear producto</a>
        </div>
      </div>
    <?php else: ?>

      <form id="surtidoForm" action="<?= url('surtidos/crear') ?>" method="POST" class="flex flex-col gap-4">

        <div class="card p-5">
          <div class="field max-w-sm">
            <label for="proveedor_id" class="label">Proveedor <span class="req" aria-hidden="true">*</span></label>
            <select id="proveedor_id" name="proveedor_id" class="select" required>
              <option value="">Seleccionar proveedor…</option>
              <?php foreach ($proveedores as $proveedor): ?>
                <option value="<?= (int) $proveedor['proveedor_id'] ?>"><?= htmlspecialchars($proveedor['proveedor_nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="card overflow-hidden">
          <div class="card-head">
            <div>
              <h2 class="section-title">Productos del surtido</h2>
              <p class="section-sub">Cantidad en unidades enteras y costo de compra por unidad.</p>
            </div>
            <button type="button" id="agregarFila" class="btn btn-secondary btn-sm">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
              Agregar producto
            </button>
          </div>

          <div id="filas" class="p-5 flex flex-col gap-4"><?= fila_surtido(1, $opciones_productos) ?></div>

          <div class="card-foot">
            <span id="avisoFilas" role="status" aria-live="polite"></span>
            <span class="flex items-baseline gap-2">
              <span class="text-ink-2">Costo total</span>
              <output id="totalCosto" class="money text-olive text-base"><?= money(0) ?></output>
            </span>
          </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3">
          <a href="<?= url('surtidos') ?>" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Registrar surtido</button>
        </div>
      </form>

      <!-- Plantilla para las filas que agrega el script -->
      <template id="plantillaFila"><?= fila_surtido(0, $opciones_productos) ?></template>

    <?php endif; ?>

  </div>
</main>

<script src="<?= assets('scripts/surtidos-crear.js') ?>"></script>

<?php include RUTA_APP . '/includes/footer.php'; ?>
