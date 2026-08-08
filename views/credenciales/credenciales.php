<?php
$page_title = 'Credenciales de Acceso';
include ruta . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-3xl mx-auto space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Credenciales de Acceso</h2>
          <p class="page-sub">Administra la clave de seguridad principal para ingresar al sistema de la bodega.</p>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- FORMULARIO -->
        <div class="md:col-span-2 card p-5 space-y-5">
          <div class="flex items-center gap-3 border-b border-rule pb-4">
            <div class="p-2.5 bg-olive-light text-olive rounded-lg">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-ink">Contraseña Master</h3>
              <p class="text-xs text-ink-3">Clave única requerida para iniciar sesión en la caja o administración.</p>
            </div>
          </div>

          <form class="space-y-4" onsubmit="event.preventDefault();">
            <div>
              <label for="clave_actual" class="label">Contraseña Actual</label>
              <input type="password" id="clave_actual" placeholder="••••••••" class="input">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="clave_nueva" class="label">Nueva Contraseña</label>
                <input type="password" id="clave_nueva" placeholder="Mínimo 6 caracteres" class="input">
              </div>

              <div>
                <label for="clave_confirmar" class="label">Confirmar Nueva Contraseña</label>
                <input type="password" id="clave_confirmar" placeholder="Repite la clave" class="input">
              </div>
            </div>

            <div class="flex justify-end pt-2">
              <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
            </div>
          </form>
        </div>

        <!-- INFO LATERAL -->
        <div class="card bg-card-2 p-5 space-y-4 h-fit">
          <h4 class="text-sm font-semibold text-ink flex items-center gap-2">
            <svg class="w-4 h-4 text-olive" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
            <span>Acceso Directo</span>
          </h4>

          <p class="text-xs text-ink-2 leading-relaxed">
            El sistema está optimizado para inicio de sesión único. No requiere usuario ni correo electrónico para ingresar.
          </p>

          <div class="border-t border-rule pt-3">
            <span class="text-xs font-semibold text-ink-3 uppercase tracking-wider block mb-1">Último cambio</span>
            <p class="text-xs font-medium text-ink">01 de Agosto de 2026</p>
          </div>
        </div>

      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
