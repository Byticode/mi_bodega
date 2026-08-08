
let carrito = [];
let totalCarrito = 0;

function buscarProducto() {
    const busqueda = document.getElementById('buscarProducto').value.toLowerCase();
    document.querySelectorAll('.producto-card').forEach(function (card) {
        const nombre = card.dataset.nombre || '';
        card.style.display = nombre.includes(busqueda) ? 'block' : 'none';
    });
}

function agregarAlCarrito(id, nombre, precio, unidad, stock) {
    const index = carrito.findIndex(item => item.id === id);
    if (index !== -1) {
        if (carrito[index].cantidad < stock) {
            carrito[index].cantidad++;
            actualizarCarrito();
        } else {
            alert('No hay suficiente stock disponible');
        }
    } else {
        if (stock > 0) {
            carrito.push({ id, nombre, precio, unidad, cantidad: 1, stock });
            actualizarCarrito();
        } else {
            alert('Producto sin stock');
        }
    }
}

function actualizarCarrito() {
    const container = document.getElementById('carritoContainer');
    let html = '';
    totalCarrito = 0;
    let totalProductos = 0;

    if (carrito.length === 0) {
        html = '<p class="text-gray-400 text-center py-8">No hay productos en el carrito</p>';
    } else {
        carrito.forEach(function (item, index) {
            const subtotal = item.cantidad * item.precio;
            totalCarrito += subtotal;
            totalProductos += item.cantidad;

            html += `
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
              <div class="flex-1">
                <p class="font-semibold text-gray-900 text-xs">${item.nombre}</p>
                <span class="text-xs text-gray-500">P. Unit: Bs ${item.precio.toFixed(2)}</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="flex items-center border border-gray-200 rounded">
                  <button onclick="cambiarCantidad(${index}, -1)" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600">-</button>
                  <span class="px-2 text-xs font-bold min-w-[30px] text-center">${item.cantidad}</span>
                  <button onclick="cambiarCantidad(${index}, 1)" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600">+</button>
                </div>
                <span class="font-bold min-w-[70px] text-right text-xs">Bs ${subtotal.toFixed(2)}</span>
                <button onclick="eliminarDelCarrito(${index})" class="text-rose-600 hover:text-rose-800">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>`;
        });
    }

    container.innerHTML = html;
    document.getElementById('totalCarrito').textContent = 'Bs ' + totalCarrito.toFixed(2);
    document.getElementById('totalProductos').textContent = totalProductos;
}

function cambiarCantidad(index, cambio) {
    const nuevaCantidad = carrito[index].cantidad + cambio;
    if (nuevaCantidad <= 0) {
        eliminarDelCarrito(index);
        return;
    }
    if (nuevaCantidad <= carrito[index].stock) {
        carrito[index].cantidad = nuevaCantidad;
        actualizarCarrito();
    } else {
        alert('No hay suficiente stock disponible');
    }
}

function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
}
function procesarVenta() {
    if (carrito.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    const cliente_id = document.getElementById('cliente_id').value;
    const estado = document.getElementById('estado_venta').value;
    const metodo_pago = document.getElementById('metodo_pago').value;
    const numero_pago = document.getElementById('numero_pago').value;

    if (estado === 'completada' && !metodo_pago) {
        alert('Debe seleccionar un método de pago');
        return;
    }

    const productos = carrito.map(item => ({ id: item.id, cantidad: item.cantidad, precio: item.precio }));

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = window.POS_ACTION_URL;

    const campos = { cliente_id, estado, metodo_pago, numero_pago, productos: JSON.stringify(productos) };
    for (const [key, value] of Object.entries(campos)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }

    document.body.appendChild(form);
    if (typeof showSpinner === 'function') showSpinner();
    form.submit();
}

function mostrarTicket(datos) {
    const container = document.getElementById('contenidoTicket');
    let html = `
    <div class="text-center border-b pb-3">
      <p class="font-bold text-lg">🧾 Ticket de Venta</p>
      <p class="text-xs text-gray-500">#${datos.venta_id}</p>
      <p class="text-xs text-gray-500">${new Date().toLocaleString()}</p>
    </div>
    <div class="space-y-1 py-3">`;

    datos.productos.forEach(item => {
        html += `<div class="flex justify-between text-xs">
      <span>${item.nombre} x ${item.cantidad}</span>
      <span>Bs ${(item.cantidad * item.precio).toFixed(2)}</span>
    </div>`;
    });

    html += `</div>
    <div class="border-t pt-3">
      <div class="flex justify-between font-bold text-sm"><span>TOTAL</span><span class="text-olive">Bs ${datos.total.toFixed(2)}</span></div>
      <div class="flex justify-between text-xs text-gray-500 mt-1"><span>Cliente:</span><span>${datos.cliente || 'Consumidor final'}</span></div>
      <div class="flex justify-between text-xs text-gray-500"><span>Estado:</span><span class="${datos.estado === 'completada' ? 'text-green-600' : 'text-amber-600'}">${datos.estado}</span></div>
      ${datos.metodo_pago ? `<div class="flex justify-between text-xs text-gray-500"><span>Método:</span><span>${datos.metodo_pago}</span></div>` : ''}
      ${datos.numero_pago ? `<div class="flex justify-between text-xs text-gray-500"><span>Referencia:</span><span>${datos.numero_pago}</span></div>` : ''}
    </div>`;

    container.innerHTML = html;
    document.getElementById('modalTicket').classList.remove('hidden');
}

function cerrarTicket() {
    document.getElementById('modalTicket').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function () {
    const estadoVenta = document.getElementById('estado_venta');
    const metodoContainer = document.getElementById('metodoPagoContainer');
    const numeroContainer = document.getElementById('numeroPagoContainer');

    function toggleMetodoPago() {
        const completada = estadoVenta.value === 'completada';
        metodoContainer.style.display = completada ? 'block' : 'none';
        numeroContainer.style.display = completada ? 'block' : 'none';
    }

    estadoVenta.addEventListener('change', toggleMetodoPago);
    toggleMetodoPago();

    document.getElementById('buscarProducto').addEventListener('keyup', buscarProducto);

    window.addEventListener('beforeunload', function () {
        carrito = [];
    });
});
