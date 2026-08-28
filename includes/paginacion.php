<?php
/**
 * Componente visual de paginación reutilizable.
 * 
 * Variables esperadas (opcionales con fallback inteligente):
 * @var array  $paginacion      ['total' => int, 'page' => int, 'perPage' => int, 'totalPages' => int]
 * @var string $ruta_paginacion Ruta base para los enlaces (ej: 'categorias', 'proveedores')
 * @var string $search          Término de búsqueda activo (opcional)
 * @var string $label_items     Nombre en plural de los items (ej: 'categorías', 'unidades')
 */

if (!isset($paginacion) || empty($paginacion['total']) && empty($paginacion['totalPages'])) {
    return;
}

$page = max(1, (int) ($paginacion['page'] ?? 1));
$perPage = max(1, (int) ($paginacion['perPage'] ?? 10));
$total = (int) ($paginacion['total'] ?? 0);
$totalPages = max(1, (int) ($paginacion['totalPages'] ?? 1));

$label = $label_items ?? 'registros';
$ruta = $ruta_paginacion ?? (explode('/', trim($_GET['url'] ?? '', '/'))[0] ?? '');
$busqueda = $search ?? ($_GET['q'] ?? null);

$desde = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
$hasta = min($total, $page * $perPage);

// Calcular rango inteligente de páginas
$range = 2; // Páginas a los lados de la actual
$startPage = max(1, $page - $range);
$endPage = min($totalPages, $page + $range);
?>

<div class="card-foot flex-col sm:flex-row items-center justify-between gap-3 pt-3 pb-3">
  <div class="text-xs text-ink-3">
    <?php if ($total > 0): ?>
      Mostrando <span class="font-semibold text-ink"><?= $desde ?></span> a <span class="font-semibold text-ink"><?= $hasta ?></span> de <span class="font-semibold text-ink"><?= $total ?></span> <?= htmlspecialchars($label) ?>
      <?php if (!empty($busqueda)): ?>
        <span class="text-ink-3">(filtrado)</span>
      <?php endif; ?>
    <?php else: ?>
      <span>0 <?= htmlspecialchars($label) ?> encontrados</span>
    <?php endif; ?>
  </div>

  <?php if ($totalPages > 1): ?>
    <nav class="flex items-center gap-1" aria-label="Navegación de páginas">
      <!-- Botón Anterior -->
      <?php if ($page > 1): ?>
        <a href="<?= url_paginacion($ruta, $page - 1, $busqueda) ?>" 
           class="btn-icon text-ink-2 hover:text-ink hover:bg-card-2" 
           title="Página anterior" 
           aria-label="Página anterior">
          <i class="ti ti-chevron-left text-base" aria-hidden="true"></i>
        </a>
      <?php else: ?>
        <span class="btn-icon opacity-30 cursor-not-allowed" aria-disabled="true" title="Página anterior">
          <i class="ti ti-chevron-left text-base" aria-hidden="true"></i>
        </span>
      <?php endif; ?>

      <!-- Primera página si está lejos -->
      <?php if ($startPage > 1): ?>
        <a href="<?= url_paginacion($ruta, 1, $busqueda) ?>" 
           class="btn-icon text-xs font-semibold text-ink-2 hover:text-ink hover:bg-card-2"
           aria-label="Ir a página 1">
          1
        </a>
        <?php if ($startPage > 2): ?>
          <span class="px-1 text-ink-3 text-xs select-none">…</span>
        <?php endif; ?>
      <?php endif; ?>

      <!-- Páginas intermedias -->
      <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
        <?php if ($i === $page): ?>
          <span class="btn-icon text-xs font-bold bg-olive text-white shadow-sm pointer-events-none" 
                aria-current="page">
            <?= $i ?>
          </span>
        <?php else: ?>
          <a href="<?= url_paginacion($ruta, $i, $busqueda) ?>" 
             class="btn-icon text-xs font-semibold text-ink-2 hover:text-ink hover:bg-card-2"
             aria-label="Ir a página <?= $i ?>">
            <?= $i ?>
          </a>
        <?php endif; ?>
      <?php endfor; ?>

      <!-- Última página si está lejos -->
      <?php if ($endPage < $totalPages): ?>
        <?php if ($endPage < $totalPages - 1): ?>
          <span class="px-1 text-ink-3 text-xs select-none">…</span>
        <?php endif; ?>
        <a href="<?= url_paginacion($ruta, $totalPages, $busqueda) ?>" 
           class="btn-icon text-xs font-semibold text-ink-2 hover:text-ink hover:bg-card-2"
           aria-label="Ir a página <?= $totalPages ?>">
          <?= $totalPages ?>
        </a>
      <?php endif; ?>

      <!-- Botón Siguiente -->
      <?php if ($page < $totalPages): ?>
        <a href="<?= url_paginacion($ruta, $page + 1, $busqueda) ?>" 
           class="btn-icon text-ink-2 hover:text-ink hover:bg-card-2" 
           title="Página siguiente" 
           aria-label="Página siguiente">
          <i class="ti ti-chevron-right text-base" aria-hidden="true"></i>
        </a>
      <?php else: ?>
        <span class="btn-icon opacity-30 cursor-not-allowed" aria-disabled="true" title="Página siguiente">
          <i class="ti ti-chevron-right text-base" aria-hidden="true"></i>
        </span>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
</div>
