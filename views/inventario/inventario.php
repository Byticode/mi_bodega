<?php
$page_title = 'Inventario';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-4xl space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Inventario</h2>
          <p class="page-sub">Catálogo general de productos y stock disponible.</p>
        </div>
        <div class="flex items-center gap-2">
          <input type="text" placeholder="Buscar mercancía..." class="input w-64">
          <button type="button" class="btn btn-secondary">Filtrar</button>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <!-- TARJETAS DE RESUMEN -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card bg-card-2 p-4">
          <span class="text-xs font-semibold text-ink-3 uppercase tracking-wider">Total productos</span>
          <p class="text-lg font-semibold text-ink tnum mt-1">124</p>
        </div>
        <div class="card bg-card-2 p-4">
          <span class="text-xs font-semibold text-ink-3 uppercase tracking-wider">Productos bajo stock</span>
          <p class="text-lg font-semibold text-ink tnum mt-1">3</p>
        </div>
        <div class="card bg-card-2 p-4">
          <span class="text-xs font-semibold text-ink-3 uppercase tracking-wider">Valor del inventario</span>
          <p class="text-lg font-semibold text-ink money mt-1">Bs 14.250,00</p>
        </div>
      </div>

      <!-- TABLA -->
      <div class="card overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Categoría</th>
              <th class="text-right">Stock</th>
              <th class="text-right">P. Unitario</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="font-medium">Harina de maíz 1 kg</td>
              <td class="text-ink-2">Víveres</td>
              <td class="text-right">42</td>
              <td class="text-right money">Bs 28,50</td>
            </tr>
            <tr>
              <td class="font-medium">Arroz blanco 1 kg</td>
              <td class="text-ink-2">Víveres</td>
              <td class="text-right">18</td>
              <td class="text-right money">Bs 32,00</td>
            </tr>
            <tr>
              <td class="font-medium">Café molido 500 g</td>
              <td class="text-ink-2">Víveres</td>
              <td class="text-right">4</td>
              <td class="text-right money">Bs 95,00</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
