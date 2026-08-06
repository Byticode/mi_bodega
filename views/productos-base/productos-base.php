<?php
$page_title = 'Productos base';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-3xl space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Productos base</h2>
          <p class="page-sub">Catálogo general de artículos registrados.</p>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <!-- REGISTRO -->
      <div class="card p-5">
        <h3 class="text-sm font-semibold text-ink mb-4">Nuevo producto base</h3>
        <form class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3" onsubmit="event.preventDefault();">
          <input type="text" placeholder="Código de barras" class="input">
          <input type="text" placeholder="Nombre del producto" class="input">
          <select class="select">
            <option value="">Categoría...</option>
            <option>Abarrotes</option>
          </select>
          <button type="submit" class="btn btn-primary">Agregar</button>
        </form>
      </div>

      <!-- TABLA DE LISTADO -->
      <div class="card overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Código</th>
              <th>Nombre</th>
              <th>Categoría</th>
              <th class="text-right">Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="text-center text-ink-3">No hay registros todavía.</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
