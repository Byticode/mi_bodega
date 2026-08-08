<?php
$page_title = 'Editar unidad';
$page_desc  = 'Actualiza una unidad de medida.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';

$unidad = $dato[0];
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--narrow">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <nav class="breadcrumb" aria-label="Ruta de navegación">
          <a href="index.php?controller=unidadesController&action=listar">Unidades</a>
          <span aria-hidden="true">/</span>
          <span><?= htmlspecialchars($unidad['unidad_nombre']) ?></span>
        </nav>
        <h1 class="page-title">Editar unidad</h1>
        <p class="page-sub">Cambiar la abreviatura no renombra los productos ya creados.</p>
      </div>
    </div>

    <?php include ruta . '/includes/flash.php'; ?>

    <form action="index.php?controller=unidadesController&action=editar&id=<?= (int) $unidad['unidad_id'] ?>" method="POST" class="card p-5 flex flex-col gap-4">
      <div class="field">
        <label for="nombre" class="label">Nombre <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="nombre" name="nombre" class="input" required autocomplete="off"
               value="<?= htmlspecialchars($unidad['unidad_nombre']) ?>">
      </div>

      <div class="field">
        <label for="abreviatura" class="label">Abreviatura <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="abreviatura" name="abreviatura" class="input" required autocomplete="off" maxlength="10"
               value="<?= htmlspecialchars($unidad['unidad_abreviatura']) ?>">
      </div>

      <div class="flex flex-wrap items-center justify-end gap-3 pt-3 border-t border-rule">
        <a href="index.php?controller=unidadesController&action=listar" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>

  </div>
</main>

<?php include ruta . '/includes/footer.php'; ?>
