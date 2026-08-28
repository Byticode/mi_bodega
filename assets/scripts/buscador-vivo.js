/**
 * Buscador en vivo (live search) con debounce para tablas del sistema.
 * Permite buscar mientras se escribe sin necesidad de presionar Enter o botón,
 * y maneja el botón 'x' para limpiar la búsqueda de forma instantánea.
 */
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('form.search, form[data-buscador]').forEach(function (form) {
    var input = form.querySelector('input[name="q"], input[name="search"]');
    var btnClear = form.querySelector('.btn-clear-search');
    if (!input) return;

    var timer = null;

    function toggleClear() {
      if (btnClear) {
        btnClear.style.display = input.value.trim() !== '' ? 'flex' : 'none';
      }
    }

    toggleClear();

    // Si viene con búsqueda activa y la página se acaba de cargar por esa búsqueda,
    // restaurar el foco y ubicar el cursor al final para no interrumpir la experiencia.
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('q') && urlParams.get('q') === input.value && input.value.length > 0) {
      input.focus();
      var len = input.value.length;
      input.setSelectionRange(len, len);
    }

    input.addEventListener('input', function () {
      toggleClear();
      clearTimeout(timer);
      timer = setTimeout(function () {
        // Enviar formulario automáticamente al terminar de escribir
        form.submit();
      }, 350);
    });

    if (btnClear) {
      btnClear.addEventListener('click', function (e) {
        e.preventDefault();
        input.value = '';
        toggleClear();
        form.submit();
      });
    }
  });
});
