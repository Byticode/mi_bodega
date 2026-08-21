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
        . '<i class="ti ' . $icon . '" aria-hidden="true"></i>'
        . '<span>' . $label . '</span></a>';
};

/** Enlace del submenú de configuración. */
$subLink = function (string $href, string $label, string $icon, bool $active): string {
    return '<a href="' . $href . '" class="nav-sublink"'
        . ($active ? ' aria-current="page"' : '') . '>'
        . '<i class="ti ' . $icon . '" aria-hidden="true"></i>'
        . '<span>' . $label . '</span></a>';
};

// Iconos: Tabler Icons (webfont). Un solo set en todo el proyecto.
$icons = [
    'pos'         => 'ti-shopping-cart',
    'inventario'  => 'ti-package',
    'ventas'      => 'ti-chart-bar',
    'surtido'     => 'ti-truck-delivery',
    'config'      => 'ti-settings',
    'categorias'  => 'ti-tag',
    'unidades'    => 'ti-scale',
    'proveedores' => 'ti-building-store',
    'clientes'    => 'ti-users',
    'tasa'        => 'ti-currency-dollar',
    'usuarios'    => 'ti-user-circle',
    'reportes'    => 'ti-report-analytics',
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
    <i class="ti ti-menu-2 text-lg" aria-hidden="true"></i>
  </button>
  <span class="sidebar-wordmark">Mi Bodega</span>
</div>

<!-- Fondo oscurecido del menú móvil -->
<div id="sidebarOverlay" class="sidebar-overlay" data-open="false"></div>

<!-- RAIL DE NAVEGACIÓN -->
<aside id="sidebar" class="sidebar" data-open="false" aria-label="Navegación principal">

  <div class="sidebar-brand">
    <span class="sidebar-mark" aria-hidden="true">MB</span>
    <div class="min-w-0">
      <span class="sidebar-wordmark">Mi Bodega</span>
      <span class="sidebar-tagline">Control de inventario</span>
    </div>
    <button id="sidebarCloseBtn" type="button" class="btn-icon ml-auto md:hidden" aria-label="Cerrar menú de navegación">
      <i class="ti ti-x text-lg" aria-hidden="true"></i>
    </button>
  </div>

  <nav class="sidebar-nav" aria-label="Secciones">
    <p class="sidebar-section" id="nav-operacion">Operación</p>
    <?= $navLink(url('pos'), 'Punto de venta', $icons['pos'], $isActive(['ventasController'], ['pos', 'ventas'], 'pos')) ?>
    <?= $navLink(url('productos'), 'Inventario', $icons['inventario'], $isActive(['productosController'], ['productos'])) ?>
    <?= $navLink(url('ventas'), 'Ventas', $icons['ventas'], $isActive(['ventasController'], ['ventas']) && $currentAction !== 'pos') ?>
    <?= $navLink(url('surtidos'), 'Surtido', $icons['surtido'], $isActive(['surtidosController'], ['surtidos'])) ?>
    <?= $navLink(url('reportes'), 'Reportes', $icons['reportes'], $isActive(['reportesController'], ['reportes'])) ?>

    <p class="sidebar-section">Ajustes</p>
    <button id="configBtn" type="button" class="nav-disclosure" aria-expanded="<?= $configOpen ? 'true' : 'false' ?>" aria-controls="configMenu">
      <span class="flex items-center gap-3">
        <i class="ti <?= $icons['config'] ?>" aria-hidden="true"></i>
        <span>Configuración</span>
      </span>
      <i class="ti ti-chevron-down nav-caret" aria-hidden="true"></i>
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

  <!-- Usuario y cierre de sesión -->
  <div class="border-t border-rule pt-3 pb-2 px-2 flex flex-col gap-2">
    <?php if (!empty($_SESSION['usuario'])): ?>
      <div class="flex items-center gap-2.5 px-1 min-w-0">
        <span class="w-8 h-8 rounded-lg bg-olive text-white font-bold flex items-center justify-center text-xs shrink-0" aria-hidden="true">
          <?= strtoupper(substr($_SESSION['usuario']['usuario_nombre'] ?? 'U', 0, 2)) ?>
        </span>
        <div class="min-w-0">
          <p class="text-xs font-semibold text-ink truncate"><?= htmlspecialchars($_SESSION['usuario']['usuario_nombre'] ?? 'Usuario') ?></p>
          <span class="text-xs text-ink-3 capitalize block truncate"><?= htmlspecialchars($_SESSION['usuario']['usuario_rol'] ?? 'Vendedor') ?></span>
        </div>
      </div>
    <?php endif; ?>

    <a href="<?= url('logout') ?>" class="logout-link">
      <i class="ti ti-logout" aria-hidden="true"></i>
      <span>Cerrar sesión</span>
    </a>
  </div>

  <div class="sidebar-foot">
    <span class="block"><?= htmlspecialchars($fecha) ?></span>

    <?php
    $tasa_rail  = tasa_vigente();
    $rail_fresca = in_array($tasa_rail['origen'], ['api', 'cache'], true);
    ?>
    <a href="<?= url('tasa-moneda') ?>" class="rate-chip"
       title="<?= $rail_fresca ? 'Tasa del BCV al día' : 'No se pudo consultar la API de tasas' ?>">
      <span class="badge-dot <?= $rail_fresca ? 'text-success' : 'text-warn' ?>" aria-hidden="true"></span>
      <?php if ($tasa_rail['tasa_usd']): ?>
        <span class="money"><?= money($tasa_rail['tasa_usd']) ?></span>
        <span class="text-ink-3">/ $</span>
      <?php else: ?>
        <span>Sin tasa</span>
      <?php endif; ?>
      <span class="sr-only">
        <?= $rail_fresca ? 'Tasa actualizada desde la API.' : 'Tasa desactualizada: la API no respondió.' ?>
        Ir a la página de tasa de cambio.
      </span>
    </a>
  </div>
</aside>
