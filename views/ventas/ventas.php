<?php
$page_title = 'Ventas';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-5xl space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Ventas</h2>
          <p class="page-sub">Movimientos de mostrador de los últimos días.</p>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <!-- TABLA DE VENTAS -->
      <div class="card overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>Productos</th>
              <th class="text-right">Total</th>
              <th>Método</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="6" class="text-center text-ink-3">No hay registros todavía.</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
