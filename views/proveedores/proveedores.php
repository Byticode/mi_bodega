<?php
$page_title = 'Proveedores';
$page_desc  = 'Contactos para compras y surtido.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
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

    <?php include ruta . '/includes/flash.php'; ?>

    <!-- Registro -->
    <div class="card p-5 flex flex-col gap-4">
      <h2 class="section-title">Nuevo proveedor</h2>

      <form class="flex flex-col sm:flex-row sm:items-end gap-3" action="index.php?controller=proveedoresController&action=crear" method="POST">
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
      <div class="card-head">
        <h2 class="section-title">Proveedores registrados</h2>
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
                  <div class="empty">
                    <p class="empty-title">No hay proveedores registrados</p>
                    <p class="empty-sub">Necesitas al menos uno para poder registrar surtidos.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($proveedores as $proveedor): ?>
                <tr>
                  <td class="font-medium"><?= htmlspecialchars($proveedor['proveedor_nombre']) ?></td>
                  <td class="tnum text-ink-2"><?= $proveedor['proveedor_telefono'] ? htmlspecialchars($proveedor['proveedor_telefono']) : '—' ?></td>
                  <td class="col-actions">
                    <a href="index.php?controller=proveedoresController&action=editar&id=<?= (int) $proveedor['proveedor_id'] ?>"
                       class="btn-icon" aria-label="Editar el proveedor <?= htmlspecialchars($proveedor['proveedor_nombre'], ENT_QUOTES) ?>">
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

<?php include ruta . '/includes/footer.php'; ?>
