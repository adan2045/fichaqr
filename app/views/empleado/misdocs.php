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

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type'] ?? '') === 'ok' ? 'fq-alert-ok' : 'fq-alert-danger'?>" style="margin-bottom:14px;">
        <?=htmlspecialchars($flash['msg'] ?? '')?>
      </div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

      <!-- CERTIFICADOS — empleado puede subir los suyos -->
      <section class="fq-card">
        <h3 style="margin:0 0 14px; font-size:14px; color:#00e5a0;">📋 Certificados médicos (<?=count($certificados)?>)</h3>

        <!-- Formulario subir certificado -->
        <form method="POST" enctype="multipart/form-data" id="form-cert"
              style="display:grid; gap:8px; margin-bottom:14px;">
          <input type="hidden" name="tipo_doc" value="certificado">
          <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <div style="flex:1; min-width:0; border:1px dashed rgba(0,229,160,.35); border-radius:10px;
                        padding:8px 12px; background:rgba(0,229,160,.04); display:flex; align-items:center; gap:8px;">
              <span style="font-size:18px;">📎</span>
              <span class="muted" style="font-size:12px; font-family:monospace; flex:1;" id="cert-label">
                Elegir archivo (PDF, JPG, PNG)
              </span>
              <button type="button" class="fq-btn" style="font-size:11px; padding:5px 10px;"
                      onclick="document.getElementById('cert-file').click()">
                Buscar
              </button>
            </div>
            <button class="fq-btn fq-btn-primary" type="submit" style="white-space:nowrap;">
              ↑ Adjuntar
            </button>
          </div>
          <input type="file" name="documento" id="cert-file" accept=".pdf,.jpg,.jpeg,.png" required
                 style="display:none;"
                 onchange="document.getElementById('cert-label').textContent = this.files[0]?.name ?? 'Elegir archivo'">
        </form>

        <?php if (empty($certificados)): ?>
          <p class="muted" style="font-size:13px;">Sin certificados cargados aún.</p>
        <?php else: ?>
          <ul style="list-style:none; padding:0; margin:0; display:grid; gap:8px;">
            <?php foreach ($certificados as $f): ?>
              <li style="padding:10px 12px; background:rgba(10,10,15,.4); border:1px solid #1e1e2e; border-radius:10px;
                         display:flex; justify-content:space-between; align-items:center; gap:8px;">
                <a href="<?=$ruta?>/docs/empleados/<?=$empleado['id']?>/certificados/<?=urlencode($f)?>"
                   target="_blank"
                   style="font-family:monospace; font-size:12px; color:#00e5a0; word-break:break-all;">
                  📄 <?=htmlspecialchars($f)?>
                </a>
                <a href="<?=$ruta?>/empleado/misdocs?del=<?=urlencode($f)?>&tipo=certificado"
                   onclick="return confirm('¿Eliminar este certificado?')"
                   class="fq-btn fq-btn-danger" style="padding:4px 10px; font-size:11px; white-space:nowrap;">
                  Eliminar
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>

      <!-- RECIBOS — solo lectura para el empleado -->
      <section class="fq-card">
        <h3 style="margin:0 0 14px; font-size:14px; color:#a78bfa;">💰 Recibos de sueldo (<?=count($recibos)?>)</h3>
        <p class="muted" style="font-family:monospace; font-size:11px; margin:0 0 12px;">
          Los recibos son cargados por el empleador.
        </p>
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