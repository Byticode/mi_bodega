<?php
$page_title = 'Editar cliente';
$page_desc  = 'Actualiza los datos de un cliente.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

$cliente = $dato[0];
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--narrow">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <nav class="breadcrumb" aria-label="Ruta de navegación">
          <a href="<?= url('clientes') ?>">Clientes</a>
          <span aria-hidden="true">/</span>
          <span><?= htmlspecialchars($cliente['cliente_nombre'] . ' ' . $cliente['cliente_apellido']) ?></span>
        </nav>
        <h1 class="page-title">Editar cliente</h1>
      </div>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <form action="<?= url('clientes/editar/' . $cliente['cliente_id']) ?>" method="POST" class="card p-5 flex flex-col gap-4">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="field">
          <label for="nombre" class="label">Nombre <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="nombre" name="nombre" class="input" required autocomplete="given-name"
            value="<?= htmlspecialchars($cliente['cliente_nombre']) ?>">
        </div>

        <div class="field">
          <label for="apellido" class="label">Apellido <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="apellido" name="apellido" class="input" required autocomplete="family-name"
            value="<?= htmlspecialchars($cliente['cliente_apellido']) ?>">
        </div>
      </div>

      <div class="field">
        <label for="cedula" class="label">Cédula o identificación <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="cedula" name="cedula" class="input" required autocomplete="off" inputmode="numeric"
          value="<?= htmlspecialchars($cliente['cliente_cedula']) ?>">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="field">
          <label for="telefono" class="label">Teléfono</label>
          <input type="tel" id="telefono" name="telefono" class="input" autocomplete="tel"
            value="<?= htmlspecialchars($cliente['cliente_telefono'] ?? '') ?>">
        </div>

        <div class="field">
          <label for="correo" class="label">Correo electrónico</label>
          <input type="email" id="correo" name="correo" class="input" autocomplete="email"
            value="<?= htmlspecialchars($cliente['cliente_correo'] ?? '') ?>">
        </div>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-3 pt-3 border-t border-rule">
        <a href="<?= url('clientes') ?>" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>

  </div>
</main>

<?php include RUTA_APP . '/includes/footer.php'; ?>
