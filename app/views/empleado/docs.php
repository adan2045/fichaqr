<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=htmlspecialchars($title)?></title>
</head>
<body>
  <?=$nav?>

  <main class="fq-container">

    <div class="fq-actions" style="justify-content:space-between; align-items:center; margin-bottom:16px;">
      <div>
        <h2 style="margin:0;">📁 Documentos</h2>
        <div class="muted" style="font-family:monospace; font-size:12px; margin-top:4px;">
          <?=htmlspecialchars($empleado['apellido'].' '.$empleado['nombre'])?> · Legajo: <?=htmlspecialchars($empleado['legajo'] ?? '-')?>
        </div>
      </div>
      <div class="fq-actions">
        <a class="fq-btn" href="<?=$ruta?>/empleado/listado">← Volver</a>
        <a class="fq-btn fq-btn-primary" href="<?=$ruta?>/admin/gestion">Panel</a>
      </div>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type'] ?? '') === 'ok' ? 'fq-alert-ok' : 'fq-alert-danger'?>" style="margin-bottom:14px;">
        <?=htmlspecialchars($flash['msg'] ?? '')?>
      </div>
    <?php endif; ?>

    <!-- SUBIR DOCUMENTO -->
    <section class="fq-card" style="margin-bottom:16px;">
      <h3 style="margin:0 0 14px; font-size:14px;">📤 Subir nuevo documento</h3>
      <form method="POST" enctype="multipart/form-data" style="display:grid; gap:12px;">
        <div style="display:grid; grid-template-columns: 1fr 1fr auto; gap:10px; align-items:end;">
          <div>
            <label class="label">Tipo de documento</label>
            <select class="input" name="tipo_doc">
              <option value="certificado">📋 Certificado médico</option>
              <option value="recibo">💰 Recibo de sueldo</option>
            </select>
          </div>
          <div>
            <label class="label">Archivo (PDF, JPG, PNG — máx. 10MB)</label>
            <input class="input" type="file" name="documento" accept=".pdf,.jpg,.jpeg,.png" required style="padding:8px;">
          </div>
          <div>
            <button class="fq-btn fq-btn-primary" type="submit">Subir</button>
          </div>
        </div>
      </form>
    </section>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

      <!-- CERTIFICADOS -->
      <section class="fq-card">
        <h3 style="margin:0 0 12px; font-size:14px; color:#00e5a0;">📋 Certificados médicos (<?=count($certificados)?>)</h3>
        <?php if (empty($certificados)): ?>
          <p class="muted" style="font-size:13px;">Sin certificados cargados aún.</p>
        <?php else: ?>
          <ul style="list-style:none; padding:0; margin:0; display:grid; gap:8px;">
            <?php foreach ($certificados as $f): ?>
              <li style="display:flex; justify-content:space-between; align-items:center; padding:8px 10px; background:rgba(10,10,15,.4); border:1px solid #1e1e2e; border-radius:10px;">
                <a href="<?=$ruta?>/docs/empleados/<?=$empleado['id']?>/certificados/<?=urlencode($f)?>"
                   target="_blank"
                   style="font-family:monospace; font-size:12px; color:#00e5a0; word-break:break-all;">
                  📄 <?=htmlspecialchars($f)?>
                </a>
                <a href="<?=$ruta?>/empleado/docs?id=<?=$empleado['id']?>&del=<?=urlencode($f)?>&tipo=certificado"
                   onclick="return confirm('¿Eliminar <?=htmlspecialchars($f)?>?')"
                   class="fq-btn fq-btn-danger" style="padding:4px 10px; font-size:11px; white-space:nowrap; margin-left:8px;">
                  Eliminar
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>

      <!-- RECIBOS -->
      <section class="fq-card">
        <h3 style="margin:0 0 12px; font-size:14px; color:#a78bfa;">💰 Recibos de sueldo (<?=count($recibos)?>)</h3>
        <?php if (empty($recibos)): ?>
          <p class="muted" style="font-size:13px;">Sin recibos cargados aún.</p>
        <?php else: ?>
          <ul style="list-style:none; padding:0; margin:0; display:grid; gap:8px;">
            <?php foreach ($recibos as $f): ?>
              <li style="display:flex; justify-content:space-between; align-items:center; padding:8px 10px; background:rgba(10,10,15,.4); border:1px solid #1e1e2e; border-radius:10px;">
                <a href="<?=$ruta?>/docs/empleados/<?=$empleado['id']?>/recibos/<?=urlencode($f)?>"
                   target="_blank"
                   style="font-family:monospace; font-size:12px; color:#a78bfa; word-break:break-all;">
                  📄 <?=htmlspecialchars($f)?>
                </a>
                <a href="<?=$ruta?>/empleado/docs?id=<?=$empleado['id']?>&del=<?=urlencode($f)?>&tipo=recibo"
                   onclick="return confirm('¿Eliminar <?=htmlspecialchars($f)?>?')"
                   class="fq-btn fq-btn-danger" style="padding:4px 10px; font-size:11px; white-space:nowrap; margin-left:8px;">
                  Eliminar
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
