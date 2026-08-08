<?php
$page_title = 'Credenciales de acceso';
$page_desc  = 'Administra la clave principal de ingreso al sistema.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--mid">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Credenciales de acceso</h1>
        <p class="page-sub">Clave de seguridad principal para ingresar al sistema de la bodega.</p>
      </div>
    </div>

    <?php include ruta . '/includes/flash.php'; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">

      <!-- Formulario -->
      <div class="md:col-span-2 card p-5 flex flex-col gap-5">
        <div class="flex items-center gap-3 border-b border-rule pb-4">
          <span class="p-2.5 bg-olive-light text-olive rounded-lg shrink-0" aria-hidden="true">
            <i class="ti ti-lock text-lg" aria-hidden="true"></i>
          </span>
          <div>
            <h2 class="section-title">Contraseña maestra</h2>
            <p class="section-sub">Clave única requerida para iniciar sesión en caja o administración.</p>
          </div>
        </div>

        <form class="flex flex-col gap-4" onsubmit="event.preventDefault();">
          <div class="field">
            <label for="clave_actual" class="label">Contraseña actual <span class="req" aria-hidden="true">*</span></label>
            <input type="password" id="clave_actual" name="clave_actual" class="input" autocomplete="current-password" required>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field">
              <label for="clave_nueva" class="label">Nueva contraseña <span class="req" aria-hidden="true">*</span></label>
              <input type="password" id="clave_nueva" name="clave_nueva" class="input" autocomplete="new-password"
                     minlength="6" required aria-describedby="clave-hint">
              <p class="hint" id="clave-hint">Mínimo 6 caracteres.</p>
            </div>

            <div class="field">
              <label for="clave_confirmar" class="label">Confirmar contraseña <span class="req" aria-hidden="true">*</span></label>
              <input type="password" id="clave_confirmar" name="clave_confirmar" class="input" autocomplete="new-password"
                     minlength="6" required>
            </div>
          </div>

          <div class="flex justify-end pt-3 border-t border-rule">
            <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
          </div>
        </form>
      </div>

      <!-- Nota lateral -->
      <aside class="card bg-card-2 p-5 flex flex-col gap-3" aria-labelledby="notaAcceso">
        <h2 class="section-title flex items-center gap-2" id="notaAcceso">
          <i class="ti ti-info-circle text-olive shrink-0 text-base" aria-hidden="true"></i>
          <span>Acceso directo</span>
        </h2>
        <p class="text-xs text-ink-2 leading-relaxed">
          El sistema usa inicio de sesión único: no pide usuario ni correo para ingresar, solo esta clave.
        </p>
      </aside>

    </div>

  </div>
</main>

<?php include ruta . '/includes/footer.php'; ?>
