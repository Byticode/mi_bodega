<?php
// Rail de navegación compartido. Marca el ítem activo según $_GET['controller'].

$current = $_GET['controller'] ?? 'categoriasController';

$navLink = function (string $controller, string $label, string $iconPath) use ($current): string {
    $active = $current === $controller;
    $classes = $active
        ? 'bg-olive-light text-olive font-semibold'
        : 'text-ink-2 hover:bg-card-2 hover:text-ink';
    $aria = $active ? ' aria-current="page"' : '';
    return '<a href="index.php?controller=' . $controller . '&action=listar"' . $aria
        . ' class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors ' . $classes . '">'
        . '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">'
        . '<path stroke-linecap="round" stroke-linejoin="round" d="' . $iconPath . '"/></svg>'
        . '<span>' . $label . '</span></a>';
};

$configControllers = ['productosBaseController', 'credencialesController', 'proveedoresController', 'categoriasController', 'clientesController'];
$configOpen = in_array($current, $configControllers, true);

$dias  = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
$meses = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$fecha = ucfirst($dias[(int) date('w')]) . ' ' . date('j') . ' de ' . $meses[(int) date('n')];
?>

<!-- Botón para abrir el menú en móvil -->
<button id="sidebarOpenBtn" type="button" aria-label="Abrir menú" aria-controls="sidebar"
  class="btn-secondary btn fixed top-4 left-4 z-40 md:hidden px-2.5!">
  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
</button>

<!-- Overlay móvil -->
<div id="sidebarOverlay" class="fixed inset-0 z-30 bg-ink/40 hidden md:hidden"></div>

<!-- SIDEBAR -->
<aside id="sidebar"
  class="w-64 bg-card border-r border-rule min-h-screen flex flex-col justify-between p-4 fixed left-0 top-0 bottom-0 z-40 -translate-x-full md:translate-x-0 transition-transform duration-200">
  <div class="space-y-6">
    <div class="flex items-center gap-3 px-2 py-1">
      <div class="w-10 h-10 bg-olive text-white rounded-lg flex items-center justify-center shrink-0 font-display font-semibold">MB</div>
      <div>
        <h1 class="font-display font-semibold text-ink leading-none tracking-tight">mi_bodega</h1>
        <span class="text-xs text-ink-3">Control de mercancía</span>
      </div>
    </div>

    <nav class="space-y-1" aria-label="Navegación principal">
      <?= $navLink('posController', 'POS', 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z') ?>
      <?= $navLink('inventarioController', 'Inventario', 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9') ?>
      <?= $navLink('ventasController', 'Ventas', 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z') ?>
      <?= $navLink('surtidosController', 'Surtido', 'M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5') ?>

      <div class="pt-2">
        <button id="configBtn" type="button" aria-expanded="<?= $configOpen ? 'true' : 'false' ?>" aria-controls="configMenu"
          class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-ink-2 hover:bg-card-2 hover:text-ink transition-colors">
          <span class="flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Configuración</span>
          </span>
          <svg id="configArrow" class="w-4 h-4 transition-transform duration-200 <?= $configOpen ? '' : 'rotate-180' ?>" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
        </button>

        <div id="configMenu" class="pl-6 pr-1 py-1 space-y-0.5 <?= $configOpen ? '' : 'hidden' ?>">
          <?= $navLink('productosBaseController', 'Productos base', 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z') ?>
          <?= $navLink('credencialesController', 'Credenciales', 'M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z') ?>
          <?= $navLink('proveedoresController', 'Proveedores', 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12') ?>
          <?= $navLink('categoriasController', 'Categorías', 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3zM6 6h.008v.008H6V6z') ?>
          <?= $navLink('clientesController', 'Clientes', 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z') ?>
        </div>
      </div>
    </nav>
  </div>

  <div class="border-t border-rule pt-4 flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-olive-light text-olive flex items-center justify-center font-semibold text-xs shrink-0">MB</div>
    <span class="text-xs text-ink-3 capitalize"><?= $fecha ?></span>
  </div>
</aside>
