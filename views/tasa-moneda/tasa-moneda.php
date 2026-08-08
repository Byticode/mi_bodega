<?php
$page_title = 'Tasa de cambio';
$page_desc  = 'Consulta y registra las tasas de cambio del día.';
include ruta . '/includes/head.php';
include ruta . '/includes/sidebar.php';
?>

<main id="contenido" class="app-main">
  <div class="app-wrap app-wrap--mid">

    <!-- Page header -->
    <div class="page-head">
      <div>
        <h1 class="page-title">Tasa de cambio</h1>
        <p class="page-sub">La tasa más reciente es la que usa el punto de venta para el equivalente en divisas.</p>
      </div>
    </div>

    <?php include ruta . '/includes/flash.php'; ?>

    <!-- Tasa vigente -->
    <?php if ($ultima): ?>
      <section aria-labelledby="tasaVigente" class="flex flex-col gap-2">
        <h2 class="section-title" id="tasaVigente">Tasa vigente</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
          <div class="stat">
            <span class="stat-label">Moneda base</span>
            <span class="stat-value"><?= htmlspecialchars($ultima['moneda']) ?></span>
          </div>
          <div class="stat">
            <span class="stat-label">Dólar</span>
            <span class="stat-value stat-value--money stat-value--accent"><?= money($ultima['tasa_usd']) ?></span>
          </div>
          <div class="stat">
            <span class="stat-label">Euro</span>
            <span class="stat-value stat-value--money"><?= $ultima['tasa_euro'] ? money($ultima['tasa_euro']) : '—' ?></span>
          </div>
          <div class="stat">
            <span class="stat-label">Paralelo</span>
            <span class="stat-value stat-value--money"><?= $ultima['tasa_paralelo'] ? money($ultima['tasa_paralelo']) : '—' ?></span>
          </div>
        </div>
        <p class="text-xs text-ink-3">
          Actualizada el <?= date('d/m/Y \a \l\a\s H:i', strtotime($ultima['updated_at'])) ?>
        </p>
      </section>
    <?php else: ?>
      <div class="alert alert-warn" role="status">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303-3.376c-.866 1.5.217 3.374 1.948 3.374M12 15.75h.007v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="flex-1">
          <span class="font-semibold">Sin tasas registradas.</span>
          Registra la primera para que el punto de venta pueda mostrar el equivalente en dólares.
        </div>
      </div>
    <?php endif; ?>

    <!-- Registro -->
    <div class="card p-5 flex flex-col gap-4">
      <div>
        <h2 class="section-title">Registrar nueva tasa</h2>
        <p class="section-sub">Cada registro queda en el historial; no se sobrescribe el anterior.</p>
      </div>

      <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3" action="index.php?controller=tasaMonedaController&action=crear" method="POST">
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

        <div class="sm:col-span-2 lg:col-span-4 flex flex-wrap items-center justify-between gap-3">
          <p class="hint m-0">Solo el dólar es obligatorio.</p>
          <button type="submit" class="btn btn-primary">Registrar tasa</button>
        </div>
      </form>
    </div>

    <!-- Historial -->
    <div class="card overflow-hidden">
      <div class="card-head">
        <h2 class="section-title">Historial de tasas</h2>
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
                    <p class="empty-sub">Las tasas que registres aparecerán aquí en orden cronológico.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($tasas as $tasa): ?>
                <tr>
                  <td class="font-mono text-xs text-ink-3">#<?= (int) $tasa['tasa_id'] ?></td>
                  <td class="font-medium"><?= htmlspecialchars($tasa['moneda']) ?></td>
                  <td class="num money text-olive"><?= money($tasa['tasa_usd']) ?></td>
                  <td class="num money <?= $tasa['tasa_euro'] ? 'text-ink-2' : 'text-ink-3' ?>">
                    <?= $tasa['tasa_euro'] ? money($tasa['tasa_euro']) : '—' ?>
                  </td>
                  <td class="num money <?= $tasa['tasa_paralelo'] ? 'text-ink-2' : 'text-ink-3' ?>">
                    <?= $tasa['tasa_paralelo'] ? money($tasa['tasa_paralelo']) : '—' ?>
                  </td>
                  <td class="text-ink-2 whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($tasa['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<?php include ruta . '/includes/footer.php'; ?>
