<?php
$page_title = 'Editar categoría';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main id="contenido" class="app-main">
    <div class="app-wrap app-wrap--narrow">

      <!-- Page header -->
      <div class="page-head">
        <div>
          <nav class="breadcrumb" aria-label="Ruta de navegación">
            <a href="<?= url('categorias') ?>">Categorías</a>
            <span aria-hidden="true">/</span>
            <span><?= htmlspecialchars($categoria['categorias_nombre']) ?></span>
          </nav>
          <h1 class="page-title">Editar categoría</h1>
        </div>
      </div>

      <?php include RUTA_APP . '/includes/flash.php'; ?>

      <!-- FORMULARIO -->
      <form action="<?= url('categorias/editar/' . $categoria['categorias_id']) ?>" method="POST" class="card p-5 flex flex-col gap-4">
        <input type="hidden" name="id" value="<?= (int) $categoria['categorias_id'] ?>">

        <div class="field">
          <label for="nombre" class="label">Nombre <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="nombre" name="nombre" class="input" required autocomplete="off"
            value="<?= htmlspecialchars($categoria['categorias_nombre']) ?>">
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3 pt-3 border-t border-rule">
          <a href="<?= url('categorias') ?>" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>

    </div>
  </main>

<?php include RUTA_APP . '/includes/footer.php'; ?>
