// Interacción del Menú Lateral (Sidebar) y Menús Desplegables con Accesibilidad ARIA
(function () {
  'use strict';

  var MOBILE = window.matchMedia('(max-width: 767px)');

  /* ── Submenú de configuración ─────────────────────────────────────── */
  var configBtn = document.getElementById('configBtn');
  var configMenu = document.getElementById('configMenu');

  if (configBtn && configMenu) {
    configBtn.addEventListener('click', function () {
      var willOpen = configBtn.getAttribute('aria-expanded') !== 'true';
      configBtn.setAttribute('aria-expanded', String(willOpen));
      configMenu.hidden = !willOpen;
    });
  }

  /* ── Rail off-canvas en móvil ─────────────────────────────────────── */
  var sidebar = document.getElementById('sidebar');
  var overlay = document.getElementById('sidebarOverlay');
  var openBtn = document.getElementById('sidebarOpenBtn');
  var closeBtn = document.getElementById('sidebarCloseBtn');

  if (!sidebar || !overlay || !openBtn) return;

  var lastFocused = null;

  function isOpen() {
    return sidebar.dataset.open === 'true';
  }

  function focusables() {
    return Array.prototype.filter.call(
      sidebar.querySelectorAll('a[href], button:not([disabled])'),
      function (el) { return el.offsetParent !== null; }
    );
  }

  function setOpen(open) {
    sidebar.dataset.open = String(open);
    overlay.dataset.open = String(open);
    openBtn.setAttribute('aria-expanded', String(open));
    document.body.style.overflow = open ? 'hidden' : '';
    syncInert();

    if (open) {
      lastFocused = document.activeElement;
      var first = focusables()[0];
      if (first) first.focus();
    } else if (lastFocused && typeof lastFocused.focus === 'function') {
      lastFocused.focus();
      lastFocused = null;
    }
  }

  function syncInert() {
    var hide = MOBILE.matches && !isOpen();
    if (hide) {
      sidebar.setAttribute('inert', '');
    } else {
      sidebar.removeAttribute('inert');
    }
  }

  openBtn.addEventListener('click', function () { setOpen(true); });
  overlay.addEventListener('click', function () { setOpen(false); });
  if (closeBtn) closeBtn.addEventListener('click', function () { setOpen(false); });

  sidebar.addEventListener('click', function (e) {
    if (e.target.closest('a[href]') && MOBILE.matches) setOpen(false);
  });

  document.addEventListener('keydown', function (e) {
    if (!isOpen()) return;

    if (e.key === 'Escape') {
      setOpen(false);
      return;
    }

    if (e.key === 'Tab') {
      var items = focusables();
      if (!items.length) return;
      var first = items[0];
      var last = items[items.length - 1];

      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  });

  MOBILE.addEventListener('change', function (e) {
    if (!e.matches && isOpen()) setOpen(false);
    syncInert();
  });

  syncInert();
})();
