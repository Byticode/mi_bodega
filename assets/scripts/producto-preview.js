// Vista previa en tiempo real para el nombre del producto (crear y editar)
(function () {
  'use strict';

  var nombre = document.getElementById('nombre');
  var peso = document.getElementById('peso');
  var unidad = document.getElementById('unidad');
  var preview = document.getElementById('previewNombre');

  if (!nombre || !preview) return;

  function abreviatura() {
    if (!unidad) return '';
    var texto = unidad.options[unidad.selectedIndex] ? unidad.options[unidad.selectedIndex].text : '';
    var match = texto.match(/\(([^)]+)\)/);
    return match ? match[1] : '';
  }

  function actualizar() {
    var base = nombre.value.trim();
    if (!base) {
      preview.textContent = '—';
      return;
    }

    base = base.charAt(0).toUpperCase() + base.slice(1);

    var valorPeso = peso ? peso.value.trim() : '';
    var abrev = abreviatura();

    if (valorPeso && abrev) {
      var n = parseFloat(valorPeso);
      preview.textContent = base + ' ' + (Number.isInteger(n) ? n : n.toFixed(2)) + abrev;
    } else if (valorPeso) {
      preview.textContent = base + ' ' + valorPeso;
    } else {
      preview.textContent = base;
    }
  }

  nombre.addEventListener('input', actualizar);
  if (peso) peso.addEventListener('input', actualizar);
  if (unidad) unidad.addEventListener('change', actualizar);
  actualizar();
})();
