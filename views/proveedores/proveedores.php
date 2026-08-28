<?php
$page_title = 'Proveedores';
$page_desc = 'Contactos para compras y surtido.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--mid">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Proveedores</h1>
        <p class="page-sub">A quién le compras. Cada surtido se registra a nombre de un proveedor.</p>
      </div>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <!-- Registro -->
    <div class="card p-5 flex flex-col gap-4">
      <h2 class="section-title">Nuevo proveedor</h2>

      <form class="flex flex-col sm:flex-row sm:items-end gap-3" action="<?= url('proveedores/crear') ?>" method="POST">
        <div class="field flex-1">
          <label for="nombre" class="label">Nombre comercial <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="nombre" name="nombre" class="input" required autocomplete="organization">
        </div>
        <div class="field flex-1">
          <label for="telefono" class="label">Teléfono</label>
          <input type="tel" id="telefono" name="telefono" class="input" autocomplete="tel">
        </div>
        <button type="submit" class="btn btn-primary">Agregar</button>
      </form>
    </div>

    <!-- Tabla -->
    <div class="card overflow-hidden">
      <div class="card-head flex-wrap gap-3">
        <h2 class="section-title">Proveedores registrados</h2>
        <form method="GET" action="<?= url('proveedores') ?>"
          class="search w-full sm:w-64 ml-auto relative flex items-center">
          <label for="filtroProveedores" class="sr-only">Buscar proveedor</label>
          <i class="ti ti-search search-icon" aria-hidden="true"></i>
          <input type="text" id="filtroProveedores" name="q" value="<?= htmlspecialchars($search ?? '') ?>"
            class="input pl-9 pr-8" placeholder="Buscar proveedor o teléfono…" autocomplete="off">
          <button type="button"
            class="btn-clear-search absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-3 hover:text-ink hover:bg-card-2 p-1 rounded-full flex items-center justify-center transition-colors"
            title="Limpiar búsqueda" aria-label="Limpiar búsqueda" <?= empty($search) ? 'style="display:none;"' : '' ?>>
            <i class="ti ti-x text-xs cursor-pointer text-red-500"></i>
          </button>
        </form>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Proveedores registrados con su teléfono de contacto</caption>
          <thead>
            <tr>
              <th scope="col">Proveedor</th>
              <th scope="col">Teléfono</th>
              <th scope="col" class="col-actions">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($proveedores)): ?>
              <tr>
                <td colspan="3">
                  <?php if (!empty($search)): ?>
                    <div class="empty">
                      <i class="ti ti-search-off empty-icon" aria-hidden="true"></i>
                      <p class="empty-title">Sin resultados para «<?= htmlspecialchars($search) ?>»</p>
                      <p class="empty-sub">Intenta con otro término o limpia el buscador.</p>
                      <a href="<?= url('proveedores') ?>" class="btn btn-secondary btn-sm mt-2">Ver todos</a>
                    </div>
                  <?php else: ?>
                    <div class="empty">
                      <p class="empty-title">No hay proveedores registrados</p>
                      <p class="empty-sub">Necesitas al menos uno para poder registrar surtidos.</p>
                    </div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($proveedores as $proveedor): ?>
                <tr>
                  <td class="font-medium"><?= htmlspecialchars($proveedor['proveedor_nombre']) ?></td>
                  <td class="tnum text-ink-2">
                    <?= $proveedor['proveedor_telefono'] ? htmlspecialchars($proveedor['proveedor_telefono']) : '—' ?></td>
                  <td class="col-actions">
                    <a href="<?= url('proveedores/editar/' . $proveedor['proveedor_id']) ?>" class="btn-icon"
                      aria-label="Editar el proveedor <?= htmlspecialchars($proveedor['proveedor_nombre'], ENT_QUOTES) ?>">
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
      $label_items = 'proveedores';
      $ruta_paginacion = 'proveedores';
      include RUTA_APP . '/includes/paginacion.php';
      ?>
    </div>

  </div>
</main>

<?php include RUTA_APP . '/includes/footer.php'; ?>