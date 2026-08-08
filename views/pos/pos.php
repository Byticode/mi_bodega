<?php
$page_title = 'Punto de venta';
$page_desc  = 'Registra ventas de mostrador y cobra el ticket.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';

$tasa_usd = isset($tasa['tasa_usd']) ? (float) $tasa['tasa_usd'] : 0;
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--wide">

    <?php include ruta . '/includes/flash.php'; ?>

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
              <a href="index.php?controller=productosController&action=crear" class="btn btn-primary mt-3">Crear producto</a>
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
        <form id="ventaForm" method="POST" action="index.php?controller=ventasController&action=crear" class="flex flex-col min-h-0">

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
                  <a href="index.php?controller=tasaMonedaController&action=listar" class="underline font-semibold">Configúrala aquí.</a>
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

          <!-- Aviso accesible: anuncia altas, bajas y errores de stock -->
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
  (function () {
    'use strict';

    var TASA_USD = <?= json_encode($tasa_usd) ?>;

    var carrito = [];
    var grid = document.getElementById('productosGrid');
    var contenedor = document.getElementById('carritoContainer');
    var aviso = document.getElementById('posAviso');
    var inputProductos = document.getElementById('productosInput');
    var cobrarBtn = document.getElementById('cobrarBtn');
    var vaciarBtn = document.getElementById('vaciarBtn');
    var buscador = document.getElementById('buscarProducto');
    var sinResultados = document.getElementById('sinResultados');
    var contador = document.getElementById('contadorProductos');

    function bs(n) {
      return 'Bs ' + n.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function anunciar(texto) {
      aviso.textContent = texto;
    }

    /* ── Búsqueda ─────────────────────────────────────────────────────── */
    function filtrar() {
      if (!grid) return;
      var q = buscador.value.trim().toLowerCase();
      var visibles = 0;

      Array.prototype.forEach.call(grid.querySelectorAll('.pos-tile'), function (tile) {
        var coincide = !q || tile.dataset.buscar.indexOf(q) !== -1;
        tile.hidden = !coincide;
        if (coincide) visibles++;
      });

      sinResultados.hidden = visibles > 0;
      contador.textContent = visibles === 1 ? '1 producto' : visibles + ' productos';
    }

    if (buscador) {
      buscador.addEventListener('input', filtrar);

      // Enter con un solo resultado visible lo agrega: el flujo de caja
      // se hace con las dos manos en el teclado, no con el ratón.
      buscador.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        var visibles = grid ? Array.prototype.filter.call(
          grid.querySelectorAll('.pos-tile'), function (t) { return !t.hidden; }
        ) : [];
        if (visibles.length === 1) {
          agregar(visibles[0]);
          buscador.select();
        }
      });
    }

    /* ── Carrito ──────────────────────────────────────────────────────── */
    function agregar(tile) {
      var id = Number(tile.dataset.id);
      var stock = Number(tile.dataset.stock);
      var nombre = tile.dataset.nombre;

      if (stock <= 0) {
        anunciar(nombre + ' está agotado.');
        return;
      }

      var linea = carrito.find(function (item) { return item.id === id; });

      if (linea) {
        if (linea.cantidad >= stock) {
          anunciar('No hay más stock de ' + nombre + '. Máximo ' + stock + '.');
          return;
        }
        linea.cantidad++;
      } else {
        carrito.push({
          id: id,
          nombre: nombre,
          precio: Number(tile.dataset.precio),
          unidad: tile.dataset.unidad,
          stock: stock,
          cantidad: 1
        });
      }

      anunciar(nombre + ' agregado al ticket.');
      pintar();
    }

    function cambiar(id, delta) {
      var i = carrito.findIndex(function (item) { return item.id === id; });
      if (i === -1) return;

      var nueva = carrito[i].cantidad + delta;

      if (nueva <= 0) {
        anunciar(carrito[i].nombre + ' eliminado del ticket.');
        carrito.splice(i, 1);
      } else if (nueva > carrito[i].stock) {
        anunciar('No hay más stock de ' + carrito[i].nombre + '. Máximo ' + carrito[i].stock + '.');
        return;
      } else {
        carrito[i].cantidad = nueva;
      }

      pintar();
    }

    function eliminar(id) {
      var i = carrito.findIndex(function (item) { return item.id === id; });
      if (i === -1) return;
      anunciar(carrito[i].nombre + ' eliminado del ticket.');
      carrito.splice(i, 1);
      pintar();
    }

    function pintar() {
      var total = 0;
      var articulos = 0;

      if (!carrito.length) {
        contenedor.innerHTML = '<p class="empty-sub text-center py-10 mx-auto">El ticket está vacío.</p>';
      } else {
        contenedor.textContent = '';

        carrito.forEach(function (item) {
          var subtotal = item.cantidad * item.precio;
          total += subtotal;
          articulos += item.cantidad;

          var fila = document.createElement('div');
          fila.className = 'pos-line';

          var info = document.createElement('div');
          info.className = 'flex-1 min-w-0';
          var nombre = document.createElement('p');
          nombre.className = 'pos-line-name';
          nombre.textContent = item.nombre;
          var unit = document.createElement('p');
          unit.className = 'pos-line-unit';
          unit.textContent = bs(item.precio) + ' / ' + item.unidad;
          info.appendChild(nombre);
          info.appendChild(unit);

          var acciones = document.createElement('div');
          acciones.className = 'flex items-center gap-2 shrink-0';

          var qty = document.createElement('div');
          qty.className = 'qty';

          var menos = document.createElement('button');
          menos.type = 'button';
          menos.setAttribute('aria-label', 'Quitar una unidad de ' + item.nombre);
          menos.innerHTML = '<i class="ti ti-minus text-sm" aria-hidden="true"></i>';
          menos.addEventListener('click', function () { cambiar(item.id, -1); });

          var salida = document.createElement('output');
          salida.setAttribute('aria-label', 'Cantidad de ' + item.nombre);
          salida.textContent = item.cantidad;

          var mas = document.createElement('button');
          mas.type = 'button';
          mas.setAttribute('aria-label', 'Agregar una unidad de ' + item.nombre);
          mas.disabled = item.cantidad >= item.stock;
          mas.innerHTML = '<i class="ti ti-plus text-sm" aria-hidden="true"></i>';
          mas.addEventListener('click', function () { cambiar(item.id, 1); });

          qty.appendChild(menos);
          qty.appendChild(salida);
          qty.appendChild(mas);

          var monto = document.createElement('span');
          monto.className = 'pos-line-total';
          monto.textContent = bs(subtotal);

          var quitar = document.createElement('button');
          quitar.type = 'button';
          quitar.className = 'btn-icon btn-icon--danger';
          quitar.setAttribute('aria-label', 'Eliminar ' + item.nombre + ' del ticket');
          quitar.innerHTML = '<i class="ti ti-x text-base" aria-hidden="true"></i>';
          quitar.addEventListener('click', function () { eliminar(item.id); });

          acciones.appendChild(qty);
          acciones.appendChild(monto);
          acciones.appendChild(quitar);

          fila.appendChild(info);
          fila.appendChild(acciones);
          contenedor.appendChild(fila);
        });
      }

      document.getElementById('totalCarrito').textContent = bs(total);
      document.getElementById('totalProductos').textContent = articulos;

      var totalUsd = document.getElementById('totalUsd');
      if (totalUsd && TASA_USD > 0) {
        var usd = (total / TASA_USD).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        totalUsd.firstChild.textContent = '≈ $ ' + usd + ' ';
      }

      inputProductos.value = JSON.stringify(carrito.map(function (item) {
        return { id: item.id, cantidad: item.cantidad, precio: item.precio };
      }));

      cobrarBtn.disabled = carrito.length === 0;
      vaciarBtn.hidden = carrito.length === 0;
    }

    if (grid) {
      grid.addEventListener('click', function (e) {
        var tile = e.target.closest('.pos-tile');
        if (tile && !tile.disabled) agregar(tile);
      });
    }

    vaciarBtn.addEventListener('click', function () {
      carrito = [];
      anunciar('Ticket vaciado.');
      pintar();
      if (buscador) buscador.focus();
    });

    /* ── Estado de la venta ───────────────────────────────────────────── */
    var estado = document.getElementById('estado_venta');
    var metodoBox = document.getElementById('metodoPagoContainer');
    var numeroBox = document.getElementById('numeroPagoContainer');
    var metodo = document.getElementById('metodo_pago');
    var numero = document.getElementById('numero_pago');

    function sincronizarEstado() {
      var completada = estado.value === 'completada';
      metodoBox.hidden = !completada;
      numeroBox.hidden = !completada;
      // Deshabilitados además de ocultos: una venta pendiente no debe
      // guardar un método de pago que el cajero nunca eligió.
      metodo.disabled = !completada;
      numero.disabled = !completada;
      cobrarBtn.textContent = completada ? 'Cobrar ticket' : 'Guardar como pendiente';
    }

    estado.addEventListener('change', sincronizarEstado);
    sincronizarEstado();
    pintar();
  })();
</script>

<?php include ruta . '/includes/footer.php'; ?>
