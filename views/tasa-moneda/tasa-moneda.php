<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Tasa de Cambio</title>
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
            olive: {
              DEFAULT: '#3a6341',
              hover: '#2f5135',
              light: '#eaf0eb'
            }
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
  <main class="flex-1 p-6 space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-display text-2xl font-semibold text-gray-900">Tasa de Cambio</h2>
        <p class="text-xs text-gray-500">Consulta y registra las tasas de cambio del día</p>
      </div>
    </div>

    <!-- TASA ACTUAL -->
    <?php if ($ultima): ?>
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
          <p class="text-xs text-gray-500">Moneda</p>
          <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($ultima['moneda']) ?></p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
          <p class="text-xs text-gray-500">Tasa USD</p>
          <p class="text-2xl font-bold text-olive">Bs. <?= number_format($ultima['tasa_usd'], 2) ?></p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
          <p class="text-xs text-gray-500">Tasa Euro</p>
          <p class="text-2xl font-bold text-blue-600"><?= $ultima['tasa_euro'] ? 'Bs. ' . number_format($ultima['tasa_euro'], 2) : 'No disponible' ?></p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
          <p class="text-xs text-gray-500">Tasa Paralelo</p>
          <p class="text-2xl font-bold text-amber-600"><?= $ultima['tasa_paralelo'] ? 'Bs. ' . number_format($ultima['tasa_paralelo'], 2) : 'No disponible' ?></p>
        </div>
      </div>
      
      <div class="text-right text-xs text-gray-400">
        Actualizado: <?= date('d/m/Y H:i:s', strtotime($ultima['updated_at'])) ?>
      </div>
    <?php else: ?>
      <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-amber-800 text-sm">
        <strong>Sin datos:</strong> No hay tasas de cambio registradas. Registra una nueva tasa.
      </div>
    <?php endif; ?>

    <!-- REGISTRO DE NUEVA TASA -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-4">
      <h3 class="font-bold text-gray-900 text-sm">Registrar Nueva Tasa de Cambio</h3>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="flex items-center gap-3 p-4 mb-6 text-sm text-rose-800 border border-rose-200/80 rounded-2xl bg-rose-50/80 shadow-sm" role="alert">
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

      <form class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3" action="index.php?controller=tasaMonedaController&action=crear" method="POST">
        <input type="text" name="moneda" value="Bs" placeholder="Moneda" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive" required>
        <input type="number" step="0.01" name="tasa_usd" placeholder="Tasa USD *" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive" required>
        <input type="number" step="0.01" name="tasa_euro" placeholder="Tasa Euro" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive">
        <input type="number" step="0.01" name="tasa_paralelo" placeholder="Tasa Paralelo" class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-olive">
        <button type="submit" class="bg-olive hover:bg-olive-hover text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors">
          Registrar Tasa
        </button>
      </form>
      <p class="text-xs text-gray-400">* Campos obligatorios. Euro y Paralelo son opcionales</p>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="flex items-center gap-3 p-4 mb-6 text-sm text-green-800 border border-green-200/80 rounded-2xl bg-green-50/80 shadow-sm" role="alert">
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

    <!-- HISTORIAL DE TASAS -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="px-4 py-3 border-b border-gray-200">
        <h4 class="font-semibold text-gray-900 text-sm">Historial de Tasas de Cambio</h4>
      </div>
      <table class="w-full text-left text-xs">
        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase">
          <tr>
            <th class="p-3">#</th>
            <th class="p-3">Moneda</th>
            <th class="p-3 text-right">Tasa USD</th>
            <th class="p-3 text-right">Tasa Euro</th>
            <th class="p-3 text-right">Tasa Paralelo</th>
            <th class="p-3 text-right">Fecha</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($tasas as $tasa): ?>
            <tr class="hover:bg-gray-50">
              <td class="p-3 font-mono text-gray-400">#<?= $tasa['tasa_id'] ?></td>
              <td class="p-3 font-medium text-gray-900"><?= htmlspecialchars($tasa['moneda']) ?></td>
              <td class="p-3 text-right font-mono font-semibold text-olive">Bs. <?= number_format($tasa['tasa_usd'], 2) ?></td>
              <td class="p-3 text-right font-mono <?= $tasa['tasa_euro'] ? 'text-blue-600' : 'text-gray-400' ?>">
                <?= $tasa['tasa_euro'] ? 'Bs. ' . number_format($tasa['tasa_euro'], 2) : 'N/A' ?>
              </td>
              <td class="p-3 text-right font-mono <?= $tasa['tasa_paralelo'] ? 'text-amber-600' : 'text-gray-400' ?>">
                <?= $tasa['tasa_paralelo'] ? 'Bs. ' . number_format($tasa['tasa_paralelo'], 2) : 'N/A' ?>
              </td>
              <td class="p-3 text-right text-gray-500"><?= date('d/m/Y H:i', strtotime($tasa['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <?php
  include ruta . '/includes/sidebar.js';
  ?>
</body>

</html>