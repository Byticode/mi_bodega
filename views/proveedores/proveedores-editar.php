<?php
$page_title = 'Editar proveedor';
$page_desc  = 'Actualiza los datos de un proveedor.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';

$proveedor = $dato[0];
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--narrow">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <nav class="breadcrumb" aria-label="Ruta de navegación">
          <a href="index.php?controller=proveedoresController&action=listar">Proveedores</a>
          <span aria-hidden="true">/</span>
          <span><?= htmlspecialchars($proveedor['proveedor_nombre']) ?></span>
        </nav>
        <h1 class="page-title">Editar proveedor</h1>
      </div>
    </div>

    <?php include ruta . '/includes/flash.php'; ?>

    <form action="index.php?controller=proveedoresController&action=editar&id=<?= (int) $proveedor['proveedor_id'] ?>" method="POST" class="card p-5 flex flex-col gap-4">

      <div class="field">
        <label for="nombre" class="label">Nombre comercial <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="nombre" name="nombre" class="input" required autocomplete="organization"
          value="<?= htmlspecialchars($proveedor['proveedor_nombre']) ?>">
      </div>

      <div class="field">
        <label for="telefono" class="label">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" class="input" autocomplete="tel"
          value="<?= htmlspecialchars($proveedor['proveedor_telefono'] ?? '') ?>">
      </div>

      <div class="flex flex-wrap items-center justify-end gap-3 pt-3 border-t border-rule">
        <a href="index.php?controller=proveedoresController&action=listar" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>

  </div>
</main>

<?php include ruta . '/includes/footer.php'; ?>
