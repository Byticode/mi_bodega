<?php
$page_title = "Punto de venta";
$page_desc = "Registra ventas de mostrador y cobra el ticket.";

$tasa = $tasa ?? [];
$productos = $productos ?? [];
$clientes = $clientes ?? [];

$tasa_usd = isset($tasa["tasa_usd"]) ? (float) $tasa["tasa_usd"] : 0;

include RUTA_APP . "/includes/head.php";
include RUTA_APP . "/includes/sidebar.php";
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--wide">

    <?php include RUTA_APP . "/includes/flash.php"; ?>

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
              <a href="<?= url(
                  "productos/crear",
              ) ?>" class="btn btn-primary mt-3">Crear producto</a>
            </div>
          </div>
        <?php else: ?>
          <div id="productosGrid" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
            <?php foreach ($productos as $producto):

                $stock = (int) $producto["producto_stock"];
                $abrev = "unid.";
                ?>
              <button type="button"
                class="pos-tile"
                data-id="<?= (int) $producto["producto_id"] ?>"
                data-nombre="<?= htmlspecialchars(
                    $producto["producto_nombre"],
                    ENT_QUOTES,
                ) ?>"
                data-precio="<?= (float) $producto["producto_precio_venta"] ?>"
                data-unidad="<?= htmlspecialchars($abrev, ENT_QUOTES) ?>"
                data-stock="<?= $stock ?>"
                data-buscar="<?= htmlspecialchars(
                    mb_strtolower(
                        $producto["producto_nombre"] .
                            " " .
                            ($producto["producto_codigo"] ?? ""),
                    ),
                    ENT_QUOTES,
                ) ?>"
                <?= $stock <= 0 ? "disabled" : "" ?>>
                <span class="pos-tile-name"><?= htmlspecialchars(
                    $producto["producto_nombre"],
                ) ?></span>
                <?php if ($stock <= 0): ?>
                  <span class="badge badge-danger mt-1">Agotado</span>
                <?php elseif ($stock <= 10): ?>
                  <span class="badge badge-warn mt-1">Quedan <?= $stock ?> <?= htmlspecialchars(
     $abrev,
 ) ?></span>

                <?php else: ?>
                  <span class="badge badge-success mt-1">Quedan <?= $stock ?> <?= htmlspecialchars(
     $abrev,
 ) ?></span>
                <?php endif; ?>
                <span class="pos-tile-price"><?= usd(
                    $producto["producto_precio_venta"],
                ) ?></span>
                <span class="text-xs text-ink-3 font-normal font-mono block mt-0.5"><?= bs(
                    $producto["producto_precio_venta"],
                ) ?></span>
              </button>
            <?php
            endforeach; ?>
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
        <form id="ventaForm" method="POST" action="<?= url(
            "ventas/crear",
        ) ?>" class="flex flex-col">

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
                  <a href="<?= url(
                      "tasa-moneda",
                  ) ?>" class="underline font-semibold">Configúrala aquí.</a>
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
                  <option value="<?= (int) $cliente["cliente_id"] ?>">
                    <?= htmlspecialchars(
                        $cliente["cliente_nombre"] .
                            " " .
                            $cliente["cliente_apellido"],
                    ) ?>
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
              <span class="label block mb-1.5">Método de pago</span>
              <select id="metodo_pago" name="metodo_pago" class="sr-only" aria-hidden="true" tabindex="-1">
                <option value="efectivo" selected>Efectivo</option>
                <option value="transferencia">Transferencia</option>
                <option value="pago_movil">Pago móvil</option>
                <option value="biopago">Biopago</option>
                <option value="cashea">Cashea</option>
              </select>

              <div class="grid grid-cols-3 gap-1.5" role="radiogroup" aria-label="Método de pago">
                <!-- Efectivo -->
                <button type="button" class="payment-card flex flex-col items-center justify-center p-2.5 rounded-xl border text-center transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 active:scale-95 border-emerald-600 text-emerald-600 bg-emerald-50/40 ring-2 ring-emerald-100/50" data-value="efectivo" role="radio" aria-checked="true">
                  <i class="ti ti-cash text-xl mb-1 text-slate-400" aria-hidden="true"></i>
                  <span class="text-xs font-semibold leading-tight text-slate-600">Efectivo</span>
                </button>

                <!-- Pago Movil / Transf -->
                <button type="button" class="payment-card flex flex-col items-center justify-center p-2.5 rounded-xl border text-center transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 active:scale-95 border-slate-200 text-slate-500 bg-white hover:bg-slate-50/50 hover:border-purple-200 hover:text-purple-600" data-value="pago_movil" role="radio" aria-checked="false">
                  <i class="ti ti-device-mobile text-xl mb-1 text-slate-400" aria-hidden="true"></i>
                  <span class="text-xs font-medium leading-tight text-slate-600">Pago Móvil/Transf</span>
                </button>

                <!-- Punto -->
                <button type="button" class="payment-card flex flex-col items-center justify-center p-2.5 rounded-xl border text-center transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 active:scale-95 border-slate-200 text-slate-500 bg-white hover:bg-slate-50/50 hover:border-blue-200 hover:text-blue-600" data-value="transferencia" role="radio" aria-checked="false">
                  <i class="ti ti-credit-card text-xl mb-1 text-slate-400" aria-hidden="true"></i>
                  <span class="text-xs font-medium leading-tight text-slate-600">Punto</span>
                </button>

                <!-- Biopago -->
                <button type="button" class="payment-card flex flex-col items-center justify-center p-2.5 rounded-xl border text-center transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 active:scale-95 border-slate-200 text-slate-500 bg-white hover:bg-slate-50/50 hover:border-rose-200 hover:text-rose-600" data-value="biopago" role="radio" aria-checked="false">
                  <i class="ti ti-fingerprint text-xl mb-1 text-slate-400" aria-hidden="true"></i>
                  <span class="text-xs font-medium leading-tight text-slate-600">Biopago</span>
                </button>

                <!-- Cashea -->
                <button type="button" class="payment-card flex flex-col items-center justify-center p-2.5 rounded-xl border text-center transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 active:scale-95 border-slate-200 text-slate-500 bg-white hover:bg-slate-50/50 hover:border-amber-200 hover:text-amber-600" data-value="cashea" role="radio" aria-checked="false">
                  <span class="text-xl font-extrabold font-sans leading-none mb-1 text-slate-400">C</span>
                  <span class="text-xs font-medium leading-tight text-slate-600">Cashea</span>
                </button>
              </div>
            </div>

            <div class="field" id="numeroPagoContainer">
              <label for="numero_pago" class="label">Número de referencia</label>
              <input type="text" id="numero_pago" name="numero_pago" class="input" placeholder="Opcional" autocomplete="off">
            </div>
          </div>

          <!-- Líneas del carrito -->
          <div class="px-4">
            <div id="carritoContainer">
              <p class="empty-sub text-center py-10 mx-auto" id="carritoVacio">El ticket está vacío.</p>
            </div>
          </div>

          <!-- Aviso accesible -->
          <p id="posAviso" class="sr-only" role="status" aria-live="polite"></p>

          <div class="p-4 border-t border-rule flex flex-col gap-3">
            <div class="pos-total">
              <span class="pos-total-label">Total USD</span>
              <span class="pos-total-value" id="totalCarrito"><?= usd(
                  0,
              ) ?></span>
            </div>
            <?php if ($tasa_usd > 0): ?>
              <p class="text-xs text-ink-3 text-right tnum" id="totalUsd">
                ≈ Bs 0,00 <span class="text-ink-3">· tasa BCV <?= money(
                    $tasa_usd,
                ) ?>/$</span>
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
<script src="<?= assets("scripts/pos.js") ?>"></script>

<?php include RUTA_APP . "/includes/footer.php"; ?>
