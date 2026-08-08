<?php
// Componente de Spinner de Carga Global para Mi Bodega
?>
<div id="globalSpinner" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-gray-900/40 backdrop-blur-xs transition-opacity duration-300 opacity-0 pointer-events-none" role="status" aria-label="Cargando">
  <div class="bg-white/95 p-5 rounded-2xl shadow-xl border border-gray-100 flex flex-col items-center gap-3 max-w-[210px] w-full text-center transform transition-transform duration-300 scale-95">
    <div class="relative w-12 h-12 flex items-center justify-center">
      <!-- Spinning outer ring -->
      <div class="absolute inset-0 rounded-full border-3 border-olive/20 border-t-olive animate-spin"></div>
      <!-- Center icon -->
      <div class="w-6 h-6 rounded-lg bg-olive/10 text-olive flex items-center justify-center">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-12v.75m0 3v.75m0 3v.75m0 3V18m3-12v.75m0 3v.75m0 3v.75m0 3V18"/>
        </svg>
      </div>
    </div>
    <span class="text-xs font-bold text-gray-700 tracking-wide">Procesando...</span>
  </div>
</div>

<script src="<?= assets('scripts/spinner.js') ?>"></script>
