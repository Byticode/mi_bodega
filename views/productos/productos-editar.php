<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mi_bodega - Editar Producto</title>
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

  <main class="flex-1 p-6 space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center space-x-3">
      <a href="index.php?controller=productosController&action=listar" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </a>
      <div>
        <h2 class="font-display text-2xl font-semibold text-gray-900">Editar Producto</h2>
        <p class="text-xs text-gray-500">Actualiza la información del producto</p>
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

    <form action="index.php?controller=productosController&action=editar&id=<?= $dato[0]['producto_id'] ?>" method="POST" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Código de Barras (opcional)</label>
          <input type="text" name="codigo" value="<?= htmlspecialchars($dato[0]['producto_codigo'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Nombre del Producto *</label>
          <input type="text" name="nombre" value="<?= htmlspecialchars(preg_replace('/\s+\d+\.?\d*[A-Za-z]+$/', '', $dato[0]['producto_nombre'])) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive" required>
          <p class="text-xs text-blue-600 mt-1">⚠️ El nombre se combinará automáticamente con el peso y la unidad</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Peso</label>
          <input type="number" step="0.01" name="peso" value="<?= $dato[0]['producto_peso'] ?? '' ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Categoría *</label>
          <select name="categoria" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive bg-white" required>
            <option value="">Seleccionar categoría...</option>
            <?php foreach ($categorias as $categoria): ?>
              <option value="<?= $categoria['categorias_id'] ?>" <?= ($categoria['categorias_id'] == $dato[0]['categoria_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($categoria['categorias_nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Unidad *</label>
          <select name="unidad" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive bg-white" required>
            <option value="">Seleccionar unidad...</option>
            <?php foreach ($unidades as $unidad): ?>
              <option value="<?= $unidad['unidad_id'] ?>" <?= ($unidad['unidad_id'] == $dato[0]['unidad_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($unidad['unidad_nombre']) ?> (<?= $unidad['unidad_abreviatura'] ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Precio Venta *</label>
          <input type="number" step="0.01" name="precio" value="<?= $dato[0]['producto_precio_venta'] ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive" required>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Stock</label>
          <input type="number" step="0.01" name="stock" value="<?= $dato[0]['producto_stock'] ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-olive">
        </div>
      </div>

      <!-- Preview del nombre -->
      <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <p class="text-xs text-gray-500 mb-1">Vista previa del nombre completo:</p>
        <p id="previewNombre" class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($dato[0]['producto_nombre']) ?></p>
      </div>

      <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
        <a href="index.php?controller=productosController&action=listar" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancelar</a>
        <button type="submit" class="px-5 py-2 bg-olive hover:bg-olive-hover text-white text-xs font-bold rounded-lg">Guardar Cambios</button>
      </div>
    </form>
  </main>

  <script>
    // Vista previa del nombre en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
      const nombreInput = document.querySelector('input[name="nombre"]');
      const pesoInput = document.querySelector('input[name="peso"]');
      const unidadSelect = document.querySelector('select[name="unidad"]');
      const previewElement = document.getElementById('previewNombre');

      function updatePreview() {
        let nombre = nombreInput.value.trim() || 'Nombre';
        const peso = pesoInput.value.trim();
        const unidadText = unidadSelect.options[unidadSelect.selectedIndex]?.text || '';
        const abreviatura = unidadText.match(/\(([^)]+)\)/);
        const unidadAbrev = abreviatura ? abreviatura[1] : '';

        if (nombre) {
          nombre = nombre.charAt(0).toUpperCase() + nombre.slice(1);
        }

        let preview = nombre;
        if (peso && unidadAbrev) {
          const pesoNum = parseFloat(peso);
          const pesoFormateado = Number.isInteger(pesoNum) ? pesoNum : pesoNum.toFixed(2);
          preview = nombre + ' ' + pesoFormateado + unidadAbrev;
        } else if (peso) {
          preview = nombre + ' ' + peso;
        }

        previewElement.textContent = preview || 'Nombre del producto';
      }

      nombreInput.addEventListener('input', updatePreview);
      pesoInput.addEventListener('input', updatePreview);
      unidadSelect.addEventListener('change', updatePreview);
    });
  </script>

  <?php 
  include ruta . '/includes/sidebar.js';
  ?>
</body>
</html>