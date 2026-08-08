<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Editar Venta</title>
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
  <main class="flex-1 p-6 space-y-6 max-w-2xl mx-auto">
    <div class="flex items-center space-x-3">
      <a href="index.php?controller=ventasController&action=listar" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-100">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </a>
      <div>
        <h2 class="font-display text-3xl font-semibold tracking-[-0.015em] text-gray-900">Completar Venta #<?= $venta['venta_id'] ?></h2>
        <p class="text-sm text-gray-500">Registra el pago para completar la venta pendiente</p>
      </div>
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

    <form action="index.php?controller=ventasController&action=editar&id=<?= $venta['venta_id'] ?>" method="POST" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
      
      <!-- Mostrar resumen de la venta -->
      <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h4 class="font-semibold text-gray-900 text-sm mb-2">Resumen de la Venta</h4>
        <div class="space-y-1 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-500">Cliente:</span>
            <span class="font-medium"><?= $venta['cliente_nombre'] ? htmlspecialchars($venta['cliente_nombre'] . ' ' . ($venta['cliente_apellido'] ?? '')) : 'Consumidor final' ?></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">Fecha:</span>
            <span class="font-medium"><?= date('d/m/Y H:i', strtotime($venta['venta_fecha'])) ?></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">Total:</span>
            <span class="font-bold text-olive">Bs <?= number_format($venta['venta_total'], 2) ?></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">Productos:</span>
            <span class="font-medium"><?= count($detalles) ?> productos</span>
          </div>
        </div>
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Método de Pago *</label>
        <select name="metodo_pago" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive bg-white" required>
          <option value="efectivo">Efectivo</option>
          <option value="transferencia">Transferencia</option>
          <option value="pago_movil">Pago Móvil</option>
          <option value="biopago">Biopago</option>
          <option value="cashea">Cashea</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Número de Referencia (opcional)</label>
        <input type="text" name="numero_pago" placeholder="Ingrese número de referencia..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
        <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive bg-white">
          <option value="completada">Completada</option>
          <option value="pendiente" selected>Pendiente</option>
          <option value="cancelada">Cancelada</option>
        </select>
      </div>

      <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
        <a href="index.php?controller=ventasController&action=listar" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancelar</a>
        <button type="submit" class="px-5 py-2 bg-olive hover:bg-olive-hover text-white text-sm font-bold rounded-lg transition-colors">
          Actualizar Venta
        </button>
      </div>
    </form>
  </main>

  <?php
  include ruta . '/includes/sidebar.js';
  ?>
</body>
</html>