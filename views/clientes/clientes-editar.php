<?php
$page_title = 'Editar cliente';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-xl mx-auto space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Editar cliente</h2>
          <p class="page-sub">
            <a href="index.php?controller=clientesController&action=listar" class="text-olive hover:underline">Clientes</a>
            <span class="text-ink-3"> / <?= htmlspecialchars($dato[0]['cliente_nombre'] . ' ' . $dato[0]['cliente_apellido']) ?></span>
          </p>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <!-- FORMULARIO -->
      <div class="card p-5">
        <form action="index.php?controller=clientesController&action=editar&id=<?= $dato[0]['cliente_id'] ?>" method="POST" class="space-y-4">

          <div>
            <label for="nombre" class="label">Nombre *</label>
            <input type="text" id="nombre" name="nombre" class="input" required
              value="<?= htmlspecialchars($dato[0]['cliente_nombre']) ?>">
          </div>

          <div>
            <label for="apellido" class="label">Apellido *</label>
            <input type="text" id="apellido" name="apellido" class="input" required
              value="<?= htmlspecialchars($dato[0]['cliente_apellido']) ?>">
          </div>

          <div>
            <label for="cedula" class="label">Cédula / Identificación *</label>
            <input type="text" id="cedula" name="cedula" class="input" required
              value="<?= htmlspecialchars($dato[0]['cliente_cedula']) ?>">
          </div>

          <div>
            <label for="telefono" class="label">Teléfono (opcional)</label>
            <input type="text" id="telefono" name="telefono" class="input"
              value="<?= htmlspecialchars($dato[0]['cliente_telefono'] ?? '') ?>">
          </div>

          <div>
            <label for="correo" class="label">Correo electrónico (opcional)</label>
            <input type="email" id="correo" name="correo" class="input"
              value="<?= htmlspecialchars($dato[0]['cliente_correo'] ?? '') ?>">
          </div>

          <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="index.php?controller=clientesController&action=listar" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
