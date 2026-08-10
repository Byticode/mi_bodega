<?php
$page_title = 'Inventario';
$page_desc  = 'Catálogo de productos, precios y stock disponible.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

$bajo_stock = array_filter($productos, fn($p) => $p['producto_stock'] > 0 && $p['producto_stock'] <= 10);
$agotados   = array_filter($productos, fn($p) => $p['producto_stock'] <= 0);
$valor_usd  = array_sum(array_map(fn($p) => $p['producto_stock'] * $p['producto_precio_venta'], $productos));
$tasa_act   = tasa_vigente()['tasa_usd'] ?? 0;
?>

<main id="contenido" class="app-main">
  <div class="app-wrap">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Inventario</h1>
        <p class="page-sub">Catálogo de productos con precios en dólares ($) y conversión en bolívares (Bs).</p>
      </div>
      <div class="flex items-center gap-2">
        <a href="<?= url('productos/crear') ?>" class="btn btn-primary">
          <i class="ti ti-plus text-base" aria-hidden="true"></i>
          Nuevo producto
        </a>
      </div>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <!-- Resumen -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
      <div class="stat">
        <span class="stat-label">Productos</span>
        <span class="stat-value"><?= count($productos) ?></span>
      </div>
      <div class="stat">
        <span class="stat-label">Stock bajo</span>
        <span class="stat-value <?= count($bajo_stock) ? 'stat-value--warn' : '' ?>"><?= count($bajo_stock) ?></span>
        <span class="stat-note">10 unidades o menos</span>
      </div>
      <div class="stat">
        <span class="stat-label">Agotados</span>
        <span class="stat-value <?= count($agotados) ? 'stat-value--danger' : '' ?>"><?= count($agotados) ?></span>
      </div>
      <div class="stat">
        <span class="stat-label">Valor en stock</span>
        <span class="stat-value stat-value--money stat-value--accent"><?= usd($valor_usd) ?></span>
        <span class="stat-note"><?= bs($valor_usd) ? '≈ ' . bs($valor_usd) . ' (A precio de venta)' : 'A precio de venta' ?></span>
      </div>
    </div>

    <!-- Tabla -->
    <div class="card overflow-hidden">
      <div class="card-head flex-wrap gap-3">
        <div class="flex items-center gap-3 flex-wrap">
          <h2 class="section-title">Productos registrados</h2>
          <!-- Formulario / Barra de acciones masivas -->
          <div id="formSeleccionMasiva" class="hidden items-center gap-2">
            <button type="button" id="btnAjustarPreciosMasivo" class="btn btn-secondary btn-sm flex items-center gap-1.5 font-medium px-3 py-1.5 text-xs rounded-lg transition-colors border border-gray-300 hover:bg-gray-100">
              <i class="ti ti-percentage text-base" aria-hidden="true"></i>
              <span>Ajustar precios (<span class="countSeleccionados">0</span>)</span>
            </button>

            <form id="formEliminarMasivo" action="<?= url('productos/eliminarMasivo') ?>" method="POST" class="inline">
              <input type="hidden" name="ids" id="inputIdsMasivo" value="[]">
              <button type="button" id="btnEliminarMasivo" class="btn btn-danger btn-sm flex items-center gap-1.5 font-medium px-3 py-1.5 text-xs rounded-lg transition-colors">
                <i class="ti ti-trash text-base" aria-hidden="true"></i>
                <span>Eliminar (<span class="countSeleccionados">0</span>)</span>
              </button>
            </form>
          </div>
        </div>

        <?php if (!empty($productos)): ?>
          <div class="search w-full sm:w-64 ml-auto">
            <label for="filtroInventario" class="sr-only">Filtrar productos por nombre, código o categoría</label>
            <i class="ti ti-search search-icon" aria-hidden="true"></i>
            <input type="search" id="filtroInventario" class="input" placeholder="Filtrar…" autocomplete="off">
          </div>
        <?php endif; ?>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Listado de productos con código, presentación, categoría, precio en dólares y bolívares, y stock</caption>
          <thead>
            <tr>
              <th scope="col" class="w-10 text-center">
                <input type="checkbox" id="selectAllProductos" class="checkbox rounded cursor-pointer" aria-label="Seleccionar todos los productos">
              </th>
              <th scope="col">Código</th>
              <th scope="col">Producto</th>
              <th scope="col">Presentación</th>
              <th scope="col">Categoría</th>
              <th scope="col" class="num">Precio ($ / Bs)</th>
              <th scope="col" class="num">Stock</th>
              <th scope="col" class="col-actions">Acción</th>
            </tr>
          </thead>
          <tbody id="cuerpoInventario">
            <?php if (empty($productos)): ?>
              <tr>
                <td colspan="8">
                  <div class="empty">
                    <i class="ti ti-package empty-icon" aria-hidden="true"></i>
                    <p class="empty-title">No hay productos registrados</p>
                    <p class="empty-sub">Crea el primero para poder venderlo y surtirlo.</p>
                    <a href="<?= url('productos/crear') ?>" class="btn btn-primary mt-3">Nuevo producto</a>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($productos as $producto):
                $stock = (int) $producto['producto_stock'];
                $categoria = $producto['categorias_nombre'] ?? 'Sin categoría';
              ?>
                <tr data-id="<?= (int) $producto['producto_id'] ?>" data-buscar="<?= htmlspecialchars(mb_strtolower($producto['producto_nombre'] . ' ' . ($producto['producto_codigo'] ?? '') . ' ' . $categoria), ENT_QUOTES) ?>">
                  <td class="text-center">
                    <input type="checkbox" class="select-producto checkbox rounded cursor-pointer" value="<?= (int) $producto['producto_id'] ?>" aria-label="Seleccionar <?= htmlspecialchars($producto['producto_nombre'], ENT_QUOTES) ?>">
                  </td>
                  <td class="font-mono text-xs text-ink-3"><?= htmlspecialchars($producto['producto_codigo'] ?? '—') ?></td>
                  <td class="font-medium"><?= htmlspecialchars($producto['producto_nombre']) ?></td>
                  <td class="text-ink-2">
                    <?php if ($producto['producto_peso'] && $producto['unidad_abreviatura']): ?>
                      <?= qty($producto['producto_peso']) ?> <?= htmlspecialchars($producto['unidad_abreviatura']) ?>
                    <?php else: ?>
                      —
                    <?php endif; ?>
                  </td>
                  <td class="text-ink-2"><?= htmlspecialchars($categoria) ?></td>
                  <td class="num">
                    <span class="money text-olive font-bold block"><?= usd($producto['producto_precio_venta']) ?></span>
                    <?php if ($equiv = bs($producto['producto_precio_venta'])): ?>
                      <span class="text-xs text-ink-3 tnum"><?= $equiv ?></span>
                    <?php endif; ?>
                  </td>
                  <td class="num">
                    <?php if ($stock <= 0): ?>
                      <span class="badge badge-danger"><span class="badge-dot"></span>Agotado</span>
                    <?php elseif ($stock <= 10): ?>
                      <span class="badge badge-warn"><span class="badge-dot"></span><?= $stock ?> unid.</span>
                    <?php else: ?>
                      <span class="badge badge-success"><span class="badge-dot"></span><?= $stock ?> unid.</span> 
                    <?php endif; ?>
                  </td>
                  <td class="col-actions">
                    <div class="flex items-center gap-1 justify-end">
                      <a href="<?= url('productos/editar/' . $producto['producto_id']) ?>"
                         class="btn-icon" title="Editar <?= htmlspecialchars($producto['producto_nombre'], ENT_QUOTES) ?>" aria-label="Editar <?= htmlspecialchars($producto['producto_nombre'], ENT_QUOTES) ?>">
                        <i class="ti ti-pencil text-base" aria-hidden="true"></i>
                      </a>
                      <button type="button" class="btn-icon text-rose-600 hover:text-rose-800 btn-eliminar-uno"
                              data-id="<?= (int) $producto['producto_id'] ?>"
                              data-nombre="<?= htmlspecialchars($producto['producto_nombre'], ENT_QUOTES) ?>"
                              title="Eliminar <?= htmlspecialchars($producto['producto_nombre'], ENT_QUOTES) ?>"
                              aria-label="Eliminar <?= htmlspecialchars($producto['producto_nombre'], ENT_QUOTES) ?>">
                        <i class="ti ti-trash text-base" aria-hidden="true"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
              <tr id="filaSinResultados" hidden>
                <td colspan="8">
                  <div class="empty">
                    <p class="empty-title">Sin coincidencias</p>
                    <p class="empty-sub">Ningún producto coincide con el filtro.</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if (!empty($productos)): ?>
        <div class="card-foot">
          <span id="conteoInventario" role="status"><?= count($productos) ?> productos</span>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

<!-- Modal de confirmación para eliminar individual/masivo -->
<div id="modalConfirmarEliminar" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto hidden" role="dialog" aria-modal="true" aria-labelledby="modalEliminarTitulo">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-200 p-6 sm:p-7 relative my-auto">
    <div class="flex items-start justify-between gap-3">
      <div class="flex items-start gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
          <i class="ti ti-alert-triangle text-xl" aria-hidden="true"></i>
        </div>
        <div>
          <h3 id="modalEliminarTitulo" class="text-base sm:text-lg font-bold text-gray-900">¿Confirmar eliminación?</h3>
          <p id="modalEliminarTexto" class="text-xs sm:text-sm text-gray-600 mt-1">Esta acción no se puede deshacer.</p>
        </div>
      </div>
      <button type="button" class="btn-cerrar-modal text-gray-400 hover:text-gray-600 p-1 rounded-lg transition-colors" aria-label="Cerrar modal">
        <i class="ti ti-x text-lg" aria-hidden="true"></i>
      </button>
    </div>
    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 mt-6 border-t border-gray-100 pt-4">
      <button type="button" id="btnCancelarEliminar" class="btn btn-secondary w-full sm:w-auto">Cancelar</button>
      <button type="button" id="btnConfirmarEliminar" class="btn btn-danger w-full sm:w-auto">Eliminar</button>
    </div>
  </div>
</div>

<!-- Modal para Ajuste Masivo de Precios (Estilo Mercado Libre) -->
<div id="modalAjustarPrecios" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto hidden" role="dialog" aria-modal="true" aria-labelledby="modalAjustarTitulo">
  <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-gray-200 p-6 sm:p-8 relative my-auto">
    <form action="<?= url('productos/ajustarPreciosMasivo') ?>" method="POST" id="formAjustePreciosModal" class="flex flex-col gap-4">
      <input type="hidden" name="ids" id="inputIdsAjusteModal" value="[]">
      
      <div class="flex items-start justify-between gap-3 border-b border-gray-100 pb-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-olive/10 text-olive flex items-center justify-center shrink-0">
            <i class="ti ti-percentage text-xl" aria-hidden="true"></i>
          </div>
          <div>
            <h3 id="modalAjustarTitulo" class="text-base sm:text-lg font-bold text-gray-900">Ajuste Masivo de Precios</h3>
            <p class="text-xs sm:text-sm text-gray-600 mt-0.5">Afectará a <span class="countSeleccionados font-bold text-olive">0</span> producto(s) seleccionado(s).</p>
          </div>
        </div>
        <button type="button" id="btnCerrarAjusteModal" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg transition-colors" aria-label="Cerrar modal">
          <i class="ti ti-x text-lg" aria-hidden="true"></i>
        </button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="field sm:col-span-2">
          <label for="campoAjuste" class="label font-semibold">¿Qué campo deseas modificar?</label>
          <select id="campoAjuste" name="campo" class="select text-sm">
            <option value="costo" selected>Precio de Costo Neto (Recalcula venta con IVA y Ganancia)</option>
            <option value="precio">Precio de Venta Final Directamente</option>
          </select>
        </div>

        <div class="field">
          <label for="tipoAjuste" class="label font-semibold">Operación</label>
          <select id="tipoAjuste" name="tipo" class="select text-sm">
            <option value="aumentar" selected>Aumentar (+)</option>
            <option value="disminuir">Disminuir (-)</option>
          </select>
        </div>

        <div class="field">
          <label for="modoAjuste" class="label font-semibold">Modalidad de ajuste</label>
          <select id="modoAjuste" name="modo" class="select text-sm">
            <option value="porcentaje" selected>Porcentaje (%)</option>
            <option value="monto">Monto Fijo ($ USD)</option>
          </select>
        </div>

        <div class="field sm:col-span-2">
          <label for="valorAjuste" class="label font-semibold">Valor del ajuste <span class="req" aria-hidden="true">*</span></label>
          <div class="relative flex items-center">
            <input type="number" step="0.01" min="0.01" id="valorAjuste" name="valor" class="input input--num font-bold text-base pr-10" placeholder="Ej: 10" required autocomplete="off">
            <span id="unidadValorAjuste" class="absolute right-3 text-xs font-bold text-gray-400 pointer-events-none">%</span>
          </div>
        </div>
      </div>

      <!-- Resumen explicativo -->
      <div class="p-3.5 bg-emerald-50/80 border border-emerald-200/80 rounded-xl text-xs text-emerald-950 flex items-start gap-2.5">
        <i class="ti ti-info-circle text-emerald-600 text-base shrink-0 mt-0.5" aria-hidden="true"></i>
        <span id="explicacionAjuste" class="leading-relaxed">Se aumentará un 10% el costo neto y se recalculará el precio final con IVA y ganancia.</span>
      </div>

      <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 mt-2 border-t border-gray-100 pt-4">
        <button type="button" id="btnCancelarAjusteModal" class="btn btn-secondary w-full sm:w-auto">Cancelar</button>
        <button type="submit" class="btn btn-primary w-full sm:w-auto">Aplicar ajuste</button>
      </div>
    </form>
  </div>
</div>

<?php if (!empty($productos)): ?>
<script>
  (function () {
    var filtro = document.getElementById('filtroInventario');
    var cuerpo = document.getElementById('cuerpoInventario');
    var vacio = document.getElementById('filaSinResultados');
    var conteo = document.getElementById('conteoInventario');
    
    // Checkboxes y selección masiva
    var selectAll = document.getElementById('selectAllProductos');
    var barSeleccion = document.getElementById('formSeleccionMasiva');
    var inputIdsMasivo = document.getElementById('inputIdsMasivo');
    var inputIdsAjusteModal = document.getElementById('inputIdsAjusteModal');
    var btnEliminarMasivo = document.getElementById('btnEliminarMasivo');
    var btnAjustarPreciosMasivo = document.getElementById('btnAjustarPreciosMasivo');
    var elementosCount = document.querySelectorAll('.countSeleccionados');

    // Modal Eliminar
    var modalEliminar = document.getElementById('modalConfirmarEliminar');
    var modalEliminarTitulo = document.getElementById('modalEliminarTitulo');
    var modalEliminarTexto = document.getElementById('modalEliminarTexto');
    var btnCancelarEliminar = document.getElementById('btnCancelarEliminar');
    var btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    
    // Modal Ajuste Precios
    var modalAjustar = document.getElementById('modalAjustarPrecios');
    var btnCancelarAjusteModal = document.getElementById('btnCancelarAjusteModal');
    var campoAjuste = document.getElementById('campoAjuste');
    var tipoAjuste = document.getElementById('tipoAjuste');
    var modoAjuste = document.getElementById('modoAjuste');
    var valorAjuste = document.getElementById('valorAjuste');
    var explicacionAjuste = document.getElementById('explicacionAjuste');

    var accionPendiente = null;

    function getCheckboxes() {
      return cuerpo ? Array.prototype.slice.call(cuerpo.querySelectorAll('.select-producto')) : [];
    }

    function actualizarSeleccion() {
      var checkboxes = getCheckboxes().filter(function(cb) {
        return !cb.closest('tr').hidden;
      });
      var seleccionados = checkboxes.filter(function(cb) { return cb.checked; });
      var ids = seleccionados.map(function(cb) { return Number(cb.value); });

      elementosCount.forEach(function(el) { el.textContent = ids.length; });
      var idsJson = JSON.stringify(ids);
      if (inputIdsMasivo) inputIdsMasivo.value = idsJson;
      if (inputIdsAjusteModal) inputIdsAjusteModal.value = idsJson;

      if (barSeleccion) {
        if (ids.length > 0) {
          barSeleccion.classList.remove('hidden');
          barSeleccion.classList.add('flex');
        } else {
          barSeleccion.classList.add('hidden');
          barSeleccion.classList.remove('flex');
        }
      }

      if (selectAll) {
        selectAll.checked = checkboxes.length > 0 && seleccionados.length === checkboxes.length;
        selectAll.indeterminate = seleccionados.length > 0 && seleccionados.length < checkboxes.length;
      }
    }

    if (selectAll) {
      selectAll.addEventListener('change', function () {
        var estado = selectAll.checked;
        getCheckboxes().forEach(function(cb) {
          if (!cb.closest('tr').hidden) {
            cb.checked = estado;
          }
        });
        actualizarSeleccion();
      });
    }

    if (cuerpo) {
      cuerpo.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('select-producto')) {
          actualizarSeleccion();
        }
      });
    }

    // Modal de confirmación eliminación
    function abrirModalEliminar(titulo, texto, onConfirm) {
      modalEliminarTitulo.textContent = titulo;
      modalEliminarTexto.textContent = texto;
      accionPendiente = onConfirm;
      modalEliminar.classList.remove('hidden');
    }

    function cerrarModalEliminar() {
      modalEliminar.classList.add('hidden');
      accionPendiente = null;
    }

    if (btnCancelarEliminar) btnCancelarEliminar.addEventListener('click', cerrarModalEliminar);
    if (btnConfirmarEliminar) {
      btnConfirmarEliminar.addEventListener('click', function () {
        if (typeof accionPendiente === 'function') {
          accionPendiente();
        }
        cerrarModalEliminar();
      });
    }

    // Evento botón eliminar masivo
    if (btnEliminarMasivo) {
      btnEliminarMasivo.addEventListener('click', function () {
        var ids = JSON.parse(inputIdsMasivo.value || '[]');
        if (ids.length === 0) return;

        abrirModalEliminar(
          '¿Eliminar ' + ids.length + ' producto(s)?',
          'Esta acción eliminará de forma permanente los ' + ids.length + ' productos seleccionados. ¿Deseas continuar?',
          function () {
            document.getElementById('formEliminarMasivo').submit();
          }
        );
      });
    }

    // Evento botones eliminar individual
    if (cuerpo) {
      cuerpo.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-eliminar-uno');
        if (!btn) return;

        var id = btn.dataset.id;
        var nombre = btn.dataset.nombre;

        abrirModalEliminar(
          '¿Eliminar ' + nombre + '?',
          'Se eliminará el producto "' + nombre + '" del inventario de forma permanente.',
          function () {
            window.location.href = '<?= url("productos/eliminar/") ?>' + id;
          }
        );
      });
    }

    // Modal Ajuste de Precios
    var btnCerrarAjusteModal = document.getElementById('btnCerrarAjusteModal');
    var unidadValorAjuste = document.getElementById('unidadValorAjuste');

    function actualizarExplicacion() {
      if (!explicacionAjuste) return;
      var c = campoAjuste ? campoAjuste.value : 'costo';
      var t = tipoAjuste ? tipoAjuste.value : 'aumentar';
      var m = modoAjuste ? modoAjuste.value : 'porcentaje';
      var v = valorAjuste && valorAjuste.value ? parseFloat(valorAjuste.value) : 10;

      if (unidadValorAjuste) {
        unidadValorAjuste.textContent = m === 'porcentaje' ? '%' : '$';
      }

      var operacionText = t === 'aumentar' ? 'aumentará' : 'disminuirá';
      var simbolo = m === 'porcentaje' ? '%' : '$ USD';
      
      if (c === 'costo') {
        explicacionAjuste.textContent = 'Se ' + operacionText + ' un ' + v + simbolo + ' el costo neto. El precio de venta final se recalculará automáticamente manteniendo el % de ganancia e IVA.';
      } else {
        explicacionAjuste.textContent = 'Se ' + operacionText + ' un ' + v + simbolo + ' el precio de venta final de forma directa.';
      }
    }

    if (btnAjustarPreciosMasivo) {
      btnAjustarPreciosMasivo.addEventListener('click', function () {
        var ids = JSON.parse(inputIdsMasivo.value || '[]');
        if (ids.length === 0) return;
        actualizarExplicacion();
        modalAjustar.classList.remove('hidden');
      });
    }

    if (btnCancelarAjusteModal) {
      btnCancelarAjusteModal.addEventListener('click', function () {
        modalAjustar.classList.add('hidden');
      });
    }

    if (btnCerrarAjusteModal) {
      btnCerrarAjusteModal.addEventListener('click', function () {
        modalAjustar.classList.add('hidden');
      });
    }

    // Botones genéricos de cerrar modal por clase
    document.querySelectorAll('.btn-cerrar-modal').forEach(function(btn) {
      btn.addEventListener('click', function() {
        cerrarModalEliminar();
      });
    });

    // Cerrar al hacer clic en el backdrop
    [modalEliminar, modalAjustar].forEach(function(m) {
      if (!m) return;
      m.addEventListener('click', function(e) {
        if (e.target === m) {
          m.classList.add('hidden');
          if (m === modalEliminar) accionPendiente = null;
        }
      });
    });

    // Cerrar con la tecla ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        if (modalEliminar) modalEliminar.classList.add('hidden');
        if (modalAjustar) modalAjustar.classList.add('hidden');
        accionPendiente = null;
      }
    });

    if (campoAjuste) campoAjuste.addEventListener('change', actualizarExplicacion);
    if (tipoAjuste) tipoAjuste.addEventListener('change', actualizarExplicacion);
    if (modoAjuste) modoAjuste.addEventListener('change', actualizarExplicacion);
    if (valorAjuste) valorAjuste.addEventListener('input', actualizarExplicacion);

    // Filtro de búsqueda
    if (filtro) {
      filtro.addEventListener('input', function () {
        var q = filtro.value.trim().toLowerCase();
        var visibles = 0;

        Array.prototype.forEach.call(cuerpo.querySelectorAll('tr[data-buscar]'), function (fila) {
          var coincide = !q || fila.dataset.buscar.indexOf(q) !== -1;
          fila.hidden = !coincide;
          if (coincide) visibles++;
        });

        if (vacio) vacio.hidden = visibles > 0;
        if (conteo) conteo.textContent = visibles === 1 ? '1 producto' : visibles + ' productos';
        actualizarSeleccion();
      });
    }
  })();
</script>
<?php endif; ?>

<?php include RUTA_APP . '/includes/footer.php'; ?>
