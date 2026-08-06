<?php
$page_title = 'Editar producto base';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-xl space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Editar producto base</h2>
          <p class="page-sub">
            <a href="index.php?controller=productosBaseController&action=listar" class="text-olive hover:underline">Productos base</a>
            <span class="text-ink-3"> / Harina de Trigo 1kg</span>
          </p>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <!-- FORMULARIO -->
      <div class="card p-5">
        <form onsubmit="event.preventDefault();" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="codigo" class="label">Código de barras</label>
              <input type="text" id="codigo" name="codigo" class="input" value="7501000123456">
            </div>
            <div>
              <label for="nombre" class="label">Nombre del producto</label>
              <input type="text" id="nombre" name="nombre" class="input" value="Harina de Trigo 1kg">
            </div>
            <div class="md:col-span-2">
              <label for="categoria" class="label">Categoría</label>
              <select id="categoria" name="categoria" class="select">
                <option selected>Abarrotes</option>
                <option>Bebidas</option>
              </select>
            </div>
          </div>

          <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="index.php?controller=productosBaseController&action=listar" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
