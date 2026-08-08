<?php
$page_title = 'Editar usuario';
$page_desc  = 'Actualiza los datos de acceso de un usuario.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';

$usuario = $dato[0];
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--narrow">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <nav class="breadcrumb" aria-label="Ruta de navegación">
          <a href="index.php?controller=usuariosController&action=listar">Usuarios</a>
          <span aria-hidden="true">/</span>
          <span><?= htmlspecialchars($usuario['usuario_nombre']) ?></span>
        </nav>
        <h1 class="page-title">Editar usuario</h1>
      </div>
    </div>

    <?php include ruta . '/includes/flash.php'; ?>

    <form action="index.php?controller=usuariosController&action=editar&id=<?= (int) $usuario['usuario_id'] ?>" method="POST" class="card p-5 flex flex-col gap-4">
      <div class="field">
        <label for="nombre" class="label">Nombre completo <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="nombre" name="nombre" class="input" required autocomplete="name"
               value="<?= htmlspecialchars($usuario['usuario_nombre']) ?>">
      </div>

      <div class="field">
        <label for="username" class="label">Nombre de usuario <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="username" name="username" class="input" required autocomplete="username"
               value="<?= htmlspecialchars($usuario['usuario_username']) ?>">
      </div>

      <div class="field">
        <label for="clave" class="label">Nueva contraseña</label>
        <input type="password" id="clave" name="clave" class="input" autocomplete="new-password" minlength="6"
               placeholder="Sin cambios" aria-describedby="clave-hint">
        <p class="hint" id="clave-hint">Déjalo vacío para conservar la contraseña actual.</p>
      </div>

      <div class="field">
        <label for="rol" class="label">Rol <span class="req" aria-hidden="true">*</span></label>
        <select id="rol" name="rol" class="select" required>
          <option value="vendedor" <?= $usuario['usuario_rol'] === 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
          <option value="admin" <?= $usuario['usuario_rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-3 pt-3 border-t border-rule">
        <a href="index.php?controller=usuariosController&action=listar" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>

  </div>
</main>

<?php include ruta . '/includes/footer.php'; ?>
