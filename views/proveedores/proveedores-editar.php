<?php
$page_title = 'Editar proveedor';
$page_desc  = 'Actualiza los datos de un proveedor.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

$proveedor = $dato[0] ?? $proveedor ?? [];
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--narrow">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <nav class="breadcrumb" aria-label="Ruta de navegación">
          <a href="<?= url('proveedores') ?>">Proveedores</a>
          <span aria-hidden="true">/</span>
          <span><?= htmlspecialchars($proveedor['proveedor_nombre'] ?? '') ?></span>
        </nav>
        <h1 class="page-title">Editar proveedor</h1>
      </div>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <form action="<?= url('proveedores/editar/' . ($proveedor['proveedor_id'] ?? 0)) ?>" method="POST" class="card p-5 flex flex-col gap-4">

      <div class="field">
        <label for="nombre" class="label">Nombre comercial <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="nombre" name="nombre" class="input" required autocomplete="organization"
          value="<?= htmlspecialchars($proveedor['proveedor_nombre'] ?? '') ?>">
      </div>

      <div class="field">
        <label for="telefono" class="label">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" class="input" autocomplete="tel"
          value="<?= htmlspecialchars($proveedor['proveedor_telefono'] ?? '') ?>">
      </div>

      <div class="flex flex-wrap items-center justify-end gap-3 pt-3 border-t border-rule">
        <a href="<?= url('proveedores') ?>" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>

  </div>
</main>

<?php include RUTA_APP . '/includes/footer.php'; ?>
