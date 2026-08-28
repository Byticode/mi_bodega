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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

      <!-- ════════════════════════════════════════════════════════════════════════
           COLUMNA IZQUIERDA / PRINCIPAL: Catálogo en Lista (Arriba) + Carrito (Abajo)
           ════════════════════════════════════════════════════════════════════════ -->
      <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-6 min-w-0">

        <!-- ══ SECCIÓN 1: Catálogo de productos en MODO LISTA ══ -->
        <section class="card flex flex-col overflow-hidden" aria-labelledby="tituloCatalogo">
          <div class="card-head flex flex-wrap items-center justify-between gap-3 border-b border-rule p-4">
            <div>
              <h1 class="page-title text-lg md:text-xl font-semibold" id="tituloCatalogo">Punto de venta</h1>
              <p class="text-xs text-ink-3 mt-0.5">Selecciona productos de la lista para agregarlos al ticket.</p>
            </div>
            <span class="badge badge-neutral" id="contadorProductos" role="status">
              <?= count($productos) ?> <?= count($productos) === 1 ? 'producto' : 'productos' ?>
            </span>
          </div>

          <!-- Barra de búsqueda rápida -->
          <div class="p-3 border-b border-rule bg-card-2/40">
            <div class="search w-full relative flex items-center">
              <label for="buscarProducto" class="sr-only">Buscar producto por nombre o código</label>
              <i class="ti ti-search search-icon" aria-hidden="true"></i>
              <input type="text" id="buscarProducto" class="input bg-card pl-9 pr-8 w-full"
                placeholder="Buscar por nombre, código o categoría…" autocomplete="off" autofocus>
              <button type="button" id="btnLimpiarBuscarProducto"
                class="btn-clear-search absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-3 hover:text-ink hover:bg-card-2 p-1 rounded-full flex items-center justify-center transition-colors cursor-pointer"
                title="Limpiar búsqueda" aria-label="Limpiar búsqueda" style="display: none;">
                <i class="ti ti-x text-xs cursor-pointer text-red-500"></i>
              </button>
            </div>
          </div>

          <!-- Listado de productos en modo lista con scroll -->
          <div class="p-3">
            <?php if (empty($productos)): ?>
              <div class="empty py-10">
                <i class="ti ti-package empty-icon" aria-hidden="true"></i>
                <p class="empty-title">Todavía no hay productos con stock</p>
                <p class="empty-sub">Registra o surte mercancía en el inventario para poder venderla desde aquí.</p>
                <a href="<?= url("productos/crear") ?>" class="btn btn-primary mt-3">Crear producto</a>
              </div>
            <?php else: ?>
              <div id="productosGrid" class="pos-scroll-area flex flex-col gap-2 pr-1 min-h-65 max-h-85 overflow-y-auto">
                <?php foreach ($productos as $producto):
                  $stock = (int) ($producto["producto_stock"] ?? 0);
                  $abrev = !empty($producto["unidad_abreviatura"]) ? $producto["unidad_abreviatura"] : "unid.";
                  $catNombre = !empty($producto["categorias_nombre"]) ? $producto["categorias_nombre"] : null;
                  $cod = !empty($producto["producto_codigo"]) ? $producto["producto_codigo"] : "";
                  ?>
                  <div class="pos-list-item group p-3 cursor-pointer" role="button" tabindex="0"
                    data-id="<?= (int) $producto["producto_id"] ?>"
                    data-nombre="<?= htmlspecialchars($producto["producto_nombre"], ENT_QUOTES) ?>"
                    data-precio="<?= (float) $producto["producto_precio_venta"] ?>"
                    data-unidad="<?= htmlspecialchars($abrev, ENT_QUOTES) ?>" data-stock="<?= $stock ?>"
                    data-buscar="<?= htmlspecialchars(mb_strtolower($producto["producto_nombre"] . " " . $cod . " " . ($catNombre ?? "")), ENT_QUOTES) ?>"
                    <?= $stock <= 0 ? 'aria-disabled="true"' : "" ?>>

                    <!-- Información principal del producto -->
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                      <div
                        class="w-8 h-8 rounded-lg bg-card-2 flex items-center justify-center text-ink-3 shrink-0 group-hover:bg-olive-light group-hover:text-olive transition-colors">
                        <i class="ti ti-box text-base" aria-hidden="true"></i>
                      </div>
                      <div class="flex flex-col min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                          <span
                            class="font-medium text-sm text-ink truncate"><?= htmlspecialchars($producto["producto_nombre"]) ?></span>
                          <?php if ($catNombre): ?>
                            <span
                              class="text-[11px] px-1.5 py-0.5 rounded bg-card-2 text-ink-3 hidden sm:inline-block"><?= htmlspecialchars($catNombre) ?></span>
                          <?php endif; ?>
                          <!-- Badge de producto seleccionado en ticket -->
                          <span
                            class="pos-cart-badge badge badge-olive text-[11px] py-0.5 px-2 items-center gap-1 font-semibold"
                            data-badge-id="<?= (int) $producto["producto_id"] ?>" hidden>
                            <i class="ti ti-check text-xs" aria-hidden="true"></i>
                            <span class="badge-qty">0</span> en ticket
                          </span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5 text-xs text-ink-3">
                          <?php if ($cod): ?>
                            <span class="font-mono text-[11px] text-ink-3">#<?= htmlspecialchars($cod) ?></span>
                            <span>•</span>
                          <?php endif; ?>
                          <?php if ($stock <= 0): ?>
                            <span class="badge badge-danger text-[10px] py-0 px-1.5">Agotado</span>
                          <?php elseif ($stock <= 10): ?>
                            <span class="badge badge-warn text-[10px] py-0 px-1.5">Quedan <?= $stock ?>
                              <?= htmlspecialchars($abrev) ?></span>
                          <?php else: ?>
                            <span class="badge badge-success text-[10px] py-0 px-1.5"><?= $stock ?>
                              <?= htmlspecialchars($abrev) ?></span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>

                    <!-- Precio y controles rápidos -->
                    <div class="flex items-center gap-2 shrink-0 pl-2">
                      <div class="text-right mr-1">
                        <span
                          class="font-mono font-semibold text-sm text-ink block"><?= usd($producto["producto_precio_venta"]) ?></span>
                        <span
                          class="font-mono text-xs text-ink-3 block"><?= bs($producto["producto_precio_venta"]) ?></span>
                      </div>
                      <button type="button"
                        class="btn-info-producto-pos cursor-pointer w-7 h-7 rounded-md bg-card-2 flex items-center justify-center text-ink-3 hover:text-olive hover:bg-olive/10 transition-colors"
                        data-producto='<?= htmlspecialchars(json_encode([
                          "id" => (int) $producto["producto_id"],
                          "nombre" => $producto["producto_nombre"],
                          "codigo" => $cod,
                          "categoria" => $catNombre ?? "Sin categoría",
                          "unidad" => $abrev,
                          "peso" => $producto["producto_peso"] ?? "",
                          "precio_usd" => usd($producto["producto_precio_venta"]),
                          "precio_bs" => bs($producto["producto_precio_venta"]),
                          "stock" => $stock,
                        ]), ENT_QUOTES) ?>' aria-label="Ver detalles del producto" title="Ver detalles">
                        <i class="ti ti-eye text-sm" aria-hidden="true"></i>
                      </button>
                      <button type="button"
                        class="cursor-pointer pos-quick-btn pos-quick-minus w-7 h-7 rounded-md bg-card-2 flex items-center justify-center text-ink-3 hover:bg-danger-bg hover:text-danger transition-colors"
                        data-action="minus" data-pid="<?= (int) $producto["producto_id"] ?>" aria-label="Quitar una unidad"
                        title="Quitar" hidden>
                        <i class="ti ti-minus text-xs" aria-hidden="true"></i>
                      </button>
                      <button type="button"
                        class="cursor-pointer pos-quick-btn pos-quick-plus w-7 h-7 rounded-md bg-card-2 flex items-center justify-center text-ink-2 hover:bg-olive hover:text-white transition-colors"
                        data-action="plus" data-pid="<?= (int) $producto["producto_id"] ?>" aria-label="Agregar una unidad"
                        title="Agregar">
                        <i class="ti ti-plus text-xs" aria-hidden="true"></i>
                      </button>
                    </div>

                  </div>
                <?php endforeach; ?>
              </div>

              <div id="sinResultados" class="card my-2 border-dashed" hidden>
                <div class="empty py-8">
                  <i class="ti ti-search-off empty-icon" aria-hidden="true"></i>
                  <p class="empty-title">Sin coincidencias</p>
                  <p class="empty-sub">Ningún producto coincide con la búsqueda. Prueba con otro término.</p>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <!-- Barra de paginación del catálogo -->
          <div class="p-2.5 px-4 border-t border-rule bg-card-2/30 flex items-center justify-between gap-3 text-xs"
            id="paginacionPos">
            <span class="text-ink-3" id="infoPaginacion">Mostrando productos</span>
            <div class="flex items-center gap-1.5" id="controlesPaginacion">
              <button type="button" class="btn btn-secondary btn-sm px-2 py-1 text-xs" id="btnPaginaAnt" disabled
                aria-label="Página anterior">
                <i class="ti ti-chevron-left text-xs" aria-hidden="true"></i>
                <span class="hidden sm:inline">Anterior</span>
              </button>
              <div class="flex items-center gap-1" id="numerosPagina"></div>
              <button type="button" class="btn btn-secondary btn-sm px-2 py-1 text-xs" id="btnPaginaSig" disabled
                aria-label="Página siguiente">
                <span class="hidden sm:inline">Siguiente</span>
                <i class="ti ti-chevron-right text-xs" aria-hidden="true"></i>
              </button>
            </div>
          </div>
        </section>

        <!-- ══ SECCIÓN 2: Carrito / Ticket de productos (ABAJO) ══ -->
        <section class="card flex flex-col overflow-hidden" aria-labelledby="tituloCarrito">
          <div class="card-head flex items-center justify-between p-3.5 px-4 border-b border-rule">
            <div class="flex items-center gap-2">
              <i class="ti ti-shopping-cart text-lg text-olive" aria-hidden="true"></i>
              <h2 class="section-title text-sm sm:text-base font-semibold" id="tituloCarrito">Productos en ticket</h2>
            </div>
            <button title="Vaciar el Carrito" type="button" id="vaciarBtn"
              class="btn btn-ghost btn-sm text-danger hover:bg-danger-bg text-xs cursor-pointer" hidden>
              <i class="ti ti-trash text-sm mr-1 cursor-pointer" aria-hidden="true"></i>
              Vaciar ticket
            </button>
          </div>

          <!-- Contenedor con scroll y altura limitada para las líneas del ticket -->
          <div class="pos-scroll-area min-h-40 max-h-55 overflow-y-auto flex flex-col" id="carritoContainer">
            <div class="empty py-10 my-auto text-center" id="carritoVacio">
              <i class="cursor-pointer  ti ti-shopping-cart-x empty-icon text-ink-3 opacity-40 text-3xl mb  -2"
                aria-hidden="true"></i>
              <p class="empty-title text-sm">El ticket está vacío</p>
              <p class="empty-sub text-xs">Toca o haz clic en cualquier producto de la lista superior para agregarlo.
              </p>
            </div>
          </div>
        </section>

      </div>

      <!-- ════════════════════════════════════════════════════════════════════════
           SECCIÓN 3: BARRA LATERAL DERECHA (Panel de Cobro y Acciones)
           ════════════════════════════════════════════════════════════════════════ -->
      <aside class="lg:col-span-5 xl:col-span-4 flex flex-col gap-4">

        <!-- Tarjeta de Cobro / Formulario de Venta (Estática) -->
        <section class="card pos-ticket overflow-hidden" aria-labelledby="tituloTicket">
          <form id="ventaForm" method="POST" action="<?= url("ventas/crear") ?>" class="flex flex-col">

            <div class="card-head p-4 border-b border-rule bg-card">
              <div class="flex items-center gap-2">
                <i class="ti ti-receipt text-lg text-olive" aria-hidden="true"></i>
                <h2 class="section-title text-base font-semibold" id="tituloTicket">Resumen de cobro</h2>
              </div>
            </div>

            <?php if ($tasa_usd <= 0): ?>
              <div class="p-4 pb-0">
                <div class="alert alert-warn" role="status">
                  <i class="ti ti-alert-circle shrink-0 text-lg" aria-hidden="true"></i>
                  <div class="flex-1 text-xs">
                    <span class="font-semibold block">Sin tasa de cambio activa.</span>
                    No se podrá procesar la venta.
                    <a href="<?= url("tasa-moneda") ?>" class="underline font-semibold block mt-0.5">Configurar tasa
                      aquí</a>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <div class="p-4 flex flex-col gap-3.5 border-b border-rule">

              <!-- Selector y Buscador de Cliente -->
              <div class="field">
                <div class="flex items-center justify-between mb-1">
                  <label for="cliente_id" class="label text-xs font-semibold text-ink-2 mb-0">Cliente</label>
                  <button type="button" id="btnNuevoCliente" title="Registrar un cliente"
                    class="cursor-pointer text-xs text-olive font-semibold hover:underline flex items-center gap-1">
                    <i class="ti ti-user-plus text-xs cursor-pointer" aria-hidden="true"></i> + Registrar
                  </button>
                </div>

                <!-- Buscador de cliente por nombre o cédula -->
                <div class="relative mb-1.5 flex items-center">
                  <i class="ti ti-search absolute left-2.5 top-1/2 -translate-y-1/2 text-ink-3 text-xs pointer-events-none"
                    aria-hidden="true"></i>
                  <input type="text" id="buscarCliente" class="input text-xs pl-7 pr-8 py-1.5 bg-card-2/40 w-full"
                    placeholder="Filtrar por nombre o cédula…" autocomplete="off">
                  <button type="button" id="btnLimpiarBuscarCliente"
                    class="btn-clear-search absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-3 hover:text-ink hover:bg-card-2 p-1 rounded-full flex items-center justify-center transition-colors cursor-pointer"
                    title="Limpiar búsqueda" aria-label="Limpiar búsqueda" style="display: none;">
                    <i class="ti ti-x text-xs cursor-pointer text-red-500"></i>
                  </button>
                </div>

                <select id="cliente_id" name="cliente_id" class="select text-sm">
                  <option value="" data-buscar="">Seleccione un cliente</option>
                  <?php foreach ($clientes as $cliente):
                    $cedula = !empty($cliente["cliente_cedula"]) ? $cliente["cliente_cedula"] : "";
                    $nomCompleto = $cliente["cliente_nombre"] . " " . $cliente["cliente_apellido"];
                    $dataBuscar = mb_strtolower($nomCompleto . " " . $cedula);
                    ?>
                    <option value="<?= (int) $cliente["cliente_id"] ?>"
                      data-cedula="<?= htmlspecialchars($cedula, ENT_QUOTES) ?>"
                      data-buscar="<?= htmlspecialchars($dataBuscar, ENT_QUOTES) ?>">
                      <?= htmlspecialchars($nomCompleto) ?>   <?= $cedula ? " (V-" . htmlspecialchars($cedula) . ")" : "" ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Estado de la venta -->
              <div class="field">
                <label for="estado_venta" class="label text-xs font-semibold text-ink-2">Estado de la venta</label>
                <select id="estado_venta" name="estado" class="select text-sm">
                  <option value="completada" selected>Completada — se cobra ahora</option>
                  <option value="pendiente">Pendiente — cobrar después</option>
                </select>
              </div>

              <!-- Selector de Método de Pago -->
              <div class="field" id="metodoPagoContainer">
                <span class="label text-xs font-semibold text-ink-2 block mb-1.5">Método de pago</span>
                <select id="metodo_pago" name="metodo_pago" class="sr-only" aria-hidden="true" tabindex="-1">
                  <option value="efectivo" selected>Efectivo</option>
                  <option value="transferencia">Punto de venta</option>
                  <option value="pago_movil">Pago móvil / Transf</option>
                  <option value="biopago">Biopago</option>
                  <option value="cashea">Cashea</option>
                </select>

                <div class="grid grid-cols-3 gap-2" role="radiogroup" aria-label="Método de pago">
                  <!-- Efectivo -->
                  <button type="button" class="payment-card" data-value="efectivo" role="radio" aria-checked="true">
                    <i class="ti ti-cash text-lg mb-1" aria-hidden="true"></i>
                    <span class="text-xs font-medium leading-tight">Efectivo</span>
                  </button>

                  <!-- Punto de venta -->
                  <button type="button" class="payment-card" data-value="transferencia" role="radio"
                    aria-checked="false">
                    <i class="ti ti-credit-card text-lg mb-1" aria-hidden="true"></i>
                    <span class="text-xs font-medium leading-tight">Punto</span>
                  </button>

                  <!-- Pago Movil / Transf -->
                  <button type="button" class="payment-card" data-value="pago_movil" role="radio" aria-checked="false">
                    <i class="ti ti-device-mobile text-lg mb-1" aria-hidden="true"></i>
                    <span class="text-xs font-medium leading-tight">Pago Móvil</span>
                  </button>

                  <!-- Biopago -->
                  <button type="button" class="payment-card" data-value="biopago" role="radio" aria-checked="false">
                    <i class="ti ti-fingerprint text-lg mb-1" aria-hidden="true"></i>
                    <span class="text-xs font-medium leading-tight">Biopago</span>
                  </button>

                  <!-- Cashea -->
                  <button type="button" class="payment-card" data-value="cashea" role="radio" aria-checked="false">
                    <span class="text-base font-extrabold leading-none mb-1">C</span>
                    <span class="text-xs font-medium leading-tight">Cashea</span>
                  </button>
                </div>
              </div>

              <!-- Número de Referencia (condicional) -->
              <div class="field" id="numeroPagoContainer" hidden>
                <label for="numero_pago" class="label text-xs font-semibold text-ink-2">Número de referencia</label>
                <input type="text" id="numero_pago" name="numero_pago" class="input text-sm" placeholder="Ej. 123456"
                  autocomplete="off" disabled>
              </div>

            </div>

            <!-- Aviso accesible -->
            <p id="posAviso" class="sr-only" role="status" aria-live="polite"></p>

            <!-- Resumen de Totales y Acción de Cobro -->
            <div class="p-4 bg-card-2/30 flex flex-col gap-3">

              <div class="flex items-center justify-between text-xs text-ink-3">
                <span>Cantidad de artículos:</span>
                <span class="font-mono font-semibold text-ink" id="totalProductos">0</span>
              </div>

              <div class="pos-total pt-1">
                <span class="pos-total-label text-base">Total a pagar</span>
                <span class="pos-total-value text-xl font-bold font-mono" id="totalCarrito"><?= usd(0) ?></span>
              </div>

              <?php if ($tasa_usd > 0): ?>
                <div class="flex items-center justify-between text-xs text-ink-3 pb-1">
                  <span>Equivalente en Bs:</span>
                  <span class="font-mono font-medium text-ink-2" id="totalUsd">
                    Bs 0,00 <span class="text-[11px] text-ink-3">· BCV <?= money($tasa_usd) ?>/$</span>
                  </span>
                </div>
              <?php endif; ?>

              <input type="hidden" name="productos" id="productosInput" value="[]">

              <button type="submit" class="btn btn-primary btn-lg btn-block mt-2 font-semibold shadow-sm" id="cobrarBtn"
                disabled>
                <i class="ti ti-check text-base mr-1" aria-hidden="true"></i>
                Cobrar ticket
              </button>
            </div>

          </form>
        </section>

        <!-- Tarjeta de Utilidades y Acciones Rápidas -->
        <section class="card p-3 flex flex-col gap-2 bg-card" aria-label="Herramientas del punto de venta">
          <span class="text-[11px] font-semibold text-ink-3 uppercase tracking-wider px-1">Acciones rápidas</span>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-2">
            <!-- Botón 1: Registrar Cliente Rápido -->
            <button type="button" id="btnModalClienteDirecto" title="Registrar un cliente rápido"
              class="btn btn-secondary btn-sm justify-start text-xs font-medium w-full cursor-pointer">
              <i class="ti ti-user-plus text-base text-olive cursor-pointer" aria-hidden="true"></i>
              <span>Registrar cliente</span>
            </button>

            <!-- Botón 2: Actualizar Tasa del Día -->
            <button type="button" id="btnRefrescarTasa" title="Actualizar tasa del BCV"
              class="btn btn-secondary btn-sm justify-start text-xs font-medium w-full cursor-pointer">
              <i class="ti ti-refresh text-base text-info cursor-pointer" id="iconoRefrescarTasa"
                aria-hidden="true"></i>
              <span id="textoRefrescarTasa">Actualizar tasa BCV</span>
            </button>

            <!-- Botón 3: Vista Previa / Imprimir Ticket -->
            <button type="button" id="btnPreviewTicket" title="Vista previa del ticket"
              class="btn btn-secondary btn-sm justify-start text-xs font-medium w-full cursor-pointer">
              <i class="ti ti-printer text-base text-ink-2 cursor-pointer" aria-hidden="true"></i>
              <span>Vista previa de ticket</span>
            </button>
          </div>
        </section>

      </aside>

    </div>
  </div>
</main>

<!-- ════════════════════════════════════════════════════════════════════════
     MODAL 1: Registrar Cliente Rápido
     ════════════════════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalCliente" aria-hidden="true" role="dialog" aria-modal="true"
  aria-labelledby="tituloModalCliente">
  <div class="modal-card">
    <div class="flex items-center justify-between p-4 border-b border-rule">
      <div class="flex items-center gap-2">
        <i class="ti ti-user-plus text-lg text-olive" aria-hidden="true"></i>
        <h3 class="font-semibold text-sm text-ink" id="tituloModalCliente">Registrar nuevo cliente</h3>
      </div>
      <button type="button" class="btn btn-ghost btn-sm p-1 text-ink-3 hover:text-ink" id="btnCerrarModalCliente"
        aria-label="Cerrar modal">
        <i class="ti ti-x text-base" aria-hidden="true"></i>
      </button>
    </div>

    <form id="formClienteRapido" data-ajax="true" class="p-4 flex flex-col gap-3">
      <div id="alertaClienteRapido" class="alert alert-error text-xs" hidden></div>

      <div class="grid grid-cols-2 gap-2.5">
        <div class="field">
          <label for="cliente_nombre_modal" class="label text-xs">Nombre <span class="text-danger">*</span></label>
          <input type="text" id="cliente_nombre_modal" name="nombre" class="input text-xs" placeholder="Ej. Juan"
            required>
        </div>
        <div class="field">
          <label for="cliente_apellido_modal" class="label text-xs">Apellido <span class="text-danger">*</span></label>
          <input type="text" id="cliente_apellido_modal" name="apellido" class="input text-xs" placeholder="Ej. Pérez"
            required>
        </div>
      </div>

      <div class="field">
        <label for="cliente_cedula_modal" class="label text-xs">Cédula de identidad <span
            class="text-danger">*</span></label>
        <input type="text" id="cliente_cedula_modal" name="cedula" class="input text-xs" placeholder="Ej. 12345678"
          required>
      </div>

      <div class="grid grid-cols-2 gap-2.5">
        <div class="field">
          <label for="cliente_telefono_modal" class="label text-xs">Teléfono</label>
          <input type="text" id="cliente_telefono_modal" name="telefono" class="input text-xs"
            placeholder="Ej. 04141234567">
        </div>
        <div class="field">
          <label for="cliente_correo_modal" class="label text-xs">Correo</label>
          <input type="email" id="cliente_correo_modal" name="correo" class="input text-xs"
            placeholder="juan@correo.com">
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 pt-3 border-t border-rule">
        <button type="button" class="btn btn-secondary btn-sm text-xs" id="btnCancelarCliente">Cancelar</button>
        <button type="submit" class="btn btn-primary btn-sm text-xs font-semibold" id="btnGuardarCliente">
          <i class="ti ti-check text-xs mr-1" aria-hidden="true"></i> Guardar y seleccionar
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════
     MODAL 2: Vista Previa e Impresión de Ticket
     ════════════════════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalTicketPreview" aria-hidden="true" role="dialog" aria-modal="true"
  aria-labelledby="tituloModalTicket">
  <div class="modal-card max-w-sm">
    <div class="flex items-center justify-between p-3.5 border-b border-rule">
      <div class="flex items-center gap-2">
        <i class="ti ti-printer text-base text-olive" aria-hidden="true"></i>
        <h3 class="font-semibold text-sm text-ink" id="tituloModalTicket">Vista previa de ticket</h3>
      </div>
      <button type="button" class="btn btn-ghost btn-sm p-1 text-ink-3 hover:text-ink" id="btnCerrarModalTicket"
        aria-label="Cerrar vista previa">
        <i class="ti ti-x text-base" aria-hidden="true"></i>
      </button>
    </div>

    <!-- Área de impresión del ticket -->
    <div class="p-4 bg-card-2/30">
      <div id="ticketPrintArea" class="receipt-paper text-xs leading-relaxed">
        <div class="text-center pb-2 border-b border-dashed border-gray-400">
          <p class="font-bold text-sm tracking-wider uppercase">MI BODEGA</p>
          <p class="text-[11px] text-gray-600">Control de Inventario & POS</p>
          <p class="text-[10px] text-gray-500 mt-1" id="ticketPreviewFecha">Fecha: --/--/----</p>
        </div>

        <div class="py-2 border-b border-dashed border-gray-400 text-[11px]">
          <p><span class="font-semibold">Cliente:</span> <span id="ticketPreviewCliente">Consumidor final</span></p>
          <p><span class="font-semibold">Estado:</span> <span id="ticketPreviewEstado">Completada</span></p>
          <p><span class="font-semibold">Pago:</span> <span id="ticketPreviewMetodo">Efectivo</span></p>
        </div>

        <div class="py-2 border-b border-dashed border-gray-400">
          <div class="grid grid-cols-12 font-bold text-[10px] uppercase mb-1">
            <span class="col-span-6">Cant/Desc</span>
            <span class="col-span-3 text-right">P.U.</span>
            <span class="col-span-3 text-right">Total</span>
          </div>
          <div id="ticketPreviewItems" class="flex flex-col gap-1 text-[11px]">
            <!-- Generado dinámicamente -->
          </div>
        </div>

        <div class="pt-2 flex flex-col gap-1 text-[11px]">
          <div class="flex justify-between font-bold text-sm">
            <span>TOTAL USD:</span>
            <span id="ticketPreviewTotalUsd">$ 0,00</span>
          </div>
          <div class="flex justify-between text-gray-700 font-medium">
            <span>TOTAL BS:</span>
            <span id="ticketPreviewTotalBs">Bs 0,00</span>
          </div>
          <p class="text-[10px] text-gray-500 text-center mt-3 pt-2 border-t border-dashed border-gray-400">
            ¡Gracias por su compra!
          </p>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-between p-3 border-t border-rule bg-card">
      <button type="button" class="btn btn-secondary btn-sm text-xs" id="btnCerrarTicketPreview">Cerrar</button>
      <button type="button" class="btn btn-primary btn-sm text-xs font-semibold" id="btnImprimirTicket">
        <i class="ti ti-printer text-xs mr-1" aria-hidden="true"></i> Imprimir
      </button>
    </div>
  </div>
</div>

<!-- Modal Detalle Resumido de Producto en POS -->
<div id="modalInfoProductoPOS"
  class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 items-center justify-center p-4 overflow-y-auto hidden"
  role="dialog" aria-modal="true" aria-labelledby="posInfoNombre">
  <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-gray-200 p-5 relative my-auto">
    <div class="flex items-start justify-between gap-3 border-b border-gray-100 pb-3">
      <div class="flex items-start gap-2.5">
        <div class="w-9 h-9 rounded-lg bg-olive/10 text-olive flex items-center justify-center shrink-0 mt-0.5">
          <i class="ti ti-box text-xl" aria-hidden="true"></i>
        </div>
        <div>
          <span id="posInfoCategoria" class="badge badge-olive text-[10px] py-0 px-1.5 font-semibold">Categoría</span>
          <h3 id="posInfoNombre" class="text-base font-bold text-gray-900 leading-snug mt-0.5">Nombre del producto</h3>
          <p id="posInfoCodigo" class="text-[11px] font-mono text-gray-500">Código: —</p>
        </div>
      </div>
      <button type="button" id="btnCerrarInfoPOS"
        class="text-gray-400 hover:text-gray-600 p-1 rounded-lg transition-colors cursor-pointer"
        aria-label="Cerrar modal">
        <i class="ti ti-x text-base" aria-hidden="true"></i>
      </button>
    </div>

    <div class="grid grid-cols-2 gap-2.5 my-3.5 text-xs">
      <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-100">
        <span class="text-[10px] font-semibold text-gray-500 uppercase block">Presentación</span>
        <span id="posInfoPresentacion" class="font-medium text-gray-900 block mt-0.5">—</span>
      </div>
      <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-100">
        <span class="text-[10px] font-semibold text-gray-500 uppercase block">Stock disponible</span>
        <span id="posInfoStock" class="font-bold text-gray-900 block mt-0.5">—</span>
      </div>
      <div
        class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-200/70 col-span-2 flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold text-emerald-800 uppercase block">Precio de venta</span>
          <span id="posInfoPrecioBs" class="text-[11px] text-emerald-700 block">Bs 0,00</span>
        </div>
        <span id="posInfoPrecioUsd" class="text-lg font-extrabold text-emerald-900">$ 0,00</span>
      </div>
    </div>

    <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-3">
      <button type="button" id="btnCerrarInfoPOSFooter" class="btn btn-secondary btn-sm cursor-pointer">Cerrar</button>
      <button type="button" id="btnAgregarDesdeInfoPOS"
        class="btn btn-primary btn-sm cursor-pointer flex items-center gap-1">
        <i class="ti ti-plus text-xs" aria-hidden="true"></i>
        <span>Agregar al ticket</span>
      </button>
    </div>
  </div>
</div>

<!-- Contenedor flotante de notificaciones toast -->
<div id="posToastContainer"
  class="fixed top-4 left-1/2 -translate-x-1/2 z-9999 flex flex-col items-center gap-2 pointer-events-none w-full max-w-lg px-4"
  aria-live="polite"></div>

<script>
  window.TASA_USD = <?= json_encode($tasa_usd) ?>;
  window.URL_CREAR_CLIENTE = <?= json_encode(url("clientes/crearRapido")) ?>;
  window.URL_REFRESCAR_TASA = <?= json_encode(url("tasa-moneda/actualizarAjax")) ?>;
</script>
<script src="<?= assets("scripts/pos.js") ?>"></script>

<?php include RUTA_APP . "/includes/footer.php"; ?>