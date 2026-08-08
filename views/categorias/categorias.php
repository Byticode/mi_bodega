<?php
$page_title = 'Categorías';
$page_desc  = 'Agrupa tu mercancía para encontrarla más rápido.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
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

    <?php include ruta . '/includes/flash.php'; ?>

    <!-- Registro -->
    <div class="card p-5 flex flex-col gap-4">
      <h2 class="section-title">Nueva categoría</h2>
      <form class="flex flex-col sm:flex-row sm:items-end gap-3" action="index.php?controller=categoriasController&action=crear" method="POST">
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
                    <a href="index.php?controller=categoriasController&action=editar&id=<?= (int) $categoria['categorias_id'] ?>"
                       class="btn-icon" aria-label="Editar la categoría <?= htmlspecialchars($categoria['categorias_nombre'], ENT_QUOTES) ?>">
                      <i class="ti ti-pencil text-base" aria-hidden="true"></i>
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

<?php include ruta . '/includes/footer.php'; ?>
