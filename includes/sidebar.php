<?php
// Rail de navegación compartido. Marca el ítem activo según controller + action.

$current       = $_GET['controller'] ?? '';
$currentAction = $_GET['action'] ?? '';

/**
 * Devuelve true si el ítem apunta a la vista que se está mostrando.
 * $action = null significa "cualquier acción de este controlador".
 */
$isActive = function (string $controller, ?string $action = null) use ($current, $currentAction): bool {
    if ($current !== $controller) {
        return false;
    }
    return $action === null || $currentAction === $action;
};

/** Enlace principal del rail. */
$navLink = function (string $href, string $label, string $icon, bool $active): string {
    return '<a href="' . $href . '" class="nav-link"' . ($active ? ' aria-current="page"' : '') . '>'
        . '<i class="ti ' . $icon . '" aria-hidden="true"></i>'
        . '<span>' . $label . '</span></a>';
};

/** Enlace del submenú de configuración. */
$subLink = function (string $controller, string $label, string $icon, bool $active): string {
    return '<a href="index.php?controller=' . $controller . '&action=listar" class="nav-sublink"'
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
];

// El submenú de configuración se abre solo si estás dentro de una de sus secciones.
$configControllers = ['categoriasController', 'unidadesController', 'proveedoresController', 'clientesController', 'tasaMonedaController', 'usuariosController'];
$configOpen        = in_array($current, $configControllers, true);

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
    <?= $navLink('index.php?controller=ventasController&action=pos', 'Punto de venta', $icons['pos'], $isActive('ventasController', 'pos')) ?>
    <?= $navLink('index.php?controller=productosController&action=listar', 'Inventario', $icons['inventario'], $isActive('productosController')) ?>
    <?= $navLink('index.php?controller=ventasController&action=listar', 'Ventas', $icons['ventas'], $isActive('ventasController') && $currentAction !== 'pos') ?>
    <?= $navLink('index.php?controller=surtidosController&action=listar', 'Surtido', $icons['surtido'], $isActive('surtidosController')) ?>

    <p class="sidebar-section">Ajustes</p>
    <button id="configBtn" type="button" class="nav-disclosure" aria-expanded="<?= $configOpen ? 'true' : 'false' ?>" aria-controls="configMenu">
      <span class="flex items-center gap-3">
        <i class="ti <?= $icons['config'] ?>" aria-hidden="true"></i>
        <span>Configuración</span>
      </span>
      <i class="ti ti-chevron-down nav-caret" aria-hidden="true"></i>
    </button>

    <div id="configMenu" class="nav-submenu" <?= $configOpen ? '' : 'hidden' ?>>
      <?= $subLink('categoriasController',  'Categorías',     $icons['categorias'],  $isActive('categoriasController')) ?>
      <?= $subLink('unidadesController',    'Unidades',       $icons['unidades'],    $isActive('unidadesController')) ?>
      <?= $subLink('proveedoresController', 'Proveedores',    $icons['proveedores'], $isActive('proveedoresController')) ?>
      <?= $subLink('clientesController',    'Clientes',       $icons['clientes'],    $isActive('clientesController')) ?>
      <?= $subLink('tasaMonedaController',  'Tasa de cambio', $icons['tasa'],        $isActive('tasaMonedaController')) ?>
      <?= $subLink('usuariosController',    'Usuarios',       $icons['usuarios'],    $isActive('usuariosController')) ?>
    </div>
  </nav>

  <div class="sidebar-foot">
    <span class="block"><?= htmlspecialchars($fecha) ?></span>

    <?php
    $tasa_rail  = tasa_vigente();
    $rail_fresca = in_array($tasa_rail['origen'], ['api', 'cache'], true);
    ?>
    <a href="index.php?controller=tasaMonedaController&action=listar" class="rate-chip"
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
