<?php
$page_title = 'Nuevo producto';
$page_desc  = 'Registra un producto nuevo en el inventario.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--mid">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <nav class="breadcrumb" aria-label="Ruta de navegación">
          <a href="<?= url('productos') ?>">Inventario</a>
          <span aria-hidden="true">/</span>
          <span>Nuevo producto</span>
        </nav>
        <h1 class="page-title">Nuevo producto</h1>
        <p class="page-sub">El nombre final se arma con el peso y la unidad que elijas.</p>
      </div>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <form action="<?= url('productos/crear') ?>" method="POST" class="card p-5 sm:p-6 flex flex-col gap-5">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="field md:col-span-2">
          <label for="nombre" class="label">Nombre del producto <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="nombre" name="nombre" class="input" placeholder="Ej: Refresco Fanta Toronja"
                 required minlength="3" autocomplete="off" autofocus>
          <p class="hint" id="nombre-hint">Mínimo 3 caracteres. No escribas aquí el peso: se agrega solo.</p>
        </div>

        <div class="field">
          <label for="codigo" class="label">Código de barras</label>
          <input type="text" id="codigo" name="codigo" class="input" placeholder="Opcional" autocomplete="off"
                 aria-describedby="codigo-hint">
          <p class="hint" id="codigo-hint">Si lo dejas vacío se genera automáticamente. Mínimo 5 caracteres.</p>
        </div>

        <div class="field">
          <label for="categoria" class="label">Categoría <span class="req" aria-hidden="true">*</span></label>
          <select id="categoria" name="categoria" class="select" required>
            <option value="">Seleccionar categoría…</option>
            <?php foreach ($categorias as $categoria): ?>
              <option value="<?= (int) $categoria['categorias_id'] ?>"><?= htmlspecialchars($categoria['categorias_nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="peso" class="label">Peso o contenido</label>
          <input type="number" step="0.01" min="0" id="peso" name="peso" class="input input--num" placeholder="Ej: 2"
                 autocomplete="off" aria-describedby="peso-hint">
          <p class="hint" id="peso-hint">Opcional. Se añade al nombre junto a la unidad.</p>
        </div>

        <div class="field">
          <label for="unidad" class="label">Unidad de medida <span class="req" aria-hidden="true">*</span></label>
          <select id="unidad" name="unidad" class="select" required>
            <option value="">Seleccionar unidad…</option>
            <?php foreach ($unidades as $unidad): ?>
              <option value="<?= (int) $unidad['unidad_id'] ?>"><?= htmlspecialchars($unidad['unidad_nombre']) ?> (<?= htmlspecialchars($unidad['unidad_abreviatura']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="precio" class="label">Precio de venta <span class="req" aria-hidden="true">*</span></label>
          <input type="number" step="0.01" min="0.01" id="precio" name="precio" class="input input--num"
                 placeholder="0.00" required autocomplete="off">
        </div>

        <!-- <div class="field">
          <label for="stock" class="label">Stock inicial</label>
          <input type="number" step="1" min="0" id="stock" name="stock" class="input input--num" value="0" autocomplete="off">
        </div> -->
      </div>

      <!-- Vista previa del nombre final -->
      <div class="stat">
        <span class="stat-label">Así se guardará</span>
        <span class="stat-value" id="previewNombre" role="status" aria-live="polite">—</span>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-3 pt-1 border-t border-rule">
        <a href="<?= url('productos') ?>" class="btn btn-secondary mt-4">Cancelar</a>
        <button type="submit" class="btn btn-primary mt-4">Guardar producto</button>
      </div>
    </form>

  </div>
</main>

<script src="<?= assets('scripts/producto-preview.js') ?>"></script>

<?php include RUTA_APP . '/includes/footer.php'; ?>
