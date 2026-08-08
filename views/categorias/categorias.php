<?php
$page_title = 'Categorías';
$page_desc  = 'Agrupa tu mercancía para encontrarla más rápido.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--narrow">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Categorías</h1>
        <p class="page-sub">Agrupa tu mercancía para encontrarla más rápido.</p>
      </div>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <!-- Registro -->
    <div class="card p-5 flex flex-col gap-4">
      <h2 class="section-title">Nueva categoría</h2>
      <form class="flex flex-col sm:flex-row sm:items-end gap-3" action="<?= url('categorias/crear') ?>" method="POST">
        <div class="field flex-1">
          <label for="nombre" class="label">Nombre <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="nombre" name="nombre" class="input" placeholder="Ej: Víveres" required autocomplete="off">
        </div>
        <button type="submit" class="btn btn-primary">Agregar</button>
      </form>
    </div>

    <!-- Tabla -->
    <div class="card overflow-hidden">
      <div class="card-head">
        <h2 class="section-title">Categorías registradas</h2>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Categorías registradas</caption>
          <thead>
            <tr>
              <th scope="col">Nombre</th>
              <th scope="col" class="col-actions">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($categorias)): ?>
              <tr>
                <td colspan="2">
                  <div class="empty">
                    <p class="empty-title">No hay categorías todavía</p>
                    <p class="empty-sub">Crea la primera con el formulario de arriba.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($categorias as $categoria): ?>
                <tr>
                  <td class="font-medium"><?= htmlspecialchars($categoria['categorias_nombre']) ?></td>
                  <td class="col-actions">
                    <a href="<?= url('categorias/editar/' . $categoria['categorias_id']) ?>"
                       class="btn-icon" aria-label="Editar la categoría <?= htmlspecialchars($categoria['categorias_nombre'], ENT_QUOTES) ?>">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<?php include RUTA_APP . '/includes/footer.php'; ?>
