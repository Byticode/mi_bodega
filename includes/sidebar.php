<?php
// Rail de navegación compartido. Marca el ítem activo según $_GET['controller'] o la ruta limpia.

$currentController = $_GET['controller'] ?? '';
$urlPath = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$resource = !empty($urlPath) ? explode('/', $urlPath)[0] : '';
$currentAction = $_GET['action'] ?? (!empty(explode('/', $urlPath)[1]) ? explode('/', $urlPath)[1] : '');

$isActive = function(array $controllers, array $resources, ?string $targetAction = null) use ($currentController, $resource, $currentAction): bool {
    $matchController = in_array($currentController, $controllers, true) || in_array($resource, $resources, true);
    if (!$matchController) return false;
    if ($targetAction !== null) {
        return $currentAction === $targetAction;
    }
    return true;
};
?>

<!-- Botón para abrir el menú en móvil -->
<button id="sidebarOpenBtn" type="button" aria-label="Abrir menú" aria-controls="sidebar"
  class="btn-secondary btn fixed top-4 left-4 z-40 md:hidden px-2.5!">
  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
</button>

<!-- Overlay móvil -->
<div id="sidebarOverlay" class="fixed inset-0 z-30 bg-ink/40 hidden md:hidden"></div>

<!-- SIDEBAR -->
<aside id="sidebar" class="w-64 bg-white border-r border-gray-200 min-h-screen flex flex-col justify-between p-4 fixed left-0 top-0 bottom-0 z-40 -translate-x-full md:translate-x-0 transition-transform duration-200">
    <div class="space-y-6">
        <div class="flex items-center space-x-3 px-2 py-1">
            <div class="w-10 h-10 bg-olive text-white font-bold rounded flex items-center justify-center shrink-0">MB</div>
            <div>
                <h1 class="font-bold text-gray-900 leading-none">mi_bodega</h1>
                <span class="text-xs text-gray-500">Control de mercancía</span>
            </div>
        </div>
        <nav class="space-y-1">
            <!-- POS -->
            <a href="<?= url('pos') ?>" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors <?= $isActive(['ventasController'], ['pos', 'ventas'], 'pos') ? 'bg-olive-light text-olive' : '' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                </svg>
                <span>POS</span>
            </a>

            <!-- Inventario -->
            <a href="<?= url('productos') ?>" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors <?= $isActive(['productosController'], ['productos']) ? 'bg-olive-light text-olive' : '' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span>Inventario</span>
            </a>

            <!-- Ventas -->
            <a href="<?= url('ventas') ?>" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors <?= $isActive(['ventasController'], ['ventas']) && $currentAction !== 'pos' ? 'bg-olive-light text-olive' : '' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>Ventas</span>
            </a>

            <!-- Surtido -->
            <a href="<?= url('surtidos') ?>" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors <?= $isActive(['surtidosController'], ['surtidos']) ? 'bg-olive-light text-olive' : '' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <span>Surtido</span>
            </a>

            <!-- Configuración Desplegable -->
            <?php $isConfigActive = $isActive(['categoriasController', 'unidadesController', 'proveedoresController', 'clientesController', 'tasaMonedaController', 'usuariosController'], ['categorias', 'unidades', 'proveedores', 'clientes', 'tasa-moneda', 'tasamoneda', 'usuarios']); ?>
            <div class="relative pt-2">
                <button id="configBtn" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-lg <?= $isConfigActive ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-100' ?> focus:outline-none transition-colors">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Configuración</span>
                    </div>
                    <svg id="configArrow" class="w-4 h-4 transition-transform duration-200 <?= $isConfigActive ? 'rotate-0' : 'rotate-180' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="configMenu" class="pl-8 pr-2 py-1 space-y-1 <?= $isConfigActive ? '' : 'hidden' ?>">
                    <!-- Categorías -->
                    <a href="<?= url('categorias') ?>" class="flex items-center space-x-2.5 px-3 py-2 text-xs font-medium rounded-md transition-colors <?= $isActive(['categoriasController'], ['categorias']) ? 'text-olive bg-olive-light' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M13 7h7m-7 4h7m-7 4h7M3 5h18a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V6a1 1 0 011-1z"></path>
                        </svg>
                        <span>Categorías</span>
                    </a>

                    <!-- Unidades -->
                    <a href="<?= url('unidades') ?>" class="flex items-center space-x-2.5 px-3 py-2 text-xs font-medium rounded-md transition-colors <?= $isActive(['unidadesController'], ['unidades']) ? 'text-olive bg-olive-light' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Unidades</span>
                    </a>

                    <!-- Proveedores -->
                    <a href="<?= url('proveedores') ?>" class="flex items-center space-x-2.5 px-3 py-2 text-xs font-medium rounded-md transition-colors <?= $isActive(['proveedoresController'], ['proveedores']) ? 'text-olive bg-olive-light' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                        </svg>
                        <span>Proveedores</span>
                    </a>

                    <!-- Clientes -->
                    <a href="<?= url('clientes') ?>" class="flex items-center space-x-2.5 px-3 py-2 text-xs font-medium rounded-md transition-colors <?= $isActive(['clientesController'], ['clientes']) ? 'text-olive bg-olive-light' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Clientes</span>
                    </a>

                    <!-- Tasa Moneda -->
                    <a href="<?= url('tasa-moneda') ?>" class="flex items-center space-x-2.5 px-3 py-2 text-xs font-medium rounded-md transition-colors <?= $isActive(['tasaMonedaController'], ['tasa-moneda', 'tasamoneda']) ? 'text-olive bg-olive-light' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Tasa Moneda</span>
                    </a>

                    <!-- Usuarios (Admin Only) -->
                    <?php if (($_SESSION['usuario']['usuario_rol'] ?? '') === 'admin'): ?>
                        <a href="<?= url('usuarios') ?>" class="flex items-center space-x-2.5 px-3 py-2 text-xs font-medium rounded-md transition-colors <?= $isActive(['usuariosController'], ['usuarios']) ? 'text-olive bg-olive-light' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>Usuarios</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>

    <!-- Footer del Sidebar con Usuario y Cierre de Sesión -->
    <div class="border-t border-gray-200 pt-4 space-y-3">
        <?php if (!empty($_SESSION['usuario'])): ?>
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-lg bg-olive text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-sm">
                        <?= strtoupper(substr($_SESSION['usuario']['usuario_nombre'] ?? 'U', 0, 2)) ?>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-semibold text-gray-900 truncate"><?= htmlspecialchars($_SESSION['usuario']['usuario_nombre'] ?? 'Usuario') ?></p>
                        <span class="text-[11px] text-gray-500 capitalize block truncate"><?= htmlspecialchars($_SESSION['usuario']['usuario_rol'] ?? 'Vendedor') ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <a href="<?= url('logout') ?>" 
           class="flex items-center space-x-2.5 px-3 py-2 text-xs font-medium text-rose-600 rounded-lg hover:bg-rose-50 transition-colors w-full">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H8.25" />
            </svg>
            <span>Cerrar Sesión</span>
        </a>
    </div>
</aside>
