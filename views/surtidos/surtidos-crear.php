<?php
$page_title = 'Nuevo surtido';
$page_desc  = 'Registra una entrada de mercancía al inventario.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';

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
          <i class="ti ti-trash text-base" aria-hidden="true"></i>
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
          <a href="index.php?controller=surtidosController&action=listar">Surtido</a>
          <span aria-hidden="true">/</span>
          <span>Nuevo surtido</span>
        </nav>
        <h1 class="page-title">Nuevo surtido</h1>
        <p class="page-sub">Las cantidades que registres se suman al stock de cada producto.</p>
      </div>
    </div>

    <?php include ruta . '/includes/flash.php'; ?>

    <?php if (empty($productos)): ?>
      <div class="card">
        <div class="empty">
          <p class="empty-title">No hay productos para surtir</p>
          <p class="empty-sub">Crea al menos un producto en el inventario antes de registrar un surtido.</p>
          <a href="index.php?controller=productosController&action=crear" class="btn btn-primary mt-3">Crear producto</a>
        </div>
      </div>
    <?php else: ?>

      <form id="surtidoForm" action="index.php?controller=surtidosController&action=crear" method="POST" class="flex flex-col gap-4">

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
              <i class="ti ti-plus text-sm" aria-hidden="true"></i>
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
          <a href="index.php?controller=surtidosController&action=listar" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Registrar surtido</button>
        </div>
      </form>

      <!-- Plantilla para las filas que agrega el script -->
      <template id="plantillaFila"><?= fila_surtido(0, $opciones_productos) ?></template>

    <?php endif; ?>

  </div>
</main>

<?php if (!empty($productos)): ?>
<script>
  (function () {
    'use strict';

    var contenedor = document.getElementById('filas');
    var plantilla = document.getElementById('plantillaFila');
    var agregarBtn = document.getElementById('agregarFila');
    var totalOut = document.getElementById('totalCosto');
    var aviso = document.getElementById('avisoFilas');
    var form = document.getElementById('surtidoForm');
    var contador = 1; // la fila 1 ya viene renderizada desde el servidor

    function bs(n) {
      return 'Bs ' + n.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function calcular() {
      var total = 0;
      Array.prototype.forEach.call(contenedor.querySelectorAll('.fila'), function (fila) {
        var cantidad = parseInt(fila.querySelector('.fila-cantidad').value, 10) || 0;
        var precio = parseFloat(fila.querySelector('.fila-precio').value) || 0;
        total += cantidad * precio;
      });
      totalOut.textContent = bs(total);
    }

    // El botón de quitar solo tiene sentido con más de una fila.
    function sincronizarQuitar() {
      var filas = contenedor.querySelectorAll('.fila');
      Array.prototype.forEach.call(filas, function (fila) {
        fila.querySelector('.fila-quitar').disabled = filas.length <= 1;
      });
      aviso.textContent = filas.length === 1 ? '1 producto en el surtido' : filas.length + ' productos en el surtido';
    }

    // Conecta los listeners de una fila (venga del servidor o del clon).
    function activarFila(fila) {
      var select = fila.querySelector('.fila-producto');
      var cantidad = fila.querySelector('.fila-cantidad');
      var precio = fila.querySelector('.fila-precio');
      var quitar = fila.querySelector('.fila-quitar');

      select.addEventListener('change', function () {
        var opcion = select.options[select.selectedIndex];
        precio.placeholder = opcion && opcion.dataset.precio
          ? 'Venta: ' + opcion.dataset.precio
          : '0,00';
        calcular();
      });

      cantidad.addEventListener('input', calcular);
      precio.addEventListener('input', calcular);

      quitar.addEventListener('click', function () {
        if (contenedor.querySelectorAll('.fila').length <= 1) return;
        fila.remove();
        sincronizarQuitar();
        calcular();
      });

      return select;
    }

    function agregarFila() {
      var fila = plantilla.content.firstElementChild.cloneNode(true);
      contador++;

      // Los ids de la plantilla terminan en 0: hay que renumerarlos o el
      // label apuntaría al control de otra fila.
      ['producto', 'cantidad', 'precio'].forEach(function (campo) {
        var control = fila.querySelector('#surtido-' + campo + '-0');
        var label = fila.querySelector('label[for="surtido-' + campo + '-0"]');
        var id = 'surtido-' + campo + '-' + contador;
        control.id = id;
        if (label) label.setAttribute('for', id);
      });

      fila.querySelector('.fila-quitar')
        .setAttribute('aria-label', 'Quitar el producto ' + contador + ' del surtido');

      contenedor.appendChild(fila);
      activarFila(fila).focus();
      sincronizarQuitar();
      calcular();
    }

    agregarBtn.addEventListener('click', agregarFila);
    form.addEventListener('submit', calcular);

    Array.prototype.forEach.call(contenedor.querySelectorAll('.fila'), activarFila);
    sincronizarQuitar();
    calcular();
  })();
</script>
<?php endif; ?>

<?php include ruta . '/includes/footer.php'; ?>
