// Punto de Venta (POS) — Lógica interactiva con modo lista, contenedor de ticket inferior y métodos de pago
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

  // Elementos de paginación
  var paginacionContenedor = document.getElementById("paginacionPos");
  var btnPaginaAnt = document.getElementById("btnPaginaAnt");
  var btnPaginaSig = document.getElementById("btnPaginaSig");
  var numerosPagina = document.getElementById("numerosPagina");
  var infoPaginacion = document.getElementById("infoPaginacion");

  var POR_PAGINA = 6;
  var paginaActual = 1;
  var todosLosProductos = [];
  var productosFiltrados = [];

  if (grid) {
    todosLosProductos = Array.prototype.slice.call(
      grid.querySelectorAll(".pos-list-item, .pos-tile")
    );
  }

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

  /* ── Paginación y Filtrado del Catálogo ───────────────────────────── */
  function renderPaginacion() {
    var totalItems = productosFiltrados.length;
    var totalPaginas = Math.ceil(totalItems / POR_PAGINA) || 1;

    if (paginaActual > totalPaginas) paginaActual = totalPaginas;
    if (paginaActual < 1) paginaActual = 1;

    var inicio = (paginaActual - 1) * POR_PAGINA;
    var fin = inicio + POR_PAGINA;

    // Mostrar solo los elementos de la página actual
    todosLosProductos.forEach(function (el) {
      el.hidden = true;
    });

    var itemsPagina = productosFiltrados.slice(inicio, fin);
    itemsPagina.forEach(function (el) {
      el.hidden = false;
    });

    // Actualizar texto informativo
    if (infoPaginacion) {
      if (totalItems === 0) {
        infoPaginacion.textContent = "0 productos";
      } else {
        var mostradosHasta = Math.min(fin, totalItems);
        infoPaginacion.textContent =
          (inicio + 1) + "–" + mostradosHasta + " de " + totalItems + " productos";
      }
    }

    if (contador) {
      contador.textContent =
        totalItems === 1 ? "1 producto" : totalItems + " productos";
    }

    if (sinResultados) sinResultados.hidden = totalItems > 0;
    if (paginacionContenedor) paginacionContenedor.hidden = totalItems <= POR_PAGINA;

    // Actualizar botones de navegación
    if (btnPaginaAnt) btnPaginaAnt.disabled = paginaActual <= 1;
    if (btnPaginaSig) btnPaginaSig.disabled = paginaActual >= totalPaginas;

    // Renderizar botones numéricos
    if (numerosPagina) {
      numerosPagina.innerHTML = "";
      if (totalPaginas > 1) {
        for (var p = 1; p <= totalPaginas; p++) {
          (function (num) {
            var btn = document.createElement("button");
            btn.type = "button";
            btn.className =
              "px-2 py-0.5 rounded text-xs font-semibold transition-colors " +
              (num === paginaActual
                ? "bg-olive text-white shadow-xs"
                : "bg-card hover:bg-card-2 text-ink border border-rule");
            btn.textContent = num;
            btn.setAttribute("aria-label", "Ir a página " + num);
            btn.addEventListener("click", function () {
              irAPagina(num);
            });
            numerosPagina.appendChild(btn);
          })(p);
        }
      }
    }
  }

  function irAPagina(p) {
    paginaActual = p;
    renderPaginacion();
    if (grid) grid.scrollTop = 0;
  }

  if (btnPaginaAnt) {
    btnPaginaAnt.addEventListener("click", function () {
      if (paginaActual > 1) irAPagina(paginaActual - 1);
    });
  }

  if (btnPaginaSig) {
    btnPaginaSig.addEventListener("click", function () {
      var totalPaginas = Math.ceil(productosFiltrados.length / POR_PAGINA);
      if (paginaActual < totalPaginas) irAPagina(paginaActual + 1);
    });
  }

  function filtrar() {
    if (!grid || !buscador) return;
    var q = buscador.value.trim().toLowerCase();

    productosFiltrados = todosLosProductos.filter(function (item) {
      return !q || (item.dataset.buscar || "").indexOf(q) !== -1;
    });

    paginaActual = 1;
    renderPaginacion();
  }

  var btnLimpiarBuscarProducto = document.getElementById("btnLimpiarBuscarProducto");

  function toggleClearProducto() {
    if (btnLimpiarBuscarProducto && buscador) {
      btnLimpiarBuscarProducto.style.display = buscador.value.trim() !== "" ? "flex" : "none";
    }
  }

  if (buscador) {
    buscador.addEventListener("input", function () {
      toggleClearProducto();
      filtrar();
    });
    buscador.addEventListener("keydown", function (e) {
      if (e.key !== "Enter") return;
      e.preventDefault();
      var noAgotados = productosFiltrados.filter(function (t) {
        return !t.disabled;
      });
      if (noAgotados.length === 1) {
        agregar(noAgotados[0]);
        buscador.select();
      }
    });
  }

  if (btnLimpiarBuscarProducto) {
    btnLimpiarBuscarProducto.addEventListener("click", function () {
      if (buscador) {
        buscador.value = "";
        toggleClearProducto();
        filtrar();
        buscador.focus();
      }
    });
  }

  // Inicializar productos filtrados
  productosFiltrados = todosLosProductos.slice();
  renderPaginacion();

  /* ── Notificaciones Toast en Tiempo Real ─────────────────────────── */
  var toastContainer = document.getElementById("posToastContainer");

  function notificar(tipo, mensaje) {
    if (!toastContainer) {
      anunciar(mensaje);
      return;
    }

    var iconos = {
      success: "ti-circle-check",
      error: "ti-circle-x",
      info: "ti-info-circle",
      warn: "ti-alert-circle"
    };

    var toast = document.createElement("div");
    toast.className = "pos-toast pos-toast--" + (tipo || "info");
    toast.innerHTML =
      '<i class="ti ' + (iconos[tipo] || iconos.info) + ' text-base shrink-0" aria-hidden="true"></i>' +
      '<div>' + mensaje + '</div>';

    toastContainer.appendChild(toast);

    requestAnimationFrame(function () {
      toast.setAttribute("data-show", "true");
    });

    setTimeout(function () {
      toast.removeAttribute("data-show");
      setTimeout(function () {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
      }, 250);
    }, 3500);
  }

  /* ── Carrito / Ticket de compra ───────────────────────────────────── */
  function agregar(tile) {
    var id = Number(tile.dataset.id);
    var stock = Number(tile.dataset.stock);
    var nombre = tile.dataset.nombre;

    if (stock <= 0) {
      notificar("error", '<strong>' + nombre + '</strong> está agotado. No se puede agregar al ticket.');
      return;
    }

    var linea = carrito.find(function (item) {
      return item.id === id;
    });

    if (linea) {
      if (linea.cantidad >= stock) {
        notificar("warn", '<strong>' + nombre + '</strong> alcanzó el stock máximo disponible (' + stock + ' unidades).');
        return;
      }
      linea.cantidad++;

      // Notificar cuando se alcanza exactamente el tope
      if (linea.cantidad === stock) {
        notificar("warn", 'Has agregado todo el stock de <strong>' + nombre + '</strong> (' + stock + ' unidades).');
      }
    } else {
      carrito.push({
        id: id,
        nombre: nombre,
        precio: Number(tile.dataset.precio),
        unidad: tile.dataset.unidad || "u",
        stock: stock,
        cantidad: 1,
      });

      // Si el producto solo tiene 1 unidad, ya alcanzó su máximo
      if (stock === 1) {
        notificar("warn", '<strong>' + nombre + '</strong> tiene solo 1 unidad disponible.');
      }
    }

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
      notificar("warn", '<strong>' + carrito[i].nombre + '</strong> alcanzó el stock máximo (' + carrito[i].stock + ' unidades).');
      return;
    } else {
      carrito[i].cantidad = nueva;

      // Notificar al llegar exactamente al tope
      if (nueva === carrito[i].stock) {
        notificar("warn", 'Has agregado todo el stock de <strong>' + carrito[i].nombre + '</strong> (' + carrito[i].stock + ' unidades).');
      }
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
        '<div class="empty py-10 my-auto text-center" id="carritoVacio">' +
        '  <i class="ti ti-shopping-cart-x empty-icon text-ink-3 opacity-40 text-3xl mb-2" aria-hidden="true"></i>' +
        '  <p class="empty-title text-sm">El ticket está vacío</p>' +
        '  <p class="empty-sub text-xs">Toca o haz clic en cualquier producto de la lista superior para agregarlo.</p>' +
        '</div>';
    } else {
      contenedor.textContent = "";

      var listaContenedor = document.createElement("div");
      listaContenedor.className = "flex flex-col divide-y divide-rule";

      carrito.forEach(function (item) {
        var subtotal = item.cantidad * item.precio;
        total += subtotal;
        articulos += item.cantidad;

        var fila = document.createElement("div");
        fila.className =
          "pos-cart-row flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 p-3 bg-card hover:bg-card-2/60 transition-colors";

        // Nombre y precio unitario
        var info = document.createElement("div");
        info.className = "flex flex-col min-w-0 flex-1";
        var nombre = document.createElement("p");
        nombre.className = "font-medium text-sm text-ink truncate";
        nombre.textContent = item.nombre;
        var unit = document.createElement("p");
        unit.className = "text-xs text-ink-3 font-mono";
        unit.textContent =
          usd(item.precio) +
          " / " +
          item.unidad +
          (TASA_USD > 0 ? " (" + bs(item.precio * TASA_USD) + ")" : "");
        info.appendChild(nombre);
        info.appendChild(unit);

        // Controles de cantidad y subtotales
        var controles = document.createElement("div");
        controles.className = "flex items-center gap-3 sm:gap-4 shrink-0";

        // Stepper de cantidad
        var qty = document.createElement("div");
        qty.className = "qty";

        var menos = document.createElement("button");
        menos.type = "button";
        menos.className = "cursor-pointer";
        menos.setAttribute("aria-label", "Quitar una unidad de " + item.nombre);
        menos.innerHTML = '<i class="ti ti-minus text-xs cursor-pointer" aria-hidden="true"></i>';
        menos.addEventListener("click", function (e) {
          e.stopPropagation();
          cambiar(item.id, -1);
        });

        var salida = document.createElement("span");
        salida.textContent = item.cantidad;

        var mas = document.createElement("button");
        mas.type = "button";
        mas.className = "cursor-pointer";
        mas.setAttribute("aria-label", "Agregar una unidad de " + item.nombre);
        mas.disabled = item.cantidad >= item.stock;
        mas.innerHTML = '<i class="ti ti-plus text-xs cursor-pointer" aria-hidden="true"></i>';
        mas.addEventListener("click", function (e) {
          e.stopPropagation();
          cambiar(item.id, 1);
        });

        qty.appendChild(menos);
        qty.appendChild(salida);
        qty.appendChild(mas);

        // Subtotal de la línea
        var monto = document.createElement("div");
        monto.className = "text-right min-w-[70px]";
        monto.innerHTML =
          '<span class="font-mono font-semibold text-sm text-ink block">' +
          usd(subtotal) +
          "</span>" +
          (TASA_USD > 0
            ? '<span class="font-mono text-xs text-ink-3 block">' +
              bs(subtotal * TASA_USD) +
              "</span>"
            : "");

        // Botón eliminar de cada línea del ticket
        var quitar = document.createElement("button");
        quitar.type = "button";
        quitar.className =
          "cursor-pointer w-8 h-8 rounded-lg flex items-center justify-center text-ink-3 hover:text-danger hover:bg-danger-bg transition-colors";
        quitar.setAttribute(
          "aria-label",
          "Eliminar " + item.nombre + " del ticket"
        );
        quitar.setAttribute("title", "Eliminar " + item.nombre);
        quitar.innerHTML = '<i class="ti ti-trash text-base cursor-pointer" aria-hidden="true"></i>';
        quitar.addEventListener("click", function (e) {
          e.stopPropagation();
          eliminar(item.id);
        });

        controles.appendChild(qty);
        controles.appendChild(monto);
        controles.appendChild(quitar);

        fila.appendChild(info);
        fila.appendChild(controles);
        listaContenedor.appendChild(fila);
      });

      contenedor.appendChild(listaContenedor);
    }

    var totalEl = document.getElementById("totalCarrito");
    if (totalEl) totalEl.textContent = usd(total);

    var articulosEl = document.getElementById("totalProductos");
    if (articulosEl) articulosEl.textContent = articulos;

    var totalUsd = document.getElementById("totalUsd");
    if (totalUsd && TASA_USD > 0) {
      totalUsd.innerHTML =
        bs(total * TASA_USD) +
        ' <span class="text-[11px] text-ink-3">· BCV ' +
        money(TASA_USD) +
        "/$</span>";
    }

    if (inputProductos) {
      inputProductos.value = JSON.stringify(
        carrito.map(function (item) {
          return { id: item.id, cantidad: item.cantidad, precio: item.precio };
        })
      );
    }

    // Actualizar estado seleccionado, badges y botón de restar en el catálogo
    todosLosProductos.forEach(function (tile) {
      var id = Number(tile.dataset.id);
      var enCarrito = carrito.find(function (item) {
        return item.id === id;
      });

      var badge = tile.querySelector(".pos-cart-badge");
      var badgeQty = tile.querySelector(".badge-qty");
      var minusBtn = tile.querySelector(".pos-quick-minus");

      if (enCarrito && enCarrito.cantidad > 0) {
        tile.classList.add("is-selected");
        if (badge) {
          badge.hidden = false;
          if (badgeQty) badgeQty.textContent = enCarrito.cantidad;
        }
        if (minusBtn) {
          minusBtn.hidden = false;
        }
      } else {
        tile.classList.remove("is-selected");
        if (badge) {
          badge.hidden = true;
        }
        if (minusBtn) {
          minusBtn.hidden = true;
        }
      }
    });

    if (cobrarBtn) {
      cobrarBtn.disabled = carrito.length === 0 || (TASA_USD <= 0 && estadoVenta && estadoVenta.value === "completada");
    }
    if (vaciarBtn) vaciarBtn.hidden = carrito.length === 0;
  }

  function money(n) {
    return (
      "Bs " +
      Number(n).toLocaleString("es-VE", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );
  }

  if (grid) {
    grid.addEventListener("click", function (e) {
      // 0. Botón de ver información resumida de producto
      var infoBtn = e.target.closest(".btn-info-producto-pos");
      if (infoBtn) {
        e.stopPropagation();
        try {
          var pData = JSON.parse(infoBtn.dataset.producto);
          abrirModalInfoPOS(pData);
        } catch (err) {
          console.error(err);
        }
        return;
      }

      // 1. Botón de restar cantidad
      var minusBtn = e.target.closest(".pos-quick-minus");
      if (minusBtn) {
        e.stopPropagation();
        var pid = Number(minusBtn.dataset.pid);
        cambiar(pid, -1);
        return;
      }

      // 2. Botón de sumar cantidad
      var plusBtn = e.target.closest(".pos-quick-plus");
      if (plusBtn) {
        e.stopPropagation();
        var item = plusBtn.closest(".pos-list-item, .pos-tile");
        if (item && item.getAttribute("aria-disabled") !== "true" && !item.disabled) {
          agregar(item);
        }
        return;
      }

      // 3. Clic en la fila completa
      var item = e.target.closest(".pos-list-item, .pos-tile");
      if (item && item.getAttribute("aria-disabled") !== "true" && !item.disabled) {
        agregar(item);
      }
    });

    grid.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        var item = e.target.closest(".pos-list-item, .pos-tile");
        if (item && item.getAttribute("aria-disabled") !== "true" && !item.disabled) {
          e.preventDefault();
          agregar(item);
        }
      }
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

  var METODOS_CON_REFERENCIA = ["pago_movil", "cashea", "transferencia"];

  function actualizarCardsPago() {
    if (!metodoInput) return;
    var valorActual = metodoInput.value;
    var cards = document.querySelectorAll(".payment-card");

    cards.forEach(function (card) {
      var val = card.dataset.value;
      var esActivo = val === valorActual;
      card.setAttribute("aria-checked", esActivo ? "true" : "false");
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
    if (cobrarBtn) {
      cobrarBtn.innerHTML = completada
        ? '<i class="ti ti-check text-base mr-1" aria-hidden="true"></i> Cobrar ticket'
        : '<i class="ti ti-clock text-base mr-1" aria-hidden="true"></i> Guardar como pendiente';
    }
    sincronizarMetodoPago();
  }

  if (estadoVenta) {
    estadoVenta.addEventListener("change", function () {
      sincronizarEstado();
      pintar();
    });
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

  /* ── 1. Buscador de Clientes en Tiempo Real ────────────────────────── */
  var buscarClienteInput = document.getElementById("buscarCliente");
  var clienteSelect = document.getElementById("cliente_id");
  var btnLimpiarBuscarCliente = document.getElementById("btnLimpiarBuscarCliente");

  function toggleClearCliente() {
    if (btnLimpiarBuscarCliente && buscarClienteInput) {
      btnLimpiarBuscarCliente.style.display = buscarClienteInput.value.trim() !== "" ? "flex" : "none";
    }
  }

  if (buscarClienteInput && clienteSelect) {
    buscarClienteInput.addEventListener("input", function () {
      toggleClearCliente();
      var q = buscarClienteInput.value.trim().toLowerCase();
      var options = clienteSelect.querySelectorAll("option");
      var primeraCoincidencia = null;

      Array.prototype.forEach.call(options, function (opt) {
        var textoBuscar = (opt.dataset.buscar || opt.textContent || "").toLowerCase();
        var coincide = !q || textoBuscar.indexOf(q) !== -1;
        opt.hidden = !coincide;
        if (coincide && !primeraCoincidencia && opt.value !== "") {
          primeraCoincidencia = opt;
        }
      });

      if (q && primeraCoincidencia) {
        clienteSelect.value = primeraCoincidencia.value;
      } else if (!q) {
        clienteSelect.value = "";
      }
    });

    if (btnLimpiarBuscarCliente) {
      btnLimpiarBuscarCliente.addEventListener("click", function () {
        buscarClienteInput.value = "";
        toggleClearCliente();
        var options = clienteSelect.querySelectorAll("option");
        Array.prototype.forEach.call(options, function (opt) {
          opt.hidden = false;
        });
        clienteSelect.value = "";
        buscarClienteInput.focus();
      });
    }

    buscarClienteInput.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        var optionsVisibles = Array.prototype.filter.call(
          clienteSelect.querySelectorAll("option:not([hidden])"),
          function (opt) {
            return opt.value !== "";
          }
        );
        if (optionsVisibles.length > 0) {
          clienteSelect.value = optionsVisibles[0].value;
          anunciar("Cliente seleccionado: " + optionsVisibles[0].textContent);
        }
      }
    });
  }

  /* ── Validación del Formulario de Venta (Cliente y Carrito) ─────────── */
  var ventaForm = document.getElementById("ventaForm");
  if (ventaForm) {
    ventaForm.addEventListener("submit", function (e) {
      if (!clienteSelect || !clienteSelect.value || clienteSelect.value.trim() === "") {
        e.preventDefault();
        notificar("error", "<strong>Cliente requerido:</strong> Debe seleccionar un cliente antes de procesar la venta.");
        if (buscarClienteInput) {
          buscarClienteInput.focus();
          buscarClienteInput.classList.add("ring-2", "ring-rose-500");
          setTimeout(function () {
            buscarClienteInput.classList.remove("ring-2", "ring-rose-500");
          }, 2500);
        } else if (clienteSelect) {
          clienteSelect.focus();
        }
        return false;
      }

      if (carrito.length === 0) {
        e.preventDefault();
        notificar("error", "<strong>Ticket vacío:</strong> Debe agregar al menos un producto al ticket.");
        return false;
      }
    });
  }

  /* ── Utilidades de Modales ─────────────────────────────────────────── */
  function abrirModal(modalEl) {
    if (!modalEl) return;
    modalEl.setAttribute("data-open", "true");
    modalEl.setAttribute("aria-hidden", "false");
    var primerInput = modalEl.querySelector("input, select, button");
    if (primerInput) setTimeout(function () { primerInput.focus(); }, 50);
  }

  function cerrarModal(modalEl) {
    if (!modalEl) return;
    modalEl.removeAttribute("data-open");
    modalEl.setAttribute("aria-hidden", "true");
  }

  // Cerrar modales con Escape o haciendo clic en el fondo
  document.querySelectorAll(".modal-overlay").forEach(function (modal) {
    modal.addEventListener("click", function (e) {
      if (e.target === modal) cerrarModal(modal);
    });
  });

  window.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      document.querySelectorAll('.modal-overlay[data-open="true"]').forEach(function (modal) {
        cerrarModal(modal);
      });
    }
  });



  /* ── 2. Modal de Registro Rápido de Cliente ────────────────────────── */
  var modalCliente = document.getElementById("modalCliente");
  var btnNuevoCliente = document.getElementById("btnNuevoCliente");
  var btnModalClienteDirecto = document.getElementById("btnModalClienteDirecto");
  var btnCerrarModalCliente = document.getElementById("btnCerrarModalCliente");
  var btnCancelarCliente = document.getElementById("btnCancelarCliente");
  var formClienteRapido = document.getElementById("formClienteRapido");
  var alertaClienteRapido = document.getElementById("alertaClienteRapido");
  var btnGuardarCliente = document.getElementById("btnGuardarCliente");

  function abrirModalCliente() {
    if (typeof hideSpinner === "function") hideSpinner();
    if (alertaClienteRapido) alertaClienteRapido.hidden = true;
    if (formClienteRapido) formClienteRapido.reset();
    abrirModal(modalCliente);
  }

  if (btnNuevoCliente) btnNuevoCliente.addEventListener("click", abrirModalCliente);
  if (btnModalClienteDirecto) btnModalClienteDirecto.addEventListener("click", abrirModalCliente);
  if (btnCerrarModalCliente) btnCerrarModalCliente.addEventListener("click", function () { cerrarModal(modalCliente); });
  if (btnCancelarCliente) btnCancelarCliente.addEventListener("click", function () { cerrarModal(modalCliente); });

  if (formClienteRapido) {
    formClienteRapido.addEventListener("submit", function (e) {
      e.preventDefault();
      e.stopPropagation();
      if (typeof hideSpinner === "function") hideSpinner();
      if (!window.URL_CREAR_CLIENTE) return;

      var formData = new FormData(formClienteRapido);
      if (btnGuardarCliente) {
        btnGuardarCliente.disabled = true;
        btnGuardarCliente.innerHTML = '<i class="ti ti-loader animate-spin text-xs mr-1" aria-hidden="true"></i> Guardando...';
      }

      fetch(window.URL_CREAR_CLIENTE, {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" }
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (typeof hideSpinner === "function") hideSpinner();
          if (btnGuardarCliente) {
            btnGuardarCliente.disabled = false;
            btnGuardarCliente.innerHTML = '<i class="ti ti-check text-xs mr-1" aria-hidden="true"></i> Guardar y seleccionar';
          }

          if (data.success && data.cliente) {
            var nuevo = data.cliente;
            var nom = nuevo.cliente_nombre + " " + nuevo.cliente_apellido;
            var ced = nuevo.cliente_cedula || "";
            var opt = document.createElement("option");
            opt.value = nuevo.cliente_id;
            opt.dataset.cedula = ced;
            opt.dataset.buscar = (nom + " " + ced).toLowerCase();
            opt.textContent = nom + (ced ? " (V-" + ced + ")" : "");

            if (clienteSelect) {
              clienteSelect.appendChild(opt);
              clienteSelect.value = nuevo.cliente_id;
            }

            if (buscarClienteInput) buscarClienteInput.value = "";
            cerrarModal(modalCliente);
            notificar("success", "Cliente <strong>" + nom + "</strong> registrado y seleccionado.");
          } else {
            if (alertaClienteRapido) {
              alertaClienteRapido.textContent = data.message || "Error al registrar cliente.";
              alertaClienteRapido.hidden = false;
            }
            notificar("error", data.message || "Error al registrar cliente.");
          }
        })
        .catch(function (err) {
          if (typeof hideSpinner === "function") hideSpinner();
          if (btnGuardarCliente) {
            btnGuardarCliente.disabled = false;
            btnGuardarCliente.innerHTML = '<i class="ti ti-check text-xs mr-1" aria-hidden="true"></i> Guardar y seleccionar';
          }
          if (alertaClienteRapido) {
            alertaClienteRapido.textContent = "Ocurrió un error de conexión al guardar el cliente.";
            alertaClienteRapido.hidden = false;
          }
          notificar("error", "Error de conexión al registrar cliente.");
        });
    });
  }

  /* ── 3. Actualizar Tasa BCV en Tiempo Real ─────────────────────────── */
  var btnRefrescarTasa = document.getElementById("btnRefrescarTasa");
  var iconoRefrescarTasa = document.getElementById("iconoRefrescarTasa");
  var textoRefrescarTasa = document.getElementById("textoRefrescarTasa");

  if (btnRefrescarTasa) {
    btnRefrescarTasa.addEventListener("click", function () {
      if (!window.URL_REFRESCAR_TASA) return;
      if (typeof hideSpinner === "function") hideSpinner();

      btnRefrescarTasa.disabled = true;
      if (iconoRefrescarTasa) iconoRefrescarTasa.classList.add("animate-spin");
      if (textoRefrescarTasa) textoRefrescarTasa.textContent = "Consultando API...";

      fetch(window.URL_REFRESCAR_TASA, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" }
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (typeof hideSpinner === "function") hideSpinner();
          btnRefrescarTasa.disabled = false;
          if (iconoRefrescarTasa) iconoRefrescarTasa.classList.remove("animate-spin");
          if (textoRefrescarTasa) textoRefrescarTasa.textContent = "Actualizar tasa BCV";

          if (data.success && data.tasa_usd) {
            TASA_USD = Number(data.tasa_usd);
            window.TASA_USD = TASA_USD;
            notificar("success", "Tasa oficial BCV actualizada a <strong>" + bs(TASA_USD) + "/$</strong>");
            pintar();
          } else {
            notificar("error", "No se pudo refrescar la tasa: " + (data.mensaje || "Error"));
          }
        })
        .catch(function () {
          if (typeof hideSpinner === "function") hideSpinner();
          btnRefrescarTasa.disabled = false;
          if (iconoRefrescarTasa) iconoRefrescarTasa.classList.remove("animate-spin");
          if (textoRefrescarTasa) textoRefrescarTasa.textContent = "Actualizar tasa BCV";
          notificar("error", "Error al consultar la tasa oficial desde la API.");
        });
    });
  }

  /* ── 4. Vista Previa e Impresión de Ticket ─────────────────────────── */
  var modalTicketPreview = document.getElementById("modalTicketPreview");
  var btnPreviewTicket = document.getElementById("btnPreviewTicket");
  var btnCerrarModalTicket = document.getElementById("btnCerrarModalTicket");
  var btnCerrarTicketPreview = document.getElementById("btnCerrarTicketPreview");
  var btnImprimirTicket = document.getElementById("btnImprimirTicket");

  var ticketFecha = document.getElementById("ticketPreviewFecha");
  var ticketCliente = document.getElementById("ticketPreviewCliente");
  var ticketEstado = document.getElementById("ticketPreviewEstado");
  var ticketMetodo = document.getElementById("ticketPreviewMetodo");
  var ticketItems = document.getElementById("ticketPreviewItems");
  var ticketTotalUsd = document.getElementById("ticketPreviewTotalUsd");
  var ticketTotalBs = document.getElementById("ticketPreviewTotalBs");

  if (btnPreviewTicket) {
    btnPreviewTicket.addEventListener("click", function () {
      if (carrito.length === 0) {
        notificar("warn", "<strong>Ticket vacío:</strong> Agrega al menos un producto al ticket para ver la vista previa.");
        return;
      }

      var ahora = new Date();
      if (ticketFecha) {
        ticketFecha.textContent = "Fecha: " + ahora.toLocaleDateString("es-VE") + " " + ahora.toLocaleTimeString("es-VE", { hour: "2-digit", minute: "2-digit" });
      }

      if (ticketCliente) {
        var optSel = clienteSelect ? clienteSelect.options[clienteSelect.selectedIndex] : null;
        ticketCliente.textContent = optSel ? optSel.textContent.trim() : "Consumidor final";
      }

      if (ticketEstado && estadoVenta) {
        ticketEstado.textContent = estadoVenta.value === "completada" ? "Completada" : "Pendiente de cobro";
      }

      if (ticketMetodo && metodoInput) {
        var labels = {
          efectivo: "Efectivo",
          transferencia: "Punto de venta",
          pago_movil: "Pago móvil / Transf",
          biopago: "Biopago",
          cashea: "Cashea"
        };
        ticketMetodo.textContent = labels[metodoInput.value] || metodoInput.value;
      }

      if (ticketItems) {
        ticketItems.innerHTML = "";
        var total = 0;

        carrito.forEach(function (it) {
          var sub = it.cantidad * it.precio;
          total += sub;

          var itemRow = document.createElement("div");
          itemRow.className = "grid grid-cols-12 gap-1 py-0.5 border-b border-gray-100";
          itemRow.innerHTML =
            '<span class="col-span-6 truncate font-medium">' + it.cantidad + 'x ' + it.nombre + '</span>' +
            '<span class="col-span-3 text-right text-gray-600">' + usd(it.precio) + '</span>' +
            '<span class="col-span-3 text-right font-bold">' + usd(sub) + '</span>';
          ticketItems.appendChild(itemRow);
        });

        if (ticketTotalUsd) ticketTotalUsd.textContent = usd(total);
        if (ticketTotalBs && TASA_USD > 0) ticketTotalBs.textContent = bs(total * TASA_USD);
      }

      abrirModal(modalTicketPreview);
    });
  }

  if (btnCerrarModalTicket) btnCerrarModalTicket.addEventListener("click", function () { cerrarModal(modalTicketPreview); });
  if (btnCerrarTicketPreview) btnCerrarTicketPreview.addEventListener("click", function () { cerrarModal(modalTicketPreview); });

  if (btnImprimirTicket) {
    btnImprimirTicket.addEventListener("click", function () {
      window.print();
    });
  }

  /* ── 5. Modal Detalle Resumido de Producto en POS ─────────────────── */
  var modalInfoPOS = document.getElementById("modalInfoProductoPOS");
  var btnCerrarInfoPOS = document.getElementById("btnCerrarInfoPOS");
  var btnCerrarInfoPOSFooter = document.getElementById("btnCerrarInfoPOSFooter");
  var btnAgregarDesdeInfoPOS = document.getElementById("btnAgregarDesdeInfoPOS");
  var posInfoCategoria = document.getElementById("posInfoCategoria");
  var posInfoNombre = document.getElementById("posInfoNombre");
  var posInfoCodigo = document.getElementById("posInfoCodigo");
  var posInfoPresentacion = document.getElementById("posInfoPresentacion");
  var posInfoStock = document.getElementById("posInfoStock");
  var posInfoPrecioUsd = document.getElementById("posInfoPrecioUsd");
  var posInfoPrecioBs = document.getElementById("posInfoPrecioBs");
  var productoSeleccionadoInfo = null;

  function abrirModalInfoPOS(p) {
    if (!modalInfoPOS || !p) return;
    productoSeleccionadoInfo = p;
    if (posInfoCategoria) posInfoCategoria.textContent = p.categoria || "Sin categoría";
    if (posInfoNombre) posInfoNombre.textContent = p.nombre || "—";
    if (posInfoCodigo) posInfoCodigo.textContent = p.codigo ? "Código: #" + p.codigo : "Sin código";

    var pres = "";
    if (p.peso && parseFloat(p.peso) > 0) {
      pres = parseFloat(p.peso) + " " + (p.unidad || "");
    } else {
      pres = p.unidad || "Unidad";
    }
    if (posInfoPresentacion) posInfoPresentacion.textContent = pres;

    var s = parseInt(p.stock) || 0;
    if (posInfoStock) {
      if (s <= 0) {
        posInfoStock.innerHTML = '<span class="text-rose-600 font-bold">Agotado (0)</span>';
      } else if (s <= 10) {
        posInfoStock.innerHTML = '<span class="text-amber-600 font-bold">' + s + ' ' + (p.unidad || "") + ' (Bajo)</span>';
      } else {
        posInfoStock.innerHTML = '<span class="text-emerald-700 font-bold">' + s + ' ' + (p.unidad || "") + '</span>';
      }
    }

    if (posInfoPrecioUsd) posInfoPrecioUsd.textContent = p.precio_usd || "$ 0,00";
    if (posInfoPrecioBs) posInfoPrecioBs.textContent = p.precio_bs || "Bs 0,00";

    if (btnAgregarDesdeInfoPOS) {
      btnAgregarDesdeInfoPOS.disabled = s <= 0;
    }

    modalInfoPOS.classList.remove("hidden");
    modalInfoPOS.classList.add("flex");
  }

  function cerrarModalInfoPOS() {
    if (modalInfoPOS) {
      modalInfoPOS.classList.add("hidden");
      modalInfoPOS.classList.remove("flex");
      productoSeleccionadoInfo = null;
    }
  }

  if (btnCerrarInfoPOS) btnCerrarInfoPOS.addEventListener("click", cerrarModalInfoPOS);
  if (btnCerrarInfoPOSFooter) btnCerrarInfoPOSFooter.addEventListener("click", cerrarModalInfoPOS);

  if (btnAgregarDesdeInfoPOS) {
    btnAgregarDesdeInfoPOS.addEventListener("click", function () {
      if (!productoSeleccionadoInfo || !grid) return;
      var itemEl = grid.querySelector('.pos-list-item[data-id="' + productoSeleccionadoInfo.id + '"], .pos-tile[data-id="' + productoSeleccionadoInfo.id + '"]');
      if (itemEl && itemEl.getAttribute("aria-disabled") !== "true" && !itemEl.disabled) {
        agregar(itemEl);
        notificar("success", "<strong>" + productoSeleccionadoInfo.nombre + "</strong> agregado al ticket.");
      }
      cerrarModalInfoPOS();
    });
  }

  if (modalInfoPOS) {
    modalInfoPOS.addEventListener("click", function (e) {
      if (e.target === modalInfoPOS) cerrarModalInfoPOS();
    });
  }

  window.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      cerrarModalInfoPOS();
    }
  });

})();

