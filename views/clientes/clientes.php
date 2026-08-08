<?php
$page_title = 'Clientes';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Clientes</h2>
          <p class="page-sub">Gestión de datos de clientes frecuentes.</p>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <!-- REGISTRO -->
      <div class="card p-5">
        <h3 class="text-sm font-semibold text-ink mb-4">Nuevo cliente</h3>
        <form class="grid grid-cols-1 sm:grid-cols-3 gap-3" action="index.php?controller=clientesController&action=crear" method="POST">
          <input type="text" name="nombre" placeholder="Nombre *" class="input" required>
          <input type="text" name="apellido" placeholder="Apellido *" class="input" required>
          <input type="text" name="cedula" placeholder="Cédula / ID *" class="input" required>
          <input type="text" name="telefono" placeholder="Teléfono (opcional)" class="input">
          <input type="email" name="correo" placeholder="Correo electrónico (opcional)" class="input">
          <button type="submit" class="btn btn-primary">Registrar cliente</button>
        </form>
        <p class="text-xs text-ink-3 mt-3">* Campos obligatorios</p>
      </div>

      <!-- TABLA -->
      <div class="card overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>Cédula / ID</th>
              <th>Teléfono</th>
              <th>Correo</th>
              <th class="text-right">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($clientes)): ?>
              <tr>
                <td colspan="6" class="text-center text-ink-3">No hay registros todavía.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($clientes as $cliente): ?>
              <tr>
                <td class="font-medium"><?= htmlspecialchars($cliente['cliente_nombre']) ?></td>
                <td><?= htmlspecialchars($cliente['cliente_apellido']) ?></td>
                <td class="tnum"><?= htmlspecialchars($cliente['cliente_cedula']) ?></td>
                <td><?= htmlspecialchars($cliente['cliente_telefono'] ?? '-') ?></td>
                <td><?= htmlspecialchars($cliente['cliente_correo'] ?? '-') ?></td>
                <td class="text-right">
                  <a href="index.php?controller=clientesController&action=editar&id=<?= $cliente['cliente_id'] ?>" class="btn-icon" aria-label="Editar cliente">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
