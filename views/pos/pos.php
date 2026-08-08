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
            <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <input type="search" id="buscarProducto" class="input" placeholder="Buscar producto…" autocomplete="off" autofocus>
          </div>
          <p class="text-xs text-ink-3 tnum" id="contadorProductos" role="status">
            <?= count($productos) ?> productos
          </p>
        </div>

        <?php if (empty($productos)): ?>
          <div class="card">
            <div class="empty">
              <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
              <p class="empty-title">Todavía no hay productos</p>
              <p class="empty-sub">Registra mercancía en el inventario para poder venderla desde aquí.</p>
              <a href="<?= url('productos/crear') ?>" class="btn btn-primary mt-3">Crear producto</a>
            </div>
          </div>
        <?php else: ?>
          <div id="productosGrid" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
            <?php foreach ($productos as $producto):
              $stock = (int) $producto['producto_stock'];
              $abrev = $producto['unidad_abreviatura'] ?? 'u';
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
                  <span class="pos-tile-meta"><?= $stock ?> <?= htmlspecialchars($abrev) ?> en stock</span>
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

            <div class="field" id="metodoPagoContainer">
              <label for="metodo_pago" class="label">Método de pago</label>
              <select id="metodo_pago" name="metodo_pago" class="select">
                <option value="efectivo">Efectivo</option>
                <option value="transferencia">Transferencia</option>
                <option value="pago_movil">Pago móvil</option>
                <option value="biopago">Biopago</option>
                <option value="cashea">Cashea</option>
              </select>
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
                ≈ $ 0,00 <span class="text-ink-3">· tasa <?= money($tasa_usd) ?></span>
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
