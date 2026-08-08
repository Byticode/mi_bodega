<?php
$page_title = 'Editar proveedor';
include ruta . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-xl mx-auto space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Editar proveedor</h2>
          <p class="page-sub">
            <a href="<?= url('proveedores') ?>" class="text-olive hover:underline">Proveedores</a>
            <span class="text-ink-3"> / <?= htmlspecialchars($dato[0]['proveedor_nombre']) ?></span>
          </p>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <!-- FORMULARIO -->
      <div class="card p-5">
        <form action="<?= url('proveedores/editar/' . $dato[0]['proveedor_id']) ?>" method="POST" class="space-y-4">

          <div>
            <label for="nombre" class="label">Nombre comercial</label>
            <input type="text" id="nombre" name="nombre" class="input" required
              value="<?= htmlspecialchars($dato[0]['proveedor_nombre']) ?>">
          </div>

          <div>
            <label for="telefono" class="label">Teléfono</label>
            <input type="text" id="telefono" name="telefono" class="input"
              value="<?= htmlspecialchars($dato[0]['proveedor_telefono']) ?>">
          </div>

          <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="<?= url('proveedores') ?>" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
