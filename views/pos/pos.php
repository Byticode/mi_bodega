<?php
$page_title = 'Punto de venta';
$page_desc  = 'Registra ventas de mostrador y cobra el ticket.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

$tasa_usd = isset($tasa['tasa_usd']) ? (float) $tasa['tasa_usd'] : 0;
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--wide">

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

      <!-- ══ Selección de productos ══ -->
      <section class="lg:col-span-2 flex flex-col gap-4 min-w-0" aria-labelledby="tituloPos">
        <div class="page-head">
          <div>
            <h1 class="page-title" id="tituloPos">Punto de venta</h1>
            <p class="page-sub">Toca un producto para sumarlo al ticket.</p>
          </div>
        </div>

        <div class="toolbar">
          <div class="search flex-1 min-w-48">
            <label for="buscarProducto" class="sr-only">Buscar producto por nombre o código</label>
            <i class="ti ti-search search-icon" aria-hidden="true"></i>
            <input type="search" id="buscarProducto" class="input" placeholder="Buscar producto…" autocomplete="off" autofocus>
          </div>
          <p class="text-xs text-ink-3 tnum" id="contadorProductos" role="status">
            <?= count($productos) ?> productos
          </p>
        </div>

        <?php if (empty($productos)): ?>
          <div class="card">
            <div class="empty">
              <i class="ti ti-package empty-icon" aria-hidden="true"></i>
              <p class="empty-title">Todavía no hay productos</p>
              <p class="empty-sub">Registra mercancía en el inventario para poder venderla desde aquí.</p>
              <a href="<?= url('productos/crear') ?>" class="btn btn-primary mt-3">Crear producto</a>
            </div>
          </div>
        <?php else: ?>
          <div id="productosGrid" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
            <?php foreach ($productos as $producto):
              $stock = (int) $producto['producto_stock'];
              $abrev = 'unid.';
            ?>
              <button type="button"
                class="pos-tile"
                data-id="<?= (int) $producto['producto_id'] ?>"
                data-nombre="<?= htmlspecialchars($producto['producto_nombre'], ENT_QUOTES) ?>"
                data-precio="<?= (float) $producto['producto_precio_venta'] ?>"
                data-unidad="<?= htmlspecialchars($abrev, ENT_QUOTES) ?>"
                data-stock="<?= $stock ?>"
                data-buscar="<?= htmlspecialchars(mb_strtolower($producto['producto_nombre'] . ' ' . ($producto['producto_codigo'] ?? '')), ENT_QUOTES) ?>"
                <?= $stock <= 0 ? 'disabled' : '' ?>>
                <span class="pos-tile-name"><?= htmlspecialchars($producto['producto_nombre']) ?></span>
                <?php if ($stock <= 0): ?>
                  <span class="badge badge-danger mt-1">Agotado</span>
                <?php elseif ($stock <= 10): ?>
                  <span class="badge badge-warn mt-1">Quedan <?= $stock ?> <?= htmlspecialchars($abrev) ?></span>
                <?php else: ?>
                  <span class="badge badge-success mt-1">Quedan <?= $stock ?> <?= htmlspecialchars($abrev) ?></span>
                <?php endif; ?>
                <span class="pos-tile-price"><?= money($producto['producto_precio_venta']) ?></span>
              </button>
            <?php endforeach; ?>
          </div>

          <div id="sinResultados" class="card" hidden>
            <div class="empty">
              <p class="empty-title">Sin coincidencias</p>
              <p class="empty-sub">Ningún producto coincide con la búsqueda. Prueba con otro nombre o código.</p>
            </div>
          </div>
        <?php endif; ?>
      </section>

      <!-- ══ Ticket ══ -->
      <section class="card pos-ticket" aria-labelledby="tituloTicket">
        <form id="ventaForm" method="POST" action="<?= url('ventas/crear') ?>" class="flex flex-col min-h-0">

          <div class="card-head">
            <h2 class="section-title" id="tituloTicket">Ticket actual</h2>
            <button type="button" id="vaciarBtn" class="btn btn-ghost btn-sm" hidden>Vaciar</button>
          </div>

          <?php if ($tasa_usd <= 0): ?>
            <div class="p-4 pb-0">
              <div class="alert alert-warn" role="status">
                <i class="ti ti-alert-circle shrink-0 text-lg" aria-hidden="true"></i>
                <div class="flex-1">
                  <span class="font-semibold">Sin tasa de cambio.</span>
                  No se podrá guardar la venta.
                  <a href="<?= url('tasa-moneda') ?>" class="underline font-semibold">Configúrala aquí.</a>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <div class="p-4 flex flex-col gap-3 border-b border-rule">
            <div class="field">
              <label for="cliente_id" class="label">Cliente</label>
              <select id="cliente_id" name="cliente_id" class="select">
                <option value="">Consumidor final</option>
                <?php foreach ($clientes as $cliente): ?>
                  <option value="<?= (int) $cliente['cliente_id'] ?>">
                    <?= htmlspecialchars($cliente['cliente_nombre'] . ' ' . $cliente['cliente_apellido']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="field">
              <label for="estado_venta" class="label">Estado</label>
              <select id="estado_venta" name="estado" class="select">
                <option value="completada" selected>Completada — se cobra ahora</option>
                <option value="pendiente">Pendiente — se cobra después</option>
              </select>
            </div>

            <!-- ══ Tarjetas de Método de Pago Horizontales (5 Métodos) ══ -->
            <div class="field" id="metodoPagoContainer">
              <label class="label mb-1">Método de pago</label>
              <div class="mt-1 flex justify-center flex-wrap ">

                <!-- Biopago (Rojo) -->
                <label class="relative cursor-pointer group flex-1">
                  <input type="radio" name="metodo_pago" value="biopago" class="peer sr-only">
                  <div class="aspect-square flex flex-col items-center justify-center p-1 rounded-xl border-2 border-slate-200 bg-white transition-all peer-checked:border-red-500 peer-checked:bg-red-50/40 hover:border-slate-300">
                    <i class="ti ti-fingerprint text-base text-red-500 mb-0.5" aria-hidden="true"></i>
                    <span class="text-[4px] sm:text-[4px] font-medium text-red-500 truncate w-full text-center">Biopago</span>
                  </div>
                </label>

                <!-- Efectivo (Verde) -->
                <label class="relative cursor-pointer group flex-1">
                  <input type="radio" name="metodo_pago" value="efectivo" class="peer sr-only" checked>
                  <div class="aspect-square flex flex-col items-center justify-center p-1 rounded-xl border-2 border-slate-200 bg-white transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50/40 hover:border-slate-300">
                    <i class="ti ti-cash text-base text-emerald-500 mb-0.5" aria-hidden="true"></i>
                    <span class="text-[4px] sm:text-[4px] font-medium text-emerald-600 truncate w-full text-center">Efectivo</span>
                  </div>
                </label>

                <!-- Cashea (Amarillo/Dorado) -->
                <label class="relative cursor-pointer group flex-1">
                  <input type="radio" name="metodo_pago" value="cashea" class="peer sr-only">
                  <div class="aspect-square flex flex-col items-center justify-center p-1 rounded-xl border-2 border-slate-200 bg-white transition-all peer-checked:border-amber-400 peer-checked:bg-amber-50/40 hover:border-slate-300">
                    <span class="text-base font-black text-amber-400 leading-none mb-0.5">C</span>
                    <span class="text-[4px] sm:text-[4px] font-medium text-amber-500 truncate w-full text-center">Cashea</span>
                  </div>
                </label>

                <!-- Pago Móvil (Azul) -->
                <label class="relative cursor-pointer group flex-1">
                  <input type="radio" name="metodo_pago" value="pago_movil" class="peer sr-only">
                  <div class="aspect-square flex flex-col items-center justify-center p-1 rounded-xl border-2 border-slate-200 bg-white transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50/40 hover:border-slate-300">
                    <i class="ti ti-device-mobile text-base text-blue-500 mb-0.5" aria-hidden="true"></i>
                    <span class="text-[4px] sm:text-[4px] font-medium text-blue-600 truncate w-full text-center leading-tight">Pago Móvil</span>
                  </div>
                </label>

                <!-- Transferencia (Púrpura / Morado) -->
                <label class="relative cursor-pointer group flex-1">
                  <input type="radio" name="metodo_pago" value="transferencia" class="peer sr-only">
                  <div class="aspect-square flex flex-col items-center justify-center p-1 rounded-xl border-2 border-slate-200 bg-white transition-all peer-checked:border-purple-600 peer-checked:bg-purple-50/40 hover:border-slate-300">
                    <i class="ti ti-building-bank text-base text-purple-600 mb-0.5" aria-hidden="true"></i>
                    <span class="text-[4px] sm:text-[4px] font-medium text-purple-600 truncate w-full text-center leading-tight">Transf.</span>
                  </div>
                </label>

              </div>
            </div>

            <div class="field" id="numeroPagoContainer">
              <label for="numero_pago" class="label">Número de referencia</label>
              <input type="text" id="numero_pago" name="numero_pago" class="input" placeholder="Opcional" autocomplete="off">
            </div>
          </div>

          <!-- Líneas del carrito -->
          <div class="flex-1 overflow-y-auto px-4 min-h-36">
            <div id="carritoContainer">
              <p class="empty-sub text-center py-10 mx-auto" id="carritoVacio">El ticket está vacío.</p>
            </div>
          </div>

          <!-- Aviso accesible -->
          <p id="posAviso" class="sr-only" role="status" aria-live="polite"></p>

          <div class="p-4 border-t border-rule flex flex-col gap-3">
            <div class="pos-total">
              <span class="pos-total-label">Total</span>
              <span class="pos-total-value" id="totalCarrito"><?= money(0) ?></span>
            </div>
            <?php if ($tasa_usd > 0): ?>
              <p class="text-xs text-ink-3 text-right tnum" id="totalUsd">
                ≈ $ 0,00 <span class="text-ink-3">· tasa BCV <?= money($tasa_usd) ?></span>
              </p>
            <?php endif; ?>
            <div class="flex items-center justify-between text-xs text-ink-3">
              <span>Artículos</span>
              <span class="tnum" id="totalProductos">0</span>
            </div>

            <input type="hidden" name="productos" id="productosInput" value="[]">
            <button type="submit" class="btn btn-primary btn-lg btn-block" id="cobrarBtn" disabled>
              Cobrar ticket
            </button>
          </div>
        </form>
      </section>

    </div>
  </div>
</main>

<script>
  window.TASA_USD = <?= json_encode($tasa_usd) ?>;
</script>
<script src="<?= assets('scripts/pos.js') ?>"></script>

<?php include RUTA_APP . '/includes/footer.php'; ?>