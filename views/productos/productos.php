<?php
$page_title = 'Inventario';
$page_desc  = 'Catálogo de productos, precios y stock disponible.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

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
      <a href="<?= url('productos/crear') ?>" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Nuevo producto
      </a>
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
        <span class="stat-value stat-value--money stat-value--accent"><?= money($valor) ?></span>
        <span class="stat-note">A precio de venta</span>
      </div>
    </div>

    <!-- Tabla -->
    <div class="card overflow-hidden">
      <div class="card-head">
        <h2 class="section-title">Productos registrados</h2>
        <?php if (!empty($productos)): ?>
          <div class="search w-full sm:w-64">
            <label for="filtroInventario" class="sr-only">Filtrar productos por nombre, código o categoría</label>
            <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
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
                    <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
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
                  <td class="num money text-olive"><?= money($producto['producto_precio_venta']) ?></td>
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
                    <a href="<?= url('productos/editar/' . $producto['producto_id']) ?>"
                       class="btn-icon" aria-label="Editar <?= htmlspecialchars($producto['producto_nombre'], ENT_QUOTES) ?>">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
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

      if (vacio) vacio.hidden = visibles > 0;
      if (conteo) conteo.textContent = visibles === 1 ? '1 producto' : visibles + ' productos';
    });
  })();
</script>
<?php endif; ?>

<?php include RUTA_APP . '/includes/footer.php'; ?>
