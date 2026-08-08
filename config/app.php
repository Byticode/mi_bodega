<?php

define('BASE_URL', '/mi_bodega/');
define('APP_NAME', 'mi_bodega');
define('DEFAULT_CONTROLLER', 'VentasController');
define('DEFAULT_ACTION', 'pos');

/* ── Tasas de cambio (DolarAPI Venezuela) ──────────────────────────────────
 * Fuente pública, sin clave. `oficial` es la del BCV y es la que usa la app
 * para convertir Bs → $; `paralelo` se guarda y se muestra como referencia.
 */
define('TASA_API_USD', 'https://ve.dolarapi.com/v1/dolares');
define('TASA_API_EUR', 'https://ve.dolarapi.com/v1/euros');

/** Cada cuánto se vuelve a consultar la API (segundos). El BCV publica una vez al día. */
define('TASA_TTL', 1800);

/** TTL corto tras un fallo, para no pagar el timeout en cada carga de página. */
define('TASA_TTL_ERROR', 300);

define('TASA_TIMEOUT_CONEXION', 3);
define('TASA_TIMEOUT_TOTAL', 6);

/** A partir de cuántos segundos la tasa se considera vieja y se avisa en pantalla. */
define('TASA_ANTIGUA', 86400);

define('TASA_CACHE_DIR', __DIR__ . '/../storage');
