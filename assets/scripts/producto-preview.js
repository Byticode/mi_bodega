// Vista previa en tiempo real para el nombre y precios del producto (crear y editar)
(function () {
  'use strict';

  var nombre = document.getElementById('nombre');
  var peso = document.getElementById('peso');
  var unidad = document.getElementById('unidad');
  var previewNombre = document.getElementById('previewNombre');

  var precioCosto = document.getElementById('precio_costo');
  var ganancia = document.getElementById('ganancia');
  var iva = document.getElementById('iva');
  var precioVenta = document.getElementById('precio');
  var previewBs = document.getElementById('previewBs');
  var conversionBsHint = document.getElementById('conversionBsHint');

  var TASA_USD = Number(window.TASA_USD || 0);

  function formatoBs(monto) {
    return 'Bs ' + Number(monto).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function abreviatura() {
    if (!unidad) return '';
    var texto = unidad.options[unidad.selectedIndex] ? unidad.options[unidad.selectedIndex].text : '';
    var match = texto.match(/\(([^)]+)\)/);
    return match ? match[1] : '';
  }

  function actualizarNombre() {
    if (!nombre || !previewNombre) return;
    var base = nombre.value.trim();
    if (!base) {
      previewNombre.textContent = '—';
      return;
    }

    base = base.charAt(0).toUpperCase() + base.slice(1);

    var valorPeso = peso ? peso.value.trim() : '';
    var abrev = abreviatura();

    if (valorPeso && abrev) {
      var n = parseFloat(valorPeso);
      previewNombre.textContent = base + ' ' + (Number.isInteger(n) ? n : n.toFixed(2)) + abrev;
    } else if (valorPeso) {
      previewNombre.textContent = base + ' ' + valorPeso;
    } else {
      previewNombre.textContent = base;
    }
  }

  function calcularPrecioVenta() {
    var costo = parseFloat(precioCosto ? precioCosto.value : 0) || 0;
    var pctGanancia = parseFloat(ganancia ? ganancia.value : 0) || 0;
    var pctIva = parseFloat(iva ? iva.value : 0) || 0;

    if (costo > 0) {
      var subtotal = costo * (1 + (pctGanancia / 100));
      var total = subtotal * (1 + (pctIva / 100));
      if (precioVenta) {
        precioVenta.value = total.toFixed(2);
      }
    }
    actualizarConversionBs();
  }

  function actualizarConversionBs() {
    var usd = parseFloat(precioVenta ? precioVenta.value : 0) || 0;
    var bsVal = usd * TASA_USD;
    var textoBs = formatoBs(bsVal);

    if (previewBs) {
      previewBs.textContent = textoBs;
    }
    if (conversionBsHint) {
      conversionBsHint.textContent = '≈ ' + textoBs + ' (Tasa BCV: ' + formatoBs(TASA_USD) + '/$)';
    }
  }

  if (nombre) nombre.addEventListener('input', actualizarNombre);
  if (peso) peso.addEventListener('input', actualizarNombre);
  if (unidad) unidad.addEventListener('change', actualizarNombre);

  if (precioCosto) precioCosto.addEventListener('input', calcularPrecioVenta);
  if (ganancia) ganancia.addEventListener('input', calcularPrecioVenta);
  if (iva) iva.addEventListener('input', calcularPrecioVenta);
  if (precioVenta) precioVenta.addEventListener('input', actualizarConversionBs);

  actualizarNombre();
  actualizarConversionBs();
})();
