<?php
$page_title = 'Usuarios';
$page_desc  = 'Administra los usuarios del sistema.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--mid">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Usuarios</h1>
        <p class="page-sub">Quién puede entrar al sistema y con qué permisos.</p>
      </div>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <!-- Registro -->
    <div class="card p-5 flex flex-col gap-4">
      <h2 class="section-title">Nuevo usuario</h2>

      <form class="grid grid-cols-1 sm:grid-cols-2 gap-3" action="<?= url('usuarios/crear') ?>" method="POST">
        <div class="field">
          <label for="nombre" class="label">Nombre completo <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="nombre" name="nombre" class="input" required autocomplete="name">
        </div>

        <div class="field">
          <label for="username" class="label">Nombre de usuario <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="username" name="username" class="input" required autocomplete="username">
        </div>

        <div class="field">
          <label for="clave" class="label">Contraseña <span class="req" aria-hidden="true">*</span></label>
          <input type="password" id="clave" name="clave" class="input" required autocomplete="new-password" minlength="6"
                 aria-describedby="clave-hint">
          <p class="hint" id="clave-hint">Mínimo 6 caracteres.</p>
        </div>

        <div class="field">
          <label for="rol" class="label">Rol <span class="req" aria-hidden="true">*</span></label>
          <select id="rol" name="rol" class="select" required aria-describedby="rol-hint">
            <option value="vendedor">Vendedor</option>
            <option value="admin">Administrador</option>
          </select>
          <p class="hint" id="rol-hint">El administrador puede cambiar la configuración.</p>
        </div>

        <div class="sm:col-span-2 flex justify-end">
          <button type="submit" class="btn btn-primary">Registrar usuario</button>
        </div>
      </form>
    </div>

    <!-- Tabla -->
    <div class="card overflow-hidden">
      <div class="card-head">
        <h2 class="section-title">Usuarios registrados</h2>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Usuarios del sistema con su rol y fecha de registro</caption>
          <thead>
            <tr>
              <th scope="col">Nombre</th>
              <th scope="col">Usuario</th>
              <th scope="col">Rol</th>
              <th scope="col">Registro</th>
              <th scope="col" class="col-actions">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($usuarios)): ?>
              <tr>
                <td colspan="5">
                  <div class="empty">
                    <p class="empty-title">No hay usuarios registrados</p>
                    <p class="empty-sub">Crea el primero con el formulario de arriba.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($usuarios as $usuario): $es_admin = $usuario['usuario_rol'] === 'admin'; ?>
                <tr>
                  <td class="font-medium"><?= htmlspecialchars($usuario['usuario_nombre']) ?></td>
                  <td class="font-mono text-xs text-ink-2"><?= htmlspecialchars($usuario['usuario_username']) ?></td>
                  <td>
                    <span class="badge <?= $es_admin ? 'badge-olive' : 'badge-neutral' ?>">
                      <?= $es_admin ? 'Administrador' : 'Vendedor' ?>
                    </span>
                  </td>
                  <td class="text-ink-2 whitespace-nowrap"><?= date('d/m/Y', strtotime($usuario['created_at'])) ?></td>
                  <td class="col-actions">
                    <a href="<?= url('usuarios/editar/' . $usuario['usuario_id']) ?>"
                       class="btn-icon" aria-label="Editar a <?= htmlspecialchars($usuario['usuario_nombre'], ENT_QUOTES) ?>">
                      <i class="ti ti-pencil text-base" aria-hidden="true"></i>
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

<?php include RUTA_APP . '/includes/footer.php'; ?>
