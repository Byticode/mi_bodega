<?php
$page_title = 'Editar producto';
$page_desc  = 'Actualiza los datos de un producto del inventario.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

$producto = $dato[0] ?? $producto ?? [];
$nombre_base = preg_replace('/\s+\d+\.?\d*[A-Za-z]+$/', '', $producto['producto_nombre'] ?? '');
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--mid">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <nav class="breadcrumb" aria-label="Ruta de navegación">
          <a href="<?= url('productos') ?>">Inventario</a>
          <span aria-hidden="true">/</span>
          <span><?= htmlspecialchars($producto['producto_nombre'] ?? '') ?></span>
        </nav>
        <h1 class="page-title">Editar producto</h1>
        <p class="page-sub">El nombre final se arma con el peso y la unidad que elijas.</p>
      </div>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <form action="<?= url('productos/editar/' . $producto['producto_id']) ?>" method="POST" class="card p-5 sm:p-6 flex flex-col gap-5">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="field md:col-span-2">
          <label for="nombre" class="label">Nombre del producto <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="nombre" name="nombre" class="input" required minlength="3" autocomplete="off"
                 value="<?= htmlspecialchars($nombre_base) ?>" aria-describedby="nombre-hint">
          <p class="hint" id="nombre-hint">Mínimo 3 caracteres. No escribas aquí el peso: se agrega solo.</p>
        </div>

        <div class="field">
          <label for="codigo" class="label">Código de barras</label>
          <input type="text" id="codigo" name="codigo" class="input" autocomplete="off"
                 value="<?= htmlspecialchars($producto['producto_codigo'] ?? '') ?>" aria-describedby="codigo-hint">
          <p class="hint" id="codigo-hint">Opcional. Mínimo 5 caracteres.</p>
        </div>

        <div class="field">
          <label for="categoria" class="label">Categoría <span class="req" aria-hidden="true">*</span></label>
          <select id="categoria" name="categoria" class="select" required>
            <option value="">Seleccionar categoría…</option>
            <?php foreach ($categorias as $categoria): ?>
              <option value="<?= (int) $categoria['categorias_id'] ?>" <?= $categoria['categorias_id'] == ($producto['categoria_id'] ?? 0) ? 'selected' : '' ?>>
                <?= htmlspecialchars($categoria['categorias_nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="peso" class="label">Peso o contenido</label>
          <input type="number" step="0.01" min="0" id="peso" name="peso" class="input input--num" autocomplete="off"
                 value="<?= htmlspecialchars($producto['producto_peso'] ?? '') ?>">
        </div>

        <div class="field">
          <label for="unidad" class="label">Unidad de medida <span class="req" aria-hidden="true">*</span></label>
          <select id="unidad" name="unidad" class="select" required>
            <option value="">Seleccionar unidad…</option>
            <?php foreach ($unidades as $unidad): ?>
              <option value="<?= (int) $unidad['unidad_id'] ?>" <?= $unidad['unidad_id'] == ($producto['unidad_id'] ?? 0) ? 'selected' : '' ?>>
                <?= htmlspecialchars($unidad['unidad_nombre']) ?> (<?= htmlspecialchars($unidad['unidad_abreviatura']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="precio" class="label">Precio de venta <span class="req" aria-hidden="true">*</span></label>
          <input type="number" step="0.01" min="0.01" id="precio" name="precio" class="input input--num" required
                 autocomplete="off" value="<?= htmlspecialchars($producto['producto_precio_venta'] ?? '0.00') ?>">
        </div>

        <div class="field">
          <label for="stock" class="label">Stock</label>
          <input type="number" step="1" min="0" id="stock" name="stock" class="input input--num" autocomplete="off"
                 value="<?= (int) ($producto['producto_stock'] ?? 0) ?>" aria-describedby="stock-hint">
          <p class="hint" id="stock-hint">Para reponer mercancía usa Surtido; así queda registrado el costo.</p>
        </div>
      </div>

      <!-- Vista previa del nombre final -->
      <div class="stat">
        <span class="stat-label">Así se guardará</span>
        <span class="stat-value" id="previewNombre" role="status" aria-live="polite"><?= htmlspecialchars($producto['producto_nombre'] ?? '—') ?></span>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-3 pt-1 border-t border-rule">
        <a href="<?= url('productos') ?>" class="btn btn-secondary mt-4">Cancelar</a>
        <button type="submit" class="btn btn-primary mt-4">Guardar cambios</button>
      </div>
    </form>

  </div>
</main>

<script src="<?= assets('scripts/producto-preview.js') ?>"></script>

<?php include RUTA_APP . '/includes/footer.php'; ?>
