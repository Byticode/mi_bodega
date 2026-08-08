// Lógica dinámica para la creación de surtidos (agregar/eliminar filas y cálculo del costo total)
(function () {
  'use strict';

  var contenedor = document.getElementById('filas');
  var plantilla = document.getElementById('plantillaFila');
  var agregarBtn = document.getElementById('agregarFila');
  var totalOut = document.getElementById('totalCosto');
  var aviso = document.getElementById('avisoFilas');
  var form = document.getElementById('surtidoForm');
  var contador = 1;

  if (!contenedor) return;

  function bs(n) {
    return 'Bs ' + Number(n).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function calcular() {
    var total = 0;
    Array.prototype.forEach.call(contenedor.querySelectorAll('.fila'), function (fila) {
      var cantidadInput = fila.querySelector('.fila-cantidad');
      var precioInput = fila.querySelector('.fila-precio');
      var cantidad = parseInt(cantidadInput ? cantidadInput.value : 0, 10) || 0;
      var precio = parseFloat(precioInput ? precioInput.value : 0) || 0;
      total += cantidad * precio;
    });
    if (totalOut) totalOut.textContent = bs(total);
  }

  function sincronizarQuitar() {
    var filas = contenedor.querySelectorAll('.fila');
    Array.prototype.forEach.call(filas, function (fila) {
      var btn = fila.querySelector('.fila-quitar');
      if (btn) btn.disabled = filas.length <= 1;
    });
    if (aviso) {
      aviso.textContent = filas.length === 1 ? '1 producto en el surtido' : filas.length + ' productos en el surtido';
    }
  }

  function activarFila(fila) {
    var select = fila.querySelector('.fila-producto');
    var cantidad = fila.querySelector('.fila-cantidad');
    var precio = fila.querySelector('.fila-precio');
    var quitar = fila.querySelector('.fila-quitar');

    if (select) {
      select.addEventListener('change', function () {
        var opcion = select.options[select.selectedIndex];
        if (precio && opcion && opcion.dataset.precio) {
          precio.placeholder = 'Venta: ' + opcion.dataset.precio;
        } else if (precio) {
          precio.placeholder = '0,00';
        }
        calcular();
      });
    }

    if (cantidad) cantidad.addEventListener('input', calcular);
    if (precio) precio.addEventListener('input', calcular);

    if (quitar) {
      quitar.addEventListener('click', function () {
        if (contenedor.querySelectorAll('.fila').length <= 1) return;
        fila.remove();
        sincronizarQuitar();
        calcular();
      });
    }

    return select;
  }

  function agregarFila() {
    if (!plantilla) return;
    var fila = plantilla.content.firstElementChild.cloneNode(true);
    contador++;

    ['producto', 'cantidad', 'precio'].forEach(function (campo) {
      var control = fila.querySelector('#surtido-' + campo + '-0');
      var label = fila.querySelector('label[for="surtido-' + campo + '-0"]');
      var id = 'surtido-' + campo + '-' + contador;
      if (control) control.id = id;
      if (label) label.setAttribute('for', id);
    });

    var quitarBtn = fila.querySelector('.fila-quitar');
    if (quitarBtn) {
      quitarBtn.setAttribute('aria-label', 'Quitar el producto ' + contador + ' del surtido');
    }

    contenedor.appendChild(fila);
    var firstControl = activarFila(fila);
    if (firstControl) firstControl.focus();
    sincronizarQuitar();
    calcular();
  }

  if (agregarBtn) agregarBtn.addEventListener('click', agregarFila);
  if (form) form.addEventListener('submit', calcular);

  Array.prototype.forEach.call(contenedor.querySelectorAll('.fila'), activarFila);
  sincronizarQuitar();
  calcular();
})();
