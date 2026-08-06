<?php
$page_title = 'Surtido';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-5xl space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Surtido</h2>
          <p class="page-sub">Entradas de mercancía y reabastecimiento de inventario.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <input type="text" placeholder="Buscar mercancía..." class="input w-64">
          <button type="button" class="btn btn-secondary">Filtrar</button>
          <button type="button" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Registrar Surtido
          </button>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <!-- TARJETAS DE RESUMEN -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="card bg-card-2 p-4">
          <span class="text-xs font-semibold text-ink-3 uppercase tracking-wider">Último reabastecimiento</span>
          <p class="text-lg font-semibold text-ink mt-1">Hoy, 10:30 AM</p>
        </div>
        <div class="card bg-card-2 p-4">
          <span class="text-xs font-semibold text-ink-3 uppercase tracking-wider">Items recibidos (mes)</span>
          <p class="text-lg font-semibold text-ink tnum mt-1">450 Unidades</p>
        </div>
      </div>

      <!-- TABLA -->
      <div class="card overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Proveedor</th>
              <th>Productos</th>
              <th class="text-right">Unid. Entrante</th>
              <th class="text-right">Total Compra</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-ink-2">03/08/2026</td>
              <td class="font-medium">Distribuidora Polaris</td>
              <td><span class="badge badge-neutral">2 productos</span></td>
              <td class="text-right"><span class="badge badge-success">+50</span></td>
              <td class="text-right money">Bs 1.050,00</td>
            </tr>
            <tr>
              <td class="text-ink-2">01/08/2026</td>
              <td class="font-medium">Alimentos del Centro</td>
              <td><span class="badge badge-neutral">4 productos</span></td>
              <td class="text-right"><span class="badge badge-success">+24</span></td>
              <td class="text-right money">Bs 1.440,00</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
