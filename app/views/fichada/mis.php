<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>
</head>

<body>
  <?=$nav?>

  <main class="fq-container">
    <div style="margin-bottom:14px;">
      <h2 style="margin:0;">Mis fichadas</h2>
      <?php if (!empty($empleado)): ?>
        <div class="muted" style="margin-top:4px; font-family:monospace; font-size:12px;">
          <?=htmlspecialchars(($empleado['apellido'] ?? '').' '.($empleado['nombre'] ?? '').' · '.($empleado['legajo'] ?? ''))?>
        </div>
      <?php endif; ?>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type'] ?? '') === 'ok' ? 'alert-ok' : 'alert-danger'?>">
        <?=htmlspecialchars($flash['msg'] ?? '')?>
      </div>
    <?php endif; ?>

    <section class="fq-card" style="margin-bottom:16px;">
      <form method="GET" class="form">
        <div style="display:grid; gap:10px; grid-template-columns: 1fr 1fr auto; align-items:end;">
          <div>
            <label class="label">Desde</label>
            <input class="input" type="date" name="desde" value="<?=htmlspecialchars($desde)?>">
          </div>
          <div>
            <label class="label">Hasta</label>
            <input class="input" type="date" name="hasta" value="<?=htmlspecialchars($hasta)?>">
          </div>
          <div>
            <button class="fq-btn fq-btn-primary" type="submit">Filtrar</button>
          </div>
        </div>
      </form>
    </section>

    <section class="fq-card">
      <div class="fq-table-wrap">
        <table class="fq-table" style="min-width:680px;">
          <thead>
            <tr>
              <th>Fecha/Hora</th>
              <th>Tipo</th>
              <th>Origen</th>
              <th>Comentario</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($lista)): ?>
            <tr><td colspan="4" class="muted">Sin fichadas</td></tr>
          <?php else: ?>
            <?php foreach ($lista as $f): ?>
              <tr>
                <td><?=htmlspecialchars($f['fecha_hora'])?></td>
                <td>
                  <?php if (($f['tipo'] ?? '') === 'IN'): ?>
                    <span class="fq-badge fq-badge-in">IN</span>
                  <?php else: ?>
                    <span class="fq-badge fq-badge-out">OUT</span>
                  <?php endif; ?>
                </td>
                <td><?=htmlspecialchars($f['origen'] ?? '')?></td>
                <td><?=htmlspecialchars($f['comentario'] ?? '')?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <?=$footer?>
</body>
</html>
