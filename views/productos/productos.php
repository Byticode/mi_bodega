<?php
$page_title = 'Inventario';
$page_desc  = 'Catálogo de productos, precios y stock disponible.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';

$bajo_stock = array_filter($productos, fn($p) => $p['producto_stock'] > 0 && $p['producto_stock'] <= 10);
$agotados   = array_filter($productos, fn($p) => $p['producto_stock'] <= 0);
$valor      = array_sum(array_map(fn($p) => $p['producto_stock'] * $p['producto_precio_venta'], $productos));
?>

<main id="contenido" class="app-main">
  <div class="app-wrap">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Inventario</h1>
        <p class="page-sub">Catálogo de productos, precios y stock disponible.</p>
      </div>
      <a href="index.php?controller=productosController&action=crear" class="btn btn-primary">
        <i class="ti ti-plus text-base" aria-hidden="true"></i>
        Nuevo producto
      </a>
    </div>

    <?php include ruta . '/includes/flash.php'; ?>

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
        <span class="stat-value stat-value--money stat-value--accent"><?= money($valor) ?></span>
        <span class="stat-note"><?= usd($valor) ? 'A precio de venta · ' . usd($valor) : 'A precio de venta' ?></span>
      </div>
    </div>

    <!-- Tabla -->
    <div class="card overflow-hidden">
      <div class="card-head">
        <h2 class="section-title">Productos registrados</h2>
        <?php if (!empty($productos)): ?>
          <div class="search w-full sm:w-64">
            <label for="filtroInventario" class="sr-only">Filtrar productos por nombre, código o categoría</label>
            <i class="ti ti-search search-icon" aria-hidden="true"></i>
            <input type="search" id="filtroInventario" class="input" placeholder="Filtrar…" autocomplete="off">
          </div>
        <?php endif; ?>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Listado de productos con código, presentación, categoría, precio y stock</caption>
          <thead>
            <tr>
              <th scope="col">Código</th>
              <th scope="col">Producto</th>
              <th scope="col">Presentación</th>
              <th scope="col">Categoría</th>
              <th scope="col" class="num">Precio</th>
              <th scope="col" class="num">Stock</th>
              <th scope="col" class="col-actions">Acción</th>
            </tr>
          </thead>
          <tbody id="cuerpoInventario">
            <?php if (empty($productos)): ?>
              <tr>
                <td colspan="7">
                  <div class="empty">
                    <i class="ti ti-package empty-icon" aria-hidden="true"></i>
                    <p class="empty-title">No hay productos registrados</p>
                    <p class="empty-sub">Crea el primero para poder venderlo y surtirlo.</p>
                    <a href="index.php?controller=productosController&action=crear" class="btn btn-primary mt-3">Nuevo producto</a>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($productos as $producto):
                $stock = (int) $producto['producto_stock'];
                $categoria = $producto['categorias_nombre'] ?? 'Sin categoría';
              ?>
                <tr data-buscar="<?= htmlspecialchars(mb_strtolower($producto['producto_nombre'] . ' ' . ($producto['producto_codigo'] ?? '') . ' ' . $categoria), ENT_QUOTES) ?>">
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
                    <span class="money text-olive block"><?= money($producto['producto_precio_venta']) ?></span>
                    <?php if ($equiv = usd($producto['producto_precio_venta'])): ?>
                      <span class="text-xs text-ink-3 tnum"><?= $equiv ?></span>
                    <?php endif; ?>
                  </td>
                  <td class="num">
                    <?php if ($stock <= 0): ?>
                      <span class="badge badge-danger"><span class="badge-dot"></span>Agotado</span>
                    <?php elseif ($stock <= 10): ?>
                      <span class="badge badge-warn"><span class="badge-dot"></span><?= $stock ?></span>
                    <?php else: ?>
                      <?= $stock ?>
                    <?php endif; ?>
                  </td>
                  <td class="col-actions">
                    <a href="index.php?controller=productosController&action=editar&id=<?= (int) $producto['producto_id'] ?>"
                       class="btn-icon" aria-label="Editar <?= htmlspecialchars($producto['producto_nombre'], ENT_QUOTES) ?>">
                      <i class="ti ti-pencil text-base" aria-hidden="true"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <tr id="filaSinResultados" hidden>
                <td colspan="7">
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

<?php if (!empty($productos)): ?>
<script>
  (function () {
    var filtro = document.getElementById('filtroInventario');
    var cuerpo = document.getElementById('cuerpoInventario');
    var vacio = document.getElementById('filaSinResultados');
    var conteo = document.getElementById('conteoInventario');
    if (!filtro) return;

    filtro.addEventListener('input', function () {
      var q = filtro.value.trim().toLowerCase();
      var visibles = 0;

      Array.prototype.forEach.call(cuerpo.querySelectorAll('tr[data-buscar]'), function (fila) {
        var coincide = !q || fila.dataset.buscar.indexOf(q) !== -1;
        fila.hidden = !coincide;
        if (coincide) visibles++;
      });

      vacio.hidden = visibles > 0;
      conteo.textContent = visibles === 1 ? '1 producto' : visibles + ' productos';
    });
  })();
</script>
<?php endif; ?>

<?php include ruta . '/includes/footer.php'; ?>
