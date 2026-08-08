<?php
$page_title = 'Unidades de medida';
$page_desc  = 'Administra las unidades de medida de los productos.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--mid">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Unidades de medida</h1>
        <p class="page-sub">La abreviatura se pega al nombre del producto: «Arroz 1kg».</p>
      </div>
    </div>

    <?php include ruta . '/includes/flash.php'; ?>

    <!-- Registro -->
    <div class="card p-5 flex flex-col gap-4">
      <h2 class="section-title">Nueva unidad</h2>

      <form class="flex flex-col sm:flex-row sm:items-end gap-3" action="index.php?controller=unidadesController&action=crear" method="POST">
        <div class="field flex-1">
          <label for="nombre" class="label">Nombre <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="nombre" name="nombre" class="input" placeholder="Ej: Kilogramo" required autocomplete="off">
        </div>
        <div class="field sm:w-40">
          <label for="abreviatura" class="label">Abreviatura <span class="req" aria-hidden="true">*</span></label>
          <input type="text" id="abreviatura" name="abreviatura" class="input" placeholder="Ej: kg" required autocomplete="off" maxlength="10">
        </div>
        <button type="submit" class="btn btn-primary">Agregar</button>
      </form>
    </div>

    <!-- Tabla -->
    <div class="card overflow-hidden">
      <div class="card-head">
        <h2 class="section-title">Unidades registradas</h2>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Unidades de medida registradas</caption>
          <thead>
            <tr>
              <th scope="col">Nombre</th>
              <th scope="col">Abreviatura</th>
              <th scope="col" class="col-actions">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($unidades)): ?>
              <tr>
                <td colspan="3">
                  <div class="empty">
                    <p class="empty-title">No hay unidades registradas</p>
                    <p class="empty-sub">Agrega al menos una para poder crear productos.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($unidades as $unidad): ?>
                <tr>
                  <td class="font-medium"><?= htmlspecialchars($unidad['unidad_nombre']) ?></td>
                  <td><span class="badge badge-olive font-mono"><?= htmlspecialchars($unidad['unidad_abreviatura']) ?></span></td>
                  <td class="col-actions">
                    <a href="index.php?controller=unidadesController&action=editar&id=<?= (int) $unidad['unidad_id'] ?>"
                       class="btn-icon" aria-label="Editar la unidad <?= htmlspecialchars($unidad['unidad_nombre'], ENT_QUOTES) ?>">
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

<?php include ruta . '/includes/footer.php'; ?>
