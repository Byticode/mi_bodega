<?php
// Rail de navegación compartido. Marca el ítem activo según $_GET['controller'] o la ruta limpia.

$currentController = $_GET['controller'] ?? '';
$urlPath           = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$resource          = !empty($urlPath) ? explode('/', $urlPath)[0] : '';
$currentAction     = $_GET['action'] ?? (!empty(explode('/', $urlPath)[1]) ? explode('/', $urlPath)[1] : '');

$isActive = function (array $controllers, array $resources, ?string $targetAction = null) use ($currentController, $resource, $currentAction): bool {
    $matchController = in_array($currentController, $controllers, true) || in_array($resource, $resources, true);
    if (!$matchController) return false;
    if ($targetAction !== null) {
        return $currentAction === $targetAction;
    }
    return true;
};

/** Enlace principal del rail. */
$navLink = function (string $href, string $label, string $icon, bool $active): string {
    return '<a href="' . $href . '" class="nav-link"' . ($active ? ' aria-current="page"' : '') . '>'
        . '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">'
        . $icon
        . '</svg><span>' . $label . '</span></a>';
};

/** Enlace del submenú de configuración. */
$subLink = function (string $href, string $label, string $icon, bool $active): string {
    return '<a href="' . $href . '" class="nav-sublink"'
        . ($active ? ' aria-current="page"' : '') . '>'
        . '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">'
        . $icon
        . '</svg><span>' . $label . '</span></a>';
};

// Iconos: Heroicons outline, stroke 1.5 — un solo set en todo el proyecto.
$icons = [
    'pos'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>',
    'inventario'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>',
    'ventas'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>',
    'surtido'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>',
    'config'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
    'categorias'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>',
    'unidades'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c.665.111 1.325.24 1.98.386M18.75 4.97l2.311 6.877a3.75 3.75 0 01-5.516 4.278M5.25 4.97c-.665.111-1.325.24-1.98.386M5.25 4.97L2.94 11.847a3.75 3.75 0 005.516 4.278"/>',
    'proveedores' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72l1.189-1.19A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72"/>',
    'clientes'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>',
    'tasa'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'usuarios'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>',
];

$configControllers = ['categoriasController', 'unidadesController', 'proveedoresController', 'clientesController', 'tasaMonedaController', 'usuariosController'];
$configResources   = ['categorias', 'unidades', 'proveedores', 'clientes', 'tasa-moneda', 'tasamoneda', 'usuarios'];
$configOpen        = $isActive($configControllers, $configResources);

$dias  = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
$meses = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$fecha = ucfirst($dias[(int) date('w')]) . ' ' . date('j') . ' de ' . $meses[(int) date('n')];
?>

<!-- Barra superior (solo móvil) -->
<div class="topbar">
  <button id="sidebarOpenBtn" type="button" class="btn-icon" aria-label="Abrir menú de navegación" aria-controls="sidebar" aria-expanded="false">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
  </button>
  <span class="sidebar-wordmark">mi_bodega</span>
</div>

<!-- Fondo oscurecido del menú móvil -->
<div id="sidebarOverlay" class="sidebar-overlay" data-open="false"></div>

<!-- RAIL DE NAVEGACIÓN -->
<aside id="sidebar" class="sidebar" data-open="false" aria-label="Navegación principal">

  <div class="sidebar-brand">
    <span class="sidebar-mark" aria-hidden="true">MB</span>
    <div class="min-w-0">
      <span class="sidebar-wordmark">mi_bodega</span>
      <span class="sidebar-tagline">Control de mercancía</span>
    </div>
    <button id="sidebarCloseBtn" type="button" class="btn-icon ml-auto md:hidden" aria-label="Cerrar menú de navegación">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>

  <nav class="sidebar-nav" aria-label="Secciones">
    <p class="sidebar-section" id="nav-operacion">Operación</p>
    <?= $navLink(url('pos'), 'Punto de venta', $icons['pos'], $isActive(['ventasController'], ['pos', 'ventas'], 'pos')) ?>
    <?= $navLink(url('productos'), 'Inventario', $icons['inventario'], $isActive(['productosController'], ['productos'])) ?>
    <?= $navLink(url('ventas'), 'Ventas', $icons['ventas'], $isActive(['ventasController'], ['ventas']) && $currentAction !== 'pos') ?>
    <?= $navLink(url('surtidos'), 'Surtido', $icons['surtido'], $isActive(['surtidosController'], ['surtidos'])) ?>

    <p class="sidebar-section">Ajustes</p>
    <button id="configBtn" type="button" class="nav-disclosure" aria-expanded="<?= $configOpen ? 'true' : 'false' ?>" aria-controls="configMenu">
      <span class="flex items-center gap-3">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><?= $icons['config'] ?></svg>
        <span>Configuración</span>
      </span>
      <svg class="nav-caret" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
    </button>

    <div id="configMenu" class="nav-submenu" <?= $configOpen ? '' : 'hidden' ?>>
      <?= $subLink(url('categorias'), 'Categorías', $icons['categorias'], $isActive(['categoriasController'], ['categorias'])) ?>
      <?= $subLink(url('unidades'), 'Unidades', $icons['unidades'], $isActive(['unidadesController'], ['unidades'])) ?>
      <?= $subLink(url('proveedores'), 'Proveedores', $icons['proveedores'], $isActive(['proveedoresController'], ['proveedores'])) ?>
      <?= $subLink(url('clientes'), 'Clientes', $icons['clientes'], $isActive(['clientesController'], ['clientes'])) ?>
      <?= $subLink(url('tasa-moneda'), 'Tasa de cambio', $icons['tasa'], $isActive(['tasaMonedaController'], ['tasa-moneda', 'tasamoneda'])) ?>
      <?php if (($_SESSION['usuario']['usuario_rol'] ?? '') === 'admin'): ?>
        <?= $subLink(url('usuarios'), 'Usuarios', $icons['usuarios'], $isActive(['usuariosController'], ['usuarios'])) ?>
      <?php endif; ?>
    </div>
  </nav>

  <!-- User & Logout section in Sidebar -->
  <div class="border-t border-gray-200/80 pt-3 pb-2 px-2 space-y-2">
    <?php if (!empty($_SESSION['usuario'])): ?>
      <div class="flex items-center justify-between px-1">
        <div class="flex items-center space-x-2.5 overflow-hidden">
          <div class="w-8 h-8 rounded-lg bg-olive text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
            <?= strtoupper(substr($_SESSION['usuario']['usuario_nombre'] ?? 'U', 0, 2)) ?>
          </div>
          <div class="overflow-hidden">
            <p class="text-xs font-semibold text-gray-900 truncate"><?= htmlspecialchars($_SESSION['usuario']['usuario_nombre'] ?? 'Usuario') ?></p>
            <span class="text-[10px] text-gray-500 capitalize block truncate"><?= htmlspecialchars($_SESSION['usuario']['usuario_rol'] ?? 'Vendedor') ?></span>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <a href="<?= url('logout') ?>" 
       class="flex items-center space-x-2 px-2.5 py-1.5 text-xs font-medium text-rose-600 rounded-lg hover:bg-rose-50 transition-colors w-full">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H8.25" />
      </svg>
      <span>Cerrar Sesión</span>
    </a>
  </div>

  <div class="sidebar-foot">
    <?= htmlspecialchars($fecha) ?>
  </div>
</aside>
