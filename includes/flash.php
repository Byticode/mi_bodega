<?php
// Mensajes flash de sesión. Renderiza y limpia $_SESSION['error'] / $_SESSION['success'].
// Incluir dentro de <main>, antes del contenido de trabajo.
?>

<?php if (isset($_SESSION['error'])): ?>
  <div class="alert alert-error" role="alert">
    <i class="ti ti-alert-triangle shrink-0 text-lg" aria-hidden="true"></i>
    <div class="flex-1">
      <span class="font-semibold">Ha ocurrido un error.</span> <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <button type="button" onclick="this.closest('.alert').remove()" aria-label="Cerrar aviso">
      <i class="ti ti-x text-base" aria-hidden="true"></i>
    </button>
  </div>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
  <div class="alert alert-success" role="status">
    <i class="ti ti-circle-check shrink-0 text-lg" aria-hidden="true"></i>
    <div class="flex-1">
      <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <button type="button" onclick="this.closest('.alert').remove()" aria-label="Cerrar aviso">
      <i class="ti ti-x text-base" aria-hidden="true"></i>
    </button>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>
