<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Crear Producto</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
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
  include RUTA_APP . '/includes/sidebar.php';
  ?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="flex-1 p-6 space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center space-x-3">
      <a href="<?= url('productos') ?>" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </a>
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Crear Producto</h2>
        <p class="text-xs text-gray-500">Registra un nuevo producto en el inventario</p>
      </div>
    </div>

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

    <form action="<?= url('productos/crear') ?>" method="POST" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Código de Barras</label>
          <input type="text" name="codigo" placeholder="Opcional" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
          <p class="text-xs text-gray-400 mt-1">Si no ingresa código, se generará automáticamente</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Nombre del Producto *</label>
          <input type="text" name="nombre" placeholder="Ej: Refresco Fanta Toronja" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive" required>
          <p class="text-xs text-blue-600 mt-1">⚠️ El nombre se combinará automáticamente con el peso y la unidad (ej: Refresco Fanta Toronja 2L)</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Peso</label>
          <input type="number" step="0.01" name="peso" placeholder="Ej: 2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
          <p class="text-xs text-gray-400 mt-1">Opcional - El peso se agregará al nombre</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Unidad de Medida *</label>
          <select name="unidad" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive bg-white" required>
            <option value="">Seleccionar unidad...</option>
            <?php foreach ($unidades as $unidad): ?>
              <option value="<?= $unidad['unidad_id'] ?>"><?= htmlspecialchars($unidad['unidad_nombre']) ?> (<?= $unidad['unidad_abreviatura'] ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Categoría *</label>
          <select name="categoria" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive bg-white" required>
            <option value="">Seleccionar categoría...</option>
            <?php foreach ($categorias as $categoria): ?>
              <option value="<?= $categoria['categorias_id'] ?>"><?= htmlspecialchars($categoria['categorias_nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Precio de Venta *</label>
          <input type="number" step="0.01" name="precio" placeholder="0.00" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive" required>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Stock Inicial</label>
          <input type="number" step="0.01" name="stock" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
        </div>
      </div>

      <!-- Preview del nombre -->
      <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <p class="text-xs text-gray-500 mb-1">Vista previa del nombre completo:</p>
        <p id="previewNombre" class="text-sm font-semibold text-gray-900">Refresco Fanta Toronja 2L</p>
      </div>

      <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
        <a href="<?= url('productos') ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancelar</a>
        <button type="submit" class="px-5 py-2 bg-olive hover:bg-olive-hover text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Guardar Producto
        </button>
      </div>
    </form>
  </main>

  <script src="<?= assets('scripts/producto-preview.js') ?>"></script>

  <?php include RUTA_APP . '/includes/sidebar.js'; ?>
</body>
</html>