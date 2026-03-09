<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=htmlspecialchars($title)?></title>
  <style>
    .doc-card {
      padding: 10px 14px;
      background: rgba(10,10,15,.4);
      border: 1px solid #1e1e2e;
      border-radius: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
    }
    .doc-card a {
      font-family: monospace; font-size: 12px;
      word-break: break-all; text-decoration: none;
    }
    .doc-empty {
      font-size: 13px; color: #7b7b9a;
      font-family: monospace;
      padding: 14px 0;
    }
    .doc-section {
      border: 1px solid #1e1e2e;
      border-radius: 16px;
      padding: 18px;
      background: rgba(18,18,28,.5);
    }
    .doc-section h3 {
      margin: 0 0 14px;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .upload-zone {
      display: flex; gap: 8px; align-items: center; flex-wrap: wrap;
      border: 1px dashed rgba(0,229,160,.35);
      border-radius: 10px; padding: 10px 14px;
      background: rgba(0,229,160,.04);
      margin-bottom: 14px;
    }
    .upload-label {
      flex: 1; min-width: 0;
      font-size: 12px; font-family: monospace;
      color: #7b7b9a; display: flex; align-items: center; gap: 8px;
    }
  </style>
</head>
<body>
  <?=$nav?>
  <main class="fq-container">

    <!-- HEADER -->
    <div style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <div>
        <h2 style="margin:0;">📁 Mis documentos</h2>
        <div class="muted" style="font-family:monospace; font-size:12px; margin-top:4px;">
          <?=htmlspecialchars(($empleado['apellido']??'').' '.($empleado['nombre']??''))?>
          · Legajo: <?=htmlspecialchars($empleado['legajo']??'—')?>
        </div>
      </div>
      <a class="fq-btn" href="<?=$ruta?>/fichada/mis">← Volver a mis fichadas</a>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type']??'')==='ok'?'fq-alert-ok':'fq-alert-danger'?>"
           style="margin-bottom:16px;">
        <?=htmlspecialchars($flash['msg']??'')?>
      </div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

      <!-- ── CERTIFICADOS ── -->
      <div class="doc-section">
        <h3 style="color:#00e5a0;">
          📋 Certificados médicos
          <span style="font-size:12px; font-weight:400; color:#7b7b9a;">(<?=count($certificados)?>)</span>
        </h3>

        <!-- Subir -->
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="tipo_doc" value="certificado">
          <div class="upload-zone">
            <span class="upload-label">
              <span>📎</span>
              <span id="cert-label">Elegir archivo (PDF, JPG, PNG)</span>
            </span>
            <button type="button" class="fq-btn" style="font-size:11px; padding:5px 10px;"
                    onclick="document.getElementById('cert-file').click()">
              Buscar
            </button>
            <button class="fq-btn fq-btn-primary" type="submit" style="white-space:nowrap;">
              ↑ Adjuntar
            </button>
          </div>
          <input type="file" name="documento" id="cert-file" accept=".pdf,.jpg,.jpeg,.png" required
                 style="display:none;"
                 onchange="document.getElementById('cert-label').textContent = this.files[0]?.name ?? 'Elegir archivo'">
        </form>

        <!-- Lista -->
        <?php if (empty($certificados)): ?>
          <p class="doc-empty">Sin certificados cargados aún.</p>
        <?php else: ?>
          <div style="display:grid; gap:8px;">
            <?php foreach ($certificados as $doc): ?>
              <div class="doc-card">
                <a href="<?=$ruta?>/docs/empleados/<?=$empleado['id']?>/certificados/<?=urlencode($doc['nombre_archivo'])?>"
                   target="_blank" style="color:#00e5a0;">
                  📄 <?=htmlspecialchars($doc['nombre_display']??$doc['nombre_archivo'])?>
                </a>
                <a href="<?=$ruta?>/empleado/misdocs?del=<?=urlencode($doc['nombre_archivo'])?>&tipo=certificado"
                   onclick="return confirm('¿Eliminar este certificado?')"
                   class="fq-btn fq-btn-danger" style="padding:4px 10px; font-size:11px; white-space:nowrap;">
                  Eliminar
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- ── RECIBOS DE SUELDO ── -->
      <div class="doc-section" style="border-color: rgba(167,139,250,.2);">
        <h3 style="color:#a78bfa;">
          💰 Recibos de sueldo
          <span style="font-size:12px; font-weight:400; color:#7b7b9a;">(<?=count($recibos)?>)</span>
        </h3>
        <p style="font-family:monospace; font-size:11px; color:#7b7b9a; margin:0 0 14px;">
          Los recibos son cargados por el empleador. Solo podés verlos y descargarlos.
        </p>

        <?php if (empty($recibos)): ?>
          <p class="doc-empty">Sin recibos cargados aún.</p>
        <?php else: ?>
          <div style="display:grid; gap:8px;">
            <?php foreach ($recibos as $doc): ?>
              <div class="doc-card">
                <a href="<?=$ruta?>/docs/empleados/<?=$empleado['id']?>/recibos/<?=urlencode($doc['nombre_archivo'])?>"
                   target="_blank" style="color:#a78bfa;">
                  📄 <?=htmlspecialchars($doc['nombre_display']??$doc['nombre_archivo'])?>
                </a>
                <span style="font-size:11px; color:#7b7b9a; font-family:monospace; white-space:nowrap;">
                  <?=isset($doc['subido_en']) ? date('d/m/Y', strtotime($doc['subido_en'])) : ''?>
                </span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
  <?=$footer?>
</body>
</html>