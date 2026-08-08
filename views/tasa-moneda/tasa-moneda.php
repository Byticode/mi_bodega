<?php
$page_title = 'Tasa de cambio';
$page_desc  = 'Tasas de cambio actualizadas automáticamente desde la API del BCV.';
include RUTA_APP . '/includes/head.php';
include RUTA_APP . '/includes/sidebar.php';

$vigente_ts = !empty($tasa['vigente_desde']) ? strtotime($tasa['vigente_desde']) : null;
$vieja      = $vigente_ts && (time() - $vigente_ts) > TASA_ANTIGUA;

$origenes = [
    'api'     => ['badge-success', 'Recién consultada'],
    'cache'   => ['badge-success', 'Al día'],
    'bd'      => ['badge-warn',    'Guardada localmente'],
    'ninguna' => ['badge-danger',  'Sin datos'],
];
[$origen_clase, $origen_texto] = $origenes[$tasa['origen']] ?? $origenes['ninguna'];
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--mid">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Tasa de cambio</h1>
        <p class="page-sub">Se consulta sola desde la API del BCV. La app convierte a dólares con la tasa oficial.</p>
      </div>
      <a href="<?= url('tasa-moneda/actualizar') ?>" class="btn btn-primary">
        <i class="ti ti-refresh text-base" aria-hidden="true"></i>
        Actualizar ahora
      </a>
    </div>

    <?php include RUTA_APP . '/includes/flash.php'; ?>

    <?php if ($tasa['origen'] === 'bd'): ?>
      <div class="alert alert-warn" role="status">
        <i class="ti ti-alert-circle shrink-0 text-lg" aria-hidden="true"></i>
        <div class="flex-1">
          <span class="font-semibold">Sin conexión con la API.</span>
          Se muestra la última tasa guardada. Vuelve a intentar o registra una manualmente.
        </div>
      </div>
    <?php elseif ($tasa['origen'] === 'ninguna'): ?>
      <div class="alert alert-error" role="alert">
        <i class="ti ti-alert-triangle shrink-0 text-lg" aria-hidden="true"></i>
        <div class="flex-1">
          <span class="font-semibold">No hay ninguna tasa disponible.</span>
          Falló la API y no hay valores guardados: el punto de venta no podrá mostrar el equivalente en dólares. Registra una tasa manualmente abajo.
        </div>
      </div>
    <?php elseif ($vieja): ?>
      <div class="alert alert-warn" role="status">
        <i class="ti ti-clock shrink-0 text-lg" aria-hidden="true"></i>
        <div class="flex-1">
          <span class="font-semibold">La tasa tiene más de un día.</span>
          El BCV pudo no haber publicado todavía, o la fuente está desactualizada.
        </div>
      </div>
    <?php endif; ?>

    <!-- Tasa vigente -->
    <section aria-labelledby="tasaVigente" class="flex flex-col gap-3">
      <div class="flex flex-wrap items-center justify-between gap-2">
        <h2 class="section-title" id="tasaVigente">Tasa vigente</h2>
        <span class="badge <?= $origen_clase ?>"><span class="badge-dot"></span><?= $origen_texto ?></span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="stat border-olive-rule">
          <span class="stat-label">Dólar BCV</span>
          <span class="stat-value stat-value--money stat-value--accent">
            <?= $tasa['tasa_usd'] ? money($tasa['tasa_usd']) : '—' ?>
          </span>
          <span class="stat-note">La app convierte con esta</span>
        </div>
        <div class="stat">
          <span class="stat-label">Paralelo</span>
          <span class="stat-value stat-value--money"><?= $tasa['tasa_paralelo'] ? money($tasa['tasa_paralelo']) : '—' ?></span>
          <span class="stat-note">Solo referencia</span>
        </div>
        <div class="stat">
          <span class="stat-label">Euro BCV</span>
          <span class="stat-value stat-value--money"><?= $tasa['tasa_euro'] ? money($tasa['tasa_euro']) : '—' ?></span>
          <span class="stat-note">Solo referencia</span>
        </div>
      </div>

      <p class="text-xs text-ink-3">
        <?php if ($vigente_ts): ?>
          Publicada el <?= date('d/m/Y \a \l\a\s H:i', $vigente_ts) ?>.
        <?php endif; ?>
        Se vuelve a consultar cada <?= (int) round(TASA_TTL / 60) ?> minutos.
        Fuente: <span class="font-mono">ve.dolarapi.com</span>.
      </p>
    </section>

    <!-- Conversor rápido -->
    <?php if (!empty($tasa['tasa_usd'])): ?>
      <div class="card p-5 flex flex-col gap-4">
        <div>
          <h2 class="section-title">Conversor</h2>
          <p class="section-sub">Con la tasa BCV vigente.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
          <div class="field">
            <label for="conv_bs" class="label">Bolívares</label>
            <input type="number" step="0.01" min="0" id="conv_bs" class="input input--num" value="<?= htmlspecialchars((string) round($tasa['tasa_usd'], 2)) ?>" autocomplete="off">
          </div>
          <div class="field">
            <label for="conv_usd" class="label">Dólares</label>
            <input type="number" step="0.01" min="0" id="conv_usd" class="input input--num" value="1.00" autocomplete="off">
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Registro manual -->
    <details class="card p-5" <?= $tasa['origen'] === 'ninguna' ? 'open' : '' ?>>
      <summary class="section-title cursor-pointer">Registrar una tasa manualmente</summary>
      <p class="section-sub mb-4 mt-1">
        Úsalo solo si la API está caída. La próxima consulta automática correcta la reemplazará.
      </p>

      <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3" action="<?= url('tasa-moneda/crear') ?>" method="POST">
        <div class="field">
          <label for="moneda" class="label">Moneda base</label>
          <input type="text" id="moneda" name="moneda" class="input" value="Bs" required autocomplete="off">
        </div>
        <div class="field">
          <label for="tasa_usd" class="label">Dólar <span class="req" aria-hidden="true">*</span></label>
          <input type="number" step="0.01" min="0.01" id="tasa_usd" name="tasa_usd" class="input input--num" placeholder="0,00" required autocomplete="off">
        </div>
        <div class="field">
          <label for="tasa_euro" class="label">Euro</label>
          <input type="number" step="0.01" min="0" id="tasa_euro" name="tasa_euro" class="input input--num" placeholder="Opcional" autocomplete="off">
        </div>
        <div class="field">
          <label for="tasa_paralelo" class="label">Paralelo</label>
          <input type="number" step="0.01" min="0" id="tasa_paralelo" name="tasa_paralelo" class="input input--num" placeholder="Opcional" autocomplete="off">
        </div>

        <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
          <button type="submit" class="btn btn-secondary">Registrar tasa manual</button>
        </div>
      </form>
    </details>

    <!-- Historial -->
    <div class="card overflow-hidden">
      <div class="card-head">
        <h2 class="section-title">Historial</h2>
        <span class="text-xs text-ink-3">Se guarda una fila solo cuando la tasa cambia</span>
      </div>

      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">Historial de tasas de cambio registradas</caption>
          <thead>
            <tr>
              <th scope="col" class="w-16">#</th>
              <th scope="col">Moneda</th>
              <th scope="col" class="num">Dólar</th>
              <th scope="col" class="num">Euro</th>
              <th scope="col" class="num">Paralelo</th>
              <th scope="col">Registrada</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($tasas)): ?>
              <tr>
                <td colspan="6">
                  <div class="empty">
                    <p class="empty-title">Sin registros</p>
                    <p class="empty-sub">Cada cambio de tasa quedará aquí en orden cronológico.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($tasas as $fila): ?>
                <tr>
                  <td class="font-mono text-xs text-ink-3">#<?= (int) $fila['tasa_id'] ?></td>
                  <td class="font-medium"><?= htmlspecialchars($fila['moneda']) ?></td>
                  <td class="num money text-olive"><?= money($fila['tasa_usd']) ?></td>
                  <td class="num money <?= $fila['tasa_euro'] ? 'text-ink-2' : 'text-ink-3' ?>">
                    <?= $fila['tasa_euro'] ? money($fila['tasa_euro']) : '—' ?>
                  </td>
                  <td class="num money <?= $fila['tasa_paralelo'] ? 'text-ink-2' : 'text-ink-3' ?>">
                    <?= $fila['tasa_paralelo'] ? money($fila['tasa_paralelo']) : '—' ?>
                  </td>
                  <td class="text-ink-2 whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($fila['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<?php if (!empty($tasa['tasa_usd'])): ?>
<script>
  window.TASA_USD = <?= json_encode((float) $tasa['tasa_usd']) ?>;
</script>
<script src="<?= assets('scripts/tasa-conversor.js') ?>"></script>
<?php endif; ?>

<?php include RUTA_APP . '/includes/footer.php'; ?>
