<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Surtidos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Geist', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            display: ['Fraunces', 'Georgia', 'serif'],
            mono: ['Geist Mono', 'ui-monospace', 'monospace']
          },
          colors: {
            warmBg: '#fcfbf7',
            warmCard: '#f5f3ec',
            olive: { DEFAULT: '#3a6341', hover: '#2f5135', light: '#eaf0eb' }
          }
        }
      }
    }
  </script>
</head>
<body class="bg-warmBg text-gray-800 font-sans min-h-screen flex">

  <!-- SIDEBAR -->
  <?php
  include ruta . '/includes/sidebar.php';
  ?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="flex-1 p-6 space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-display text-2xl font-semibold text-gray-900">Surtidos</h2>
        <p class="text-xs text-gray-500">Historial de compras y surtidos de productos</p>
      </div>
      <a href="index.php?controller=surtidosController&action=crear" class="bg-olive hover:bg-olive-hover text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Nuevo Surtido
      </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="flex items-center gap-3 p-4 text-sm text-rose-800 border border-rose-200/80 rounded-2xl bg-rose-50/80 shadow-sm" role="alert">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
        <div class="flex-1">
          <span class="font-semibold">Ha ocurrido un error.</span> <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-rose-800 p-1 rounded-lg hover:bg-rose-100/60 transition-colors">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="flex items-center gap-3 p-4 text-sm text-green-800 border border-green-200/80 rounded-2xl bg-green-50/80 shadow-sm" role="alert">
        <i data-lucide="alert-circle" class="w-5 h-5 text-green-600 shrink-0"></i>
        <div class="flex-1">
          <span class="font-semibold">Correcto.</span> <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-green-800 p-1 rounded-lg hover:bg-green-100/60 transition-colors">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- TABLA DE SURTIDOS -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase">
            <tr>
              <th class="p-3"># Surtido</th>
              <th class="p-3">Proveedor</th>
              <th class="p-3 text-center">Productos</th>
              <th class="p-3 text-right">Costo Total</th>
              <th class="p-3 text-right">Fecha</th>
              <th class="p-3 text-right">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php if (empty($surtidos)): ?>
              <tr>
                <td colspan="6" class="p-6 text-center text-gray-500">
                  <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                  </svg>
                  <p class="font-medium">No hay surtidos registrados</p>
                  <p class="text-xs">Haz clic en "Nuevo Surtido" para comenzar</p>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($surtidos as $surtido): ?>
                <tr class="hover:bg-gray-50">
                  <td class="p-3 font-mono font-semibold text-olive">#<?= $surtido['surtido_id'] ?></td>
                  <td class="p-3 font-medium text-gray-900"><?= htmlspecialchars($surtido['proveedor_nombre'] ?? 'Sin proveedor') ?></td>
                  <td class="p-3 text-center"><?= $surtido['total_productos'] ?></td>
                  <td class="p-3 text-right font-semibold">Bs. <?= number_format($surtido['surtido_costo_total'], 2) ?></td>
                  <td class="p-3 text-right text-gray-500"><?= date('d/m/Y H:i', strtotime($surtido['surtido_fecha'])) ?></td>
                  <td class="p-3 text-right">
                    <a href="index.php?controller=surtidosController&action=ver&id=<?= $surtido['surtido_id'] ?>" class="inline-block p-1.5 text-gray-500 hover:text-olive hover:bg-gray-100 rounded-md transition-colors" title="Ver detalles">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Resumen rápido -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <p class="text-xs text-gray-500">Total Surtidos</p>
        <p class="text-2xl font-bold text-gray-900"><?= count($surtidos) ?></p>
      </div>
      <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <p class="text-xs text-gray-500">Inversión Total</p>
        <p class="text-2xl font-bold text-olive">
          Bs. <?= number_format(array_sum(array_column($surtidos, 'surtido_costo_total')), 2) ?>
        </p>
      </div>
      <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <p class="text-xs text-gray-500">Último Surtido</p>
        <p class="text-lg font-bold text-gray-900">
          <?= !empty($surtidos) ? date('d/m/Y', strtotime($surtidos[0]['surtido_fecha'])) : 'N/A' ?>
        </p>
      </div>
    </div>
  </main>

  <?php
  include ruta . '/includes/sidebar.js';
  ?>
</body>
</html>