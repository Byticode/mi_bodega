<?php
$page_title = 'Clientes';
$page_desc = 'Datos de los clientes frecuentes de la bodega.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';
?>

<main id="contenido" class="app-main">
  <div class="app-wrap">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Clientes</h1>
        <p class="page-sub">Los clientes registrados aparecen como opción en el punto de venta.</p>
      </div>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <!-- Registro -->
    <div class="card p-5 flex flex-col gap-4">
      <h2 class="section-title">Nuevo cliente</h2>

      <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3" action="<?= url('clientes/crear') ?>"
        method="POST">
        <div class="field">
          <label for="nombre" class="label">Nombre <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="nombre" name="nombre" class="input" required autocomplete="given-name">
        </div>
        <div class="field">
          <label for="apellido" class="label">Apellido <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="apellido" name="apellido" class="input" required autocomplete="family-name">
        </div>
        <div class="field">
          <label for="cedula" class="label">Cédula o identificación <span class="req"
              aria-hidden="true">*</span></label>
          <input type="text" id="cedula" name="cedula" class="input" required autocomplete="off" inputmode="numeric">
        </div>
        <div class="field">
          <label for="telefono" class="label">Teléfono</label>
          <input type="tel" id="telefono" name="telefono" class="input" autocomplete="tel">
        </div>
        <div class="field">
          <label for="correo" class="label">Correo electrónico</label>
          <input type="email" id="correo" name="correo" class="input" autocomplete="email">
        </div>
        <div class="field justify-end">
          <button type="submit" class="btn btn-primary">Registrar cliente</button>
        </div>
      </form>
    </div>

    <!-- Tabla -->
    <div class="card overflow-hidden">
      <div class="card-head flex-wrap gap-3">
        <h2 class="section-title">Clientes registrados</h2>
        <form method="GET" action="<?= url('clientes') ?>"
          class="search w-full sm:w-64 ml-auto relative flex items-center">
          <label for="filtroClientes" class="sr-only">Buscar cliente</label>
          <i class="ti ti-search search-icon" aria-hidden="true"></i>
          <input type="text" id="filtroClientes" name="q" value="<?= htmlspecialchars($search ?? '') ?>"
            class="input pl-9 pr-8" placeholder="Buscar por nombre, cédula o tel…" autocomplete="off">
          <button type="button"
            class="btn-clear-search absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-3 hover:text-ink hover:bg-card-2 p-1 rounded-full flex items-center justify-center transition-colors"
            title="Limpiar búsqueda" aria-label="Limpiar búsqueda" <?= empty($search) ? 'style="display:none;"' : '' ?>>
            <i class="ti ti-x text-xs cursor-pointer text-red-500"></i>
          </button>
        </form>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Clientes registrados con sus datos de contacto</caption>
          <thead>
            <tr>
              <th scope="col">Nombre</th>
              <th scope="col">Apellido</th>
              <th scope="col">Cédula</th>
              <th scope="col">Teléfono</th>
              <th scope="col">Correo</th>
              <th scope="col" class="col-actions">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($clientes)): ?>
              <tr>
                <td colspan="6">
                  <?php if (!empty($search)): ?>
                    <div class="empty">
                      <i class="ti ti-search-off empty-icon" aria-hidden="true"></i>
                      <p class="empty-title">Sin resultados para «<?= htmlspecialchars($search) ?>»</p>
                      <p class="empty-sub">Intenta con otro término o limpia el buscador.</p>
                      <a href="<?= url('clientes') ?>" class="btn btn-secondary btn-sm mt-2">Ver todos</a>
                    </div>
                  <?php else: ?>
                    <div class="empty">
                      <p class="empty-title">No hay clientes registrados</p>
                      <p class="empty-sub">Las ventas sin cliente se guardan como «Consumidor final».</p>
                    </div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($clientes as $cliente): ?>
                <tr>
                  <td class="font-medium"><?= htmlspecialchars($cliente['cliente_nombre']) ?></td>
                  <td><?= htmlspecialchars($cliente['cliente_apellido']) ?></td>
                  <td class="tnum text-ink-2"><?= htmlspecialchars($cliente['cliente_cedula']) ?></td>
                  <td class="tnum text-ink-2">
                    <?= $cliente['cliente_telefono'] ? htmlspecialchars($cliente['cliente_telefono']) : '—' ?></td>
                  <td class="text-ink-2">
                    <?= $cliente['cliente_correo'] ? htmlspecialchars($cliente['cliente_correo']) : '—' ?></td>
                  <td class="col-actions">
                    <a href="<?= url('clientes/editar/' . $cliente['cliente_id']) ?>" class="btn-icon"
                      aria-label="Editar a <?= htmlspecialchars($cliente['cliente_nombre'] . ' ' . $cliente['cliente_apellido'], ENT_QUOTES) ?>">
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
      $label_items = 'clientes';
      $ruta_paginacion = 'clientes';
      include RUTA_APP . '/includes/paginacion.php';
      ?>
    </div>

  </div>
</main>

<?php include RUTA_APP . '/includes/footer.php'; ?>