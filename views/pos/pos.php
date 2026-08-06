<?php
$page_title = 'Punto de Venta';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="app-main">
    <div class="max-w-6xl space-y-6">

      <!-- Page header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 class="page-title">Punto de Venta</h2>
          <p class="page-sub">Arma el ticket y cobra sin salir de esta pantalla.</p>
        </div>
      </div>

      <?php include ruta . '/includes/flash.php'; ?>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Selección de Productos / Búsqueda -->
        <section class="lg:col-span-2 space-y-4">
          <div class="flex flex-wrap items-center justify-end gap-2">
            <input type="text" placeholder="Buscar mercancía..." class="input w-full sm:w-64">
            <button type="button" class="btn btn-secondary">Filtrar</button>
          </div>

          <!-- Grid de productos rápidos -->
          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="card p-4 hover:border-olive cursor-pointer">
              <h4 class="font-semibold text-sm text-ink">Harina de maíz 1 kg</h4>
              <p class="text-xs text-ink-3">Stock: <span class="tnum">42</span></p>
              <p class="money text-olive mt-2">Bs 28,50</p>
            </div>
            <div class="card p-4 hover:border-olive cursor-pointer">
              <h4 class="font-semibold text-sm text-ink">Arroz blanco 1 kg</h4>
              <p class="text-xs text-ink-3">Stock: <span class="tnum">18</span></p>
              <p class="money text-olive mt-2">Bs 32,00</p>
            </div>
            <div class="card p-4 hover:border-olive cursor-pointer">
              <h4 class="font-semibold text-sm text-ink">Atún en aceite 140 g</h4>
              <p class="text-xs text-ink-3">Stock: <span class="tnum">25</span></p>
              <p class="money text-olive mt-2">Bs 22,50</p>
            </div>
            <div class="card p-4 hover:border-olive cursor-pointer">
              <h4 class="font-semibold text-sm text-ink">Aceite de maíz 1 L</h4>
              <p class="text-xs text-ink-3">Stock: <span class="tnum">10</span></p>
              <p class="money text-olive mt-2">Bs 76,00</p>
            </div>
            <div class="card p-4 hover:border-olive cursor-pointer">
              <h4 class="font-semibold text-sm text-ink">Azúcar refinada 1 kg</h4>
              <p class="text-xs text-ink-3">Stock: <span class="tnum">50</span></p>
              <p class="money text-olive mt-2">Bs 30,00</p>
            </div>
            <div class="card p-4 hover:border-olive cursor-pointer">
              <h4 class="font-semibold text-sm text-ink">Café molido 500 g</h4>
              <p class="text-xs text-ink-3">Stock: <span class="tnum">8</span></p>
              <p class="money text-olive mt-2">Bs 95,00</p>
            </div>
          </div>
        </section>

        <!-- Ticket de Compra -->
        <section class="card p-6 lg:col-span-1 flex flex-col justify-between">
          <div>
            <h3 class="font-semibold text-ink border-b border-rule pb-2 mb-4">Ticket actual</h3>

            <!-- SELECT DE CLIENTE -->
            <div class="mb-5">
              <label for="cliente" class="label">Cliente</label>
              <select id="cliente" class="select">
                <option value="consumidor_final">Consumidor final</option>
                <option value="maria_bracho">María Bracho</option>
                <option value="luis_perez">Luis A. Pérez</option>
                <option value="yelitza_morales">Yelitza Morales</option>
                <option value="bodegon_el_trebol">Bodegón El Trébol</option>
              </select>
            </div>

            <div class="space-y-4 text-sm">
              <div class="flex justify-between items-center gap-3 border-b border-rule pb-3">
                <div>
                  <p class="font-semibold text-ink">Harina de maíz 1 kg</p>
                  <span class="text-xs text-ink-3">P. Unit: Bs 28,50</span>
                </div>
                <div class="flex items-center gap-3">
                  <div class="flex items-center gap-1">
                    <button type="button" class="btn btn-secondary px-2 py-0.5 text-xs" aria-label="Quitar uno">-</button>
                    <span class="tnum px-1 text-xs font-semibold">2</span>
                    <button type="button" class="btn btn-secondary px-2 py-0.5 text-xs" aria-label="Agregar uno">+</button>
                  </div>
                  <span class="money min-w-[60px] text-right">Bs 57,00</span>
                </div>
              </div>

              <div class="flex justify-between items-center gap-3 border-b border-rule pb-3">
                <div>
                  <p class="font-semibold text-ink">Arroz blanco 1 kg</p>
                  <span class="text-xs text-ink-3">P. Unit: Bs 32,00</span>
                </div>
                <div class="flex items-center gap-3">
                  <div class="flex items-center gap-1">
                    <button type="button" class="btn btn-secondary px-2 py-0.5 text-xs" aria-label="Quitar uno">-</button>
                    <span class="tnum px-1 text-xs font-semibold">1</span>
                    <button type="button" class="btn btn-secondary px-2 py-0.5 text-xs" aria-label="Agregar uno">+</button>
                  </div>
                  <span class="money min-w-[60px] text-right">Bs 32,00</span>
                </div>
              </div>
            </div>
          </div>

          <div class="border-t border-rule pt-4 mt-6">
            <div class="flex justify-between items-baseline font-semibold mb-4">
              <span>Total:</span>
              <span class="money text-lg text-olive">Bs 89,00</span>
            </div>
            <button type="button" class="btn btn-primary w-full">
              Cobrar Ticket
            </button>
          </div>
        </section>

      </div>

    </div>
  </main>

<?php include ruta . '/includes/footer.php'; ?>
