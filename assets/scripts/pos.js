// Punto de Venta (POS) — Lógica interactiva con manejo de stock y tasa de cambio
(function () {
  "use strict";

  var TASA_USD = Number(window.TASA_USD || 0);
  var carrito = [];
  var grid = document.getElementById("productosGrid");
  var contenedor = document.getElementById("carritoContainer");
  var aviso = document.getElementById("posAviso");
  var inputProductos = document.getElementById("productosInput");
  var cobrarBtn = document.getElementById("cobrarBtn");
  var vaciarBtn = document.getElementById("vaciarBtn");
  var buscador = document.getElementById("buscarProducto");
  var sinResultados = document.getElementById("sinResultados");
  var contador = document.getElementById("contadorProductos");
  var estadoVenta = document.getElementById("estado_venta");
  var metodoBox = document.getElementById("metodoPagoContainer");
  var numeroBox = document.getElementById("numeroPagoContainer");
  var metodoInput = document.getElementById("metodo_pago");
  var numeroInput = document.getElementById("numero_pago");

  function bs(n) {
    return (
      "Bs " +
      Number(n).toLocaleString("es-VE", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );
  }

  function usd(n) {
    return (
      "$ " +
      Number(n).toLocaleString("es-VE", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );
  }

  function anunciar(texto) {
    if (aviso) aviso.textContent = texto;
  }

  /* ── Búsqueda ─────────────────────────────────────────────────────── */
  function filtrar() {
    if (!grid || !buscador) return;
    var q = buscador.value.trim().toLowerCase();
    var visibles = 0;

    Array.prototype.forEach.call(
      grid.querySelectorAll(".pos-tile"),
      function (tile) {
        var coincide = !q || (tile.dataset.buscar || "").indexOf(q) !== -1;
        tile.hidden = !coincide;
        if (coincide) visibles++;
      },
    );

    if (sinResultados) sinResultados.hidden = visibles > 0;
    if (contador)
      contador.textContent =
        visibles === 1 ? "1 producto" : visibles + " productos";
  }

  if (buscador) {
    buscador.addEventListener("input", filtrar);
    buscador.addEventListener("keydown", function (e) {
      if (e.key !== "Enter") return;
      e.preventDefault();
      var visibles = grid
        ? Array.prototype.filter.call(
            grid.querySelectorAll(".pos-tile"),
            function (t) {
              return !t.hidden;
            },
          )
        : [];
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
      anunciar(nombre + " está agotado.");
      return;
    }

    var linea = carrito.find(function (item) {
      return item.id === id;
    });

    if (linea) {
      if (linea.cantidad >= stock) {
        anunciar("No hay más stock de " + nombre + ". Máximo " + stock + ".");
        return;
      }
      linea.cantidad++;
    } else {
      carrito.push({
        id: id,
        nombre: nombre,
        precio: Number(tile.dataset.precio),
        unidad: tile.dataset.unidad || "u",
        stock: stock,
        cantidad: 1,
      });
    }

    anunciar(nombre + " agregado al ticket.");
    pintar();
  }

  function cambiar(id, delta) {
    var i = carrito.findIndex(function (item) {
      return item.id === id;
    });
    if (i === -1) return;

    var nueva = carrito[i].cantidad + delta;

    if (nueva <= 0) {
      anunciar(carrito[i].nombre + " eliminado del ticket.");
      carrito.splice(i, 1);
    } else if (nueva > carrito[i].stock) {
      anunciar(
        "No hay más stock de " +
          carrito[i].nombre +
          ". Máximo " +
          carrito[i].stock +
          ".",
      );
      return;
    } else {
      carrito[i].cantidad = nueva;
    }

    pintar();
  }

  function eliminar(id) {
    var i = carrito.findIndex(function (item) {
      return item.id === id;
    });
    if (i === -1) return;
    anunciar(carrito[i].nombre + " eliminado del ticket.");
    carrito.splice(i, 1);
    pintar();
  }

  function pintar() {
    var total = 0;
    var articulos = 0;

    if (!contenedor) return;

    if (!carrito.length) {
      contenedor.innerHTML =
        '<p class="empty-sub text-center py-10 mx-auto">El ticket está vacío.</p>';
    } else {
      contenedor.textContent = "";

      carrito.forEach(function (item) {
        var subtotal = item.cantidad * item.precio;
        total += subtotal;
        articulos += item.cantidad;

        var fila = document.createElement("div");
        fila.className =
          "pos-line flex justify-between items-center border-b border-gray-100 py-2 text-xs gap-2";

        var info = document.createElement("div");
        info.className = "flex-1 min-w-0";
        var nombre = document.createElement("p");
        nombre.className = "font-semibold text-gray-900 truncate";
        nombre.textContent = item.nombre;
        var unit = document.createElement("p");
        unit.className = "text-gray-500 text-[11px]";
        unit.textContent = usd(item.precio) + " / " + item.unidad;
        info.appendChild(nombre);
        info.appendChild(unit);

        var acciones = document.createElement("div");
        acciones.className = "flex items-center gap-2 shrink-0";

        var qty = document.createElement("div");
        qty.className =
          "flex items-center border border-gray-200 rounded overflow-hidden";

        var menos = document.createElement("button");
        menos.type = "button";
        menos.className =
          "px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600";
        menos.setAttribute("aria-label", "Quitar una unidad de " + item.nombre);
        menos.textContent = "-";
        menos.addEventListener("click", function () {
          cambiar(item.id, -1);
        });

        var salida = document.createElement("span");
        salida.className = "px-2 text-xs font-bold min-w-[28px] text-center";
        salida.textContent = item.cantidad;

        var mas = document.createElement("button");
        mas.type = "button";
        mas.className =
          "px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600";
        mas.setAttribute("aria-label", "Agregar una unidad de " + item.nombre);
        mas.disabled = item.cantidad >= item.stock;
        mas.textContent = "+";
        mas.addEventListener("click", function () {
          cambiar(item.id, 1);
        });

        qty.appendChild(menos);
        qty.appendChild(salida);
        qty.appendChild(mas);

        var monto = document.createElement("div");
        monto.className = "text-right min-w-[80px]";
        monto.innerHTML =
          '<span class="font-bold block text-gray-900">' +
          usd(subtotal) +
          "</span>" +
          '<span class="text-[10px] text-gray-500 font-mono block">' +
          bs(subtotal * TASA_USD) +
          "</span>";

        var quitar = document.createElement("button");
        quitar.type = "button";
        quitar.className = "text-rose-600 hover:text-rose-800 p-1";
        quitar.setAttribute(
          "aria-label",
          "Eliminar " + item.nombre + " del ticket",
        );
        quitar.innerHTML =
          '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
        quitar.addEventListener("click", function () {
          eliminar(item.id);
        });

        acciones.appendChild(qty);
        acciones.appendChild(monto);
        acciones.appendChild(quitar);

        fila.appendChild(info);
        fila.appendChild(acciones);
        contenedor.appendChild(fila);
      });
    }

    var totalEl = document.getElementById("totalCarrito");
    if (totalEl) totalEl.textContent = usd(total);

    var articulosEl = document.getElementById("totalProductos");
    if (articulosEl) articulosEl.textContent = articulos;

    var totalUsd = document.getElementById("totalUsd");
    if (totalUsd && TASA_USD > 0) {
      totalUsd.innerHTML =
        "≈ " +
        bs(total * TASA_USD) +
        ' <span class="text-ink-3">· tasa BCV ' +
        bs(TASA_USD) +
        "/$</span>";
    }

    if (inputProductos) {
      inputProductos.value = JSON.stringify(
        carrito.map(function (item) {
          return { id: item.id, cantidad: item.cantidad, precio: item.precio };
        }),
      );
    }

    if (cobrarBtn) cobrarBtn.disabled = carrito.length === 0;
    if (vaciarBtn) vaciarBtn.hidden = carrito.length === 0;
  }

  if (grid) {
    grid.addEventListener("click", function (e) {
      var tile = e.target.closest(".pos-tile");
      if (tile && !tile.disabled) agregar(tile);
    });
  }

  if (vaciarBtn) {
    vaciarBtn.addEventListener("click", function () {
      carrito = [];
      anunciar("Ticket vaciado.");
      pintar();
      if (buscador) buscador.focus();
    });
  }

  var METODOS_CON_REFERENCIA = ["pago_movil", "cashea"];

  function actualizarCardsPago() {
    if (!metodoInput) return;
    var valorActual = metodoInput.value;
    var cards = document.querySelectorAll(".payment-card");

    cards.forEach(function (card) {
      var val = card.dataset.value;
      var esActivo = val === valorActual;
      card.setAttribute("aria-checked", esActivo ? "true" : "false");

      // Resetear clases base de Tailwind que SÍ existen
      card.className =
        "payment-card flex flex-col items-center justify-center p-2.5 rounded-xl border text-center transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 active:scale-95";

      var icono = card.querySelector(".ti, span.text-xl");
      var texto = card.querySelector("span:last-child");

      if (esActivo) {
        // Colores en línea para asegurar que se apliquen
        if (val === "efectivo") {
          card.style.borderColor = "#059669"; // emerald-600
          card.style.backgroundColor = "#ecfdf5"; // emerald-50
          card.style.color = "#059669";
          if (icono) icono.style.color = "#059669";
          if (texto) texto.style.color = "#059669";
        } else if (val === "pago_movil") {
          card.style.borderColor = "#9333ea"; // purple-600
          card.style.backgroundColor = "#f5f3ff"; // purple-50
          card.style.color = "#9333ea";
          if (icono) icono.style.color = "#9333ea";
          if (texto) texto.style.color = "#9333ea";
        } else if (val === "transferencia") {
          card.style.borderColor = "#2563eb"; // blue-600
          card.style.backgroundColor = "#eff6ff"; // blue-50
          card.style.color = "#2563eb";
          if (icono) icono.style.color = "#2563eb";
          if (texto) texto.style.color = "#2563eb";
        } else if (val === "biopago") {
          card.style.borderColor = "#e11d48"; // rose-600
          card.style.backgroundColor = "#fff1f2"; // rose-50
          card.style.color = "#e11d48";
          if (icono) icono.style.color = "#e11d48";
          if (texto) texto.style.color = "#e11d48";
        } else if (val === "cashea") {
          card.style.borderColor = "#d97706"; // amber-600
          card.style.backgroundColor = "#fffbeb"; // amber-50
          card.style.color = "#d97706";
          if (icono) icono.style.color = "#d97706";
          if (texto) texto.style.color = "#d97706";
        }
      } else {
        // Estado inactivo
        card.style.borderColor = "#e2e8f0"; // slate-200
        card.style.backgroundColor = "#ffffff";
        card.style.color = "#64748b"; // slate-500
        if (icono) icono.style.color = "#94a3b8"; // slate-400
        if (texto) texto.style.color = "#475569"; // slate-600
      }
    });
  }

  function sincronizarMetodoPago() {
    if (!estadoVenta || !metodoInput || !numeroBox || !numeroInput) return;

    var esCompletada = estadoVenta.value === "completada";
    var requiereReferencia =
      METODOS_CON_REFERENCIA.indexOf(metodoInput.value.trim().toLowerCase()) !==
      -1;
    var mostrarNumero = esCompletada && requiereReferencia;

    numeroBox.hidden = !mostrarNumero;
    numeroInput.disabled = !mostrarNumero;
    numeroInput.required = mostrarNumero;

    if (!mostrarNumero) {
      numeroInput.value = "";
    }

    actualizarCardsPago();
  }

  function sincronizarEstado() {
    if (!estadoVenta) return;
    var completada = estadoVenta.value === "completada";
    if (metodoBox) metodoBox.hidden = !completada;
    if (numeroBox) numeroBox.hidden = !completada;
    if (metodoInput) metodoInput.disabled = !completada;
    if (numeroInput) numeroInput.disabled = !completada;
    if (cobrarBtn)
      cobrarBtn.textContent = completada
        ? "Cobrar ticket"
        : "Guardar como pendiente";
    sincronizarMetodoPago();
  }

  if (estadoVenta) {
    estadoVenta.addEventListener("change", sincronizarEstado);
  }
  if (metodoInput) {
    metodoInput.addEventListener("change", sincronizarMetodoPago);
  }

  // Configurar eventos click para las cards de pago
  var paymentCards = document.querySelectorAll(".payment-card");
  paymentCards.forEach(function (card) {
    card.addEventListener("click", function () {
      if (metodoInput && !metodoInput.disabled) {
        metodoInput.value = card.dataset.value;
        metodoInput.dispatchEvent(new Event("change"));
      }
    });
  });

  sincronizarEstado();

  pintar();
})();
