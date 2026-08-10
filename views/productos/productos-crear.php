<?php
$page_title = 'Nuevo producto';
$page_desc  = 'Registra un producto nuevo en el inventario.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

$tasa_act = tasa_vigente()['tasa_usd'] ?? 0;
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
        <p class="page-sub">El precio base se establece en Dólares ($) y se calcula su equivalente en Bolívares (Bs).</p>
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
          <p class="hint" id="codigo-hint">Opcional. Mínimo 5 caracteres.</p>
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

        <!-- Campos de estructura de precios -->
        <div class="field">
          <label for="precio_costo" class="label">Precio de Costo ($ USD)</label>
          <input type="number" step="0.01" min="0" id="precio_costo" name="precio_costo" class="input input--num"
                 placeholder="0.00" autocomplete="off">
          <p class="hint">Costo de compra por unidad en USD.</p>
        </div>

        <div class="field">
          <label for="ganancia" class="label">% Ganancia (Mínimo 30%)</label>
          <input type="number" step="0.01" min="0" id="ganancia" name="ganancia" class="input input--num"
                 value="30.00" autocomplete="off">
          <p class="hint">Margen de ganancia sugerido (30% mínimo).</p>
        </div>

        <div class="field">
          <label for="iva" class="label">% IVA (Impuesto)</label>
          <input type="number" step="0.01" min="0" id="iva" name="iva" class="input input--num"
                 value="16.00" autocomplete="off">
          <p class="hint">Aplicar IVA correspondiente (Ej: 16%).</p>
        </div>

        <div class="field">
          <label for="precio" class="label">Precio de Venta ($ USD) <span class="req" aria-hidden="true">*</span></label>
          <input type="number" step="0.01" min="0.01" id="precio" name="precio" class="input input--num font-bold text-olive"
                 placeholder="0.00" required autocomplete="off">
          <p class="hint" id="conversionBsHint">≈ Bs 0,00 (Tasa BCV: Bs <?= money($tasa_act, '') ?>/$)</p>
        </div>
      </div>

      <!-- Resumen / Vista previa -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
        <div class="stat">
          <span class="stat-label">Así se guardará el nombre</span>
          <span class="stat-value text-base font-semibold" id="previewNombre" role="status" aria-live="polite">—</span>
        </div>
        <div class="stat">
          <span class="stat-label">Conversión a Bolívares</span>
          <span class="stat-value text-base font-semibold stat-value--accent" id="previewBs" role="status" aria-live="polite">Bs 0,00</span>
        </div>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-3 pt-3 border-t border-rule">
        <a href="<?= url('productos') ?>" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar producto</button>
      </div>
    </form>

  </div>
</main>

<script>
  window.TASA_USD = <?= json_encode($tasa_act) ?>;
</script>
<script src="<?= assets('scripts/producto-preview.js') ?>"></script>

<?php include RUTA_APP . '/includes/footer.php'; ?>
