<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=htmlspecialchars($title)?></title>
</head>
<body>
  <?=$nav?>
  <main class="fq-container">

    <div style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <div>
        <h2 style="margin:0;">📁 Mis documentos</h2>
        <div class="muted" style="font-family:monospace; font-size:12px; margin-top:4px;">
          <?=htmlspecialchars(($empleado['apellido'] ?? '').' '.($empleado['nombre'] ?? ''))?> · Legajo: <?=htmlspecialchars($empleado['legajo'] ?? '—')?>
        </div>
      </div>
      <a class="fq-btn" href="<?=$ruta?>/fichada/mis">← Volver a mis fichadas</a>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

      <section class="fq-card">
        <h3 style="margin:0 0 14px; font-size:14px; color:#00e5a0;">📋 Certificados médicos (<?=count($certificados)?>)</h3>
        <?php if (empty($certificados)): ?>
          <p class="muted" style="font-size:13px;">Sin certificados cargados aún.</p>
        <?php else: ?>
          <ul style="list-style:none; padding:0; margin:0; display:grid; gap:8px;">
            <?php foreach ($certificados as $f): ?>
              <li style="padding:10px 12px; background:rgba(10,10,15,.4); border:1px solid #1e1e2e; border-radius:10px;">
                <a href="<?=$ruta?>/docs/empleados/<?=$empleado['id']?>/certificados/<?=urlencode($f)?>"
                   target="_blank"
                   style="font-family:monospace; font-size:12px; color:#00e5a0;">
                  📄 <?=htmlspecialchars($f)?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>

      <section class="fq-card">
        <h3 style="margin:0 0 14px; font-size:14px; color:#a78bfa;">💰 Recibos de sueldo (<?=count($recibos)?>)</h3>
        <?php if (empty($recibos)): ?>
          <p class="muted" style="font-size:13px;">Sin recibos cargados aún.</p>
        <?php else: ?>
          <ul style="list-style:none; padding:0; margin:0; display:grid; gap:8px;">
            <?php foreach ($recibos as $f): ?>
              <li style="padding:10px 12px; background:rgba(10,10,15,.4); border:1px solid #1e1e2e; border-radius:10px;">
                <a href="<?=$ruta?>/docs/empleados/<?=$empleado['id']?>/recibos/<?=urlencode($f)?>"
                   target="_blank"
                   style="font-family:monospace; font-size:12px; color:#a78bfa;">
                  📄 <?=htmlspecialchars($f)?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>

    </div>
  </main>
  <?=$footer?>
</body>
</html>
