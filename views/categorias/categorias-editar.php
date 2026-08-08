<?php
$page_title = 'Editar categoría';
include ruta . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-xl mx-auto space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Editar categoría</h2>
          <p class="page-sub">
            <a href="<?= url('categorias') ?>" class="text-olive hover:underline">Categorías</a>
            <span class="text-ink-3"> / <?= htmlspecialchars($categoria['categorias_nombre']) ?></span>
          </p>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <!-- FORMULARIO -->
      <div class="card p-5">
        <form action="<?= url('categorias/editar/' . $categoria['categorias_id']) ?>" method="POST" class="space-y-4">
          <input type="hidden" name="id" value="<?= $categoria['categorias_id'] ?>">

          <div>
            <label for="nombre" class="label">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="input" required
              value="<?= htmlspecialchars($categoria['categorias_nombre']) ?>">
          </div>

          <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="<?= url('categorias') ?>" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
