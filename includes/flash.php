<?php
// Mensajes flash de sesión. Se renderiza como notificación flotante fija.
$flashError   = $_SESSION['error']   ?? null;
$flashSuccess = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>

<?php if ($flashError || $flashSuccess): ?>
  <div id="flash-container" style="position:fixed; top:1rem; left:50%; transform:translateX(-50%); z-index:9999; display:flex; flex-direction:column; align-items:center; gap:0.625rem; padding:0 1rem; pointer-events:none;">
    <?php if ($flashError): ?>
      <div class="flash-msg alert alert-error" style="max-width:32rem; width:100%; opacity:0; transform:translateY(-0.5rem); transition: all 300ms ease-out; box-shadow: 0 4px 24px oklch(0% 0 0 / 0.12), 0 1px 3px oklch(0% 0 0 / 0.08); pointer-events:auto;">
        <i class="ti ti-alert-triangle" aria-hidden="true"></i>
        <div><?= htmlspecialchars($flashError) ?></div>
      </div>
    <?php endif; ?>

    <?php if ($flashSuccess): ?>
      <div class="flash-msg alert alert-success" style="max-width:32rem; width:100%; opacity:0; transform:translateY(-0.5rem); transition: all 300ms ease-out; box-shadow: 0 4px 24px oklch(0% 0 0 / 0.12), 0 1px 3px oklch(0% 0 0 / 0.08); pointer-events:auto;">
        <i class="ti ti-circle-check" aria-hidden="true"></i>
        <div><?= htmlspecialchars($flashSuccess) ?></div>
      </div>
    <?php endif; ?>
  </div>

  <script>
    (function () {
      var msgs = document.querySelectorAll('.flash-msg');

      msgs.forEach(function (el, i) {
        // Stagger entrance
        setTimeout(function () {
          el.style.opacity = '1';
          el.style.transform = 'translateY(0)';
        }, 50 * i);

        // Auto-dismiss after 5 seconds
        setTimeout(function () {
          el.style.opacity = '0';
          el.style.transform = 'translateY(-0.5rem)';
          setTimeout(function () {
            el.remove();
            var container = document.getElementById('flash-container');
            if (container && container.children.length === 0) {
              container.remove();
            }
          }, 300);
        }, 5000 + (500 * i));
      });
    })();
  </script>
<?php endif; ?>
