// Conversor Bs ↔ $ de la página de tasa de cambio.
(function () {
  'use strict';

  var TASA = Number(window.TASA_USD || 0);
  var bs = document.getElementById('conv_bs');
  var usd = document.getElementById('conv_usd');

  if (!TASA || !bs || !usd) return;

  bs.addEventListener('input', function () {
    usd.value = bs.value === '' ? '' : (parseFloat(bs.value) / TASA).toFixed(2);
  });

  usd.addEventListener('input', function () {
    bs.value = usd.value === '' ? '' : (parseFloat(usd.value) * TASA).toFixed(2);
  });
})();
