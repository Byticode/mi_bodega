<?php
$page_title = 'Categorías';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-3xl mx-auto space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Categorías</h2>
          <p class="page-sub">Agrupa tu mercancía para encontrarla más rápido.</p>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <!-- REGISTRO -->
      <div class="card p-5">
        <h3 class="text-sm font-semibold text-ink mb-4">Nueva categoría</h3>
        <form class="flex flex-col sm:flex-row gap-3" action="index.php?controller=categoriasController&action=crear" method="POST">
          <input type="text" name="nombre" placeholder="Nombre de la categoría" class="input flex-1" required>
          <button type="submit" class="btn btn-primary">Agregar</button>
        </form>
      </div>

      <!-- TABLA -->
      <div class="card overflow-hidden">
        <table class="table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th class="text-right">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categorias as $categoria): ?>
              <tr>
                <td class="font-medium"><?= htmlspecialchars($categoria['categorias_nombre']) ?></td>
                <td class="text-right">
                  <a href="index.php?controller=categoriasController&action=editar&id=<?= $categoria['categorias_id'] ?>" class="btn-icon" aria-label="Editar categoría">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
