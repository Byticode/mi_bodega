<?php
$page_title = 'Clientes';
$page_desc  = 'Datos de los clientes frecuentes de la bodega.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
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

    <?php include ruta . '/includes/flash.php'; ?>

    <!-- Registro -->
    <div class="card p-5 flex flex-col gap-4">
      <h2 class="section-title">Nuevo cliente</h2>

      <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3" action="index.php?controller=clientesController&action=crear" method="POST">
        <div class="field">
          <label for="nombre" class="label">Nombre <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="nombre" name="nombre" class="input" required autocomplete="given-name">
        </div>
        <div class="field">
          <label for="apellido" class="label">Apellido <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="apellido" name="apellido" class="input" required autocomplete="family-name">
        </div>
        <div class="field">
          <label for="cedula" class="label">Cédula o identificación <span class="req" aria-hidden="true">*</span></label>
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
      <div class="card-head">
        <h2 class="section-title">Clientes registrados</h2>
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
                  <div class="empty">
                    <p class="empty-title">No hay clientes registrados</p>
                    <p class="empty-sub">Las ventas sin cliente se guardan como «Consumidor final».</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($clientes as $cliente): ?>
                <tr>
                  <td class="font-medium"><?= htmlspecialchars($cliente['cliente_nombre']) ?></td>
                  <td><?= htmlspecialchars($cliente['cliente_apellido']) ?></td>
                  <td class="tnum text-ink-2"><?= htmlspecialchars($cliente['cliente_cedula']) ?></td>
                  <td class="tnum text-ink-2"><?= $cliente['cliente_telefono'] ? htmlspecialchars($cliente['cliente_telefono']) : '—' ?></td>
                  <td class="text-ink-2"><?= $cliente['cliente_correo'] ? htmlspecialchars($cliente['cliente_correo']) : '—' ?></td>
                  <td class="col-actions">
                    <a href="index.php?controller=clientesController&action=editar&id=<?= (int) $cliente['cliente_id'] ?>"
                       class="btn-icon" aria-label="Editar a <?= htmlspecialchars($cliente['cliente_nombre'] . ' ' . $cliente['cliente_apellido'], ENT_QUOTES) ?>">
                      <i class="ti ti-pencil text-base" aria-hidden="true"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if (!empty($clientes)): ?>
        <div class="card-foot">
          <span><?= count($clientes) ?> clientes registrados</span>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

<?php include ruta . '/includes/footer.php'; ?>
