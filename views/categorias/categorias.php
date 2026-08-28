<?php
$page_title = 'Categorías';
$page_desc = 'Agrupa tu mercancía para encontrarla más rápido.';
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
          <input type="text" id="nombre" name="nombre" class="input" placeholder="Ej: Víveres" required
            autocomplete="off">
        </div>
        <button type="submit" class="btn btn-primary">Agregar</button>
      </form>
    </div>

    <!-- Tabla -->
    <div class="card overflow-hidden">
      <div class="card-head flex-wrap gap-3">
        <h2 class="section-title">Categorías registradas</h2>
        <form method="GET" action="<?= url('categorias') ?>"
          class="search w-full sm:w-64 ml-auto relative flex items-center">
          <label for="filtroCategorias" class="sr-only">Buscar categoría</label>
          <i class="ti ti-search search-icon" aria-hidden="true"></i>
          <input type="text" id="filtroCategorias" name="q" value="<?= htmlspecialchars($search ?? '') ?>"
            class="input pl-9 pr-8" placeholder="Buscar categoría…" autocomplete="off">
          <button type="button"
            class="btn-clear-search absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-3 hover:text-ink hover:bg-card-2 p-1 rounded-full flex items-center justify-center transition-colors"
            title="Limpiar búsqueda" aria-label="Limpiar búsqueda" <?= empty($search) ? 'style="display:none;"' : '' ?>>
            <i class="ti ti-x text-xs cursor-pointer text-red-500"></i>
          </button>
        </form>
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
                  <?php if (!empty($search)): ?>
                    <div class="empty">
                      <i class="ti ti-search-off empty-icon" aria-hidden="true"></i>
                      <p class="empty-title">Sin resultados para «<?= htmlspecialchars($search) ?>»</p>
                      <p class="empty-sub">Intenta con otro término o limpia el buscador.</p>
                      <a href="<?= url('categorias') ?>" class="btn btn-secondary btn-sm mt-2">Ver todas</a>
                    </div>
                  <?php else: ?>
                    <div class="empty">
                      <p class="empty-title">No hay categorías todavía</p>
                      <p class="empty-sub">Crea la primera con el formulario de arriba.</p>
                    </div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($categorias as $categoria): ?>
                <tr>
                  <td class="font-medium"><?= htmlspecialchars($categoria['categorias_nombre']) ?></td>
                  <td class="col-actions">
                    <a href="<?= url('categorias/editar/' . $categoria['categorias_id']) ?>" class="btn-icon"
                      aria-label="Editar la categoría <?= htmlspecialchars($categoria['categorias_nombre'], ENT_QUOTES) ?>">
                      <i class="ti ti-pencil text-base" aria-hidden="true"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php
      $label_items = 'categorías';
      $ruta_paginacion = 'categorias';
      include RUTA_APP . '/includes/paginacion.php';
      ?>
    </div>

  </div>
</main>

<?php include RUTA_APP . '/includes/footer.php'; ?>