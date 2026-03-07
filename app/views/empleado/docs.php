<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=htmlspecialchars($title)?></title>
  <style>
    .emp-acordeon { border:1px solid #1e1e2e; border-radius:12px; overflow:hidden; margin-bottom:8px; }
    .emp-acordeon__header { display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:rgba(10,10,15,.5); cursor:pointer; user-select:none; gap:10px; }
    .emp-acordeon__header:hover { background:rgba(20,20,32,.8); }
    .emp-acordeon__nombre { font-weight:700; font-size:13px; }
    .emp-acordeon__meta   { font-family:monospace; font-size:11px; color:#7b7b9a; }
    .emp-acordeon__arrow  { font-size:11px; color:#7b7b9a; transition:transform .2s; }
    .emp-acordeon__body   { display:none; padding:10px 14px; border-top:1px solid #1e1e2e; }
    .emp-acordeon.open .emp-acordeon__arrow { transform:rotate(180deg); }
    .emp-acordeon.open .emp-acordeon__body  { display:block; }
    .doc-item { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; padding:8px 10px; background:rgba(10,10,15,.4); border:1px solid #1e1e2e; border-radius:9px; margin-bottom:6px; }
    .doc-item__meta { font-family:monospace; font-size:10px; color:#7b7b9a; margin-top:3px; }
    .recibo-row { border:1px solid #1e1e2e; border-radius:12px; overflow:hidden; margin-bottom:8px; }
    .recibo-row__header { display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:rgba(10,10,15,.5); flex-wrap:wrap; gap:10px; }
    .recibo-row__body { padding:10px 14px; border-top:1px solid #1e1e2e; }
  </style>
</head>
<body>
  <?=$nav?>
  <main class="fq-container">

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:20px;">
      <div>
        <h2 style="margin:0;">📁 Gestión de documentos</h2>
        <div class="muted" style="font-family:monospace; font-size:12px; margin-top:4px;">
          Panel completo · <?=count($empleados)?> empleados
        </div>
      </div>
      <div class="fq-actions">
        <a class="fq-btn" href="<?=$ruta?>/empleado/listado">← Empleados</a>
        <a class="fq-btn fq-btn-primary" href="<?=$ruta?>/admin/gestion">Panel</a>
      </div>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type'] ?? '') === 'ok' ? 'fq-alert-ok' : 'fq-alert-danger'?>" style="margin-bottom:14px;">
        <?=htmlspecialchars($flash['msg'] ?? '')?>
      </div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

      <!-- CERTIFICADOS — desplegables -->
      <section class="fq-card">
        <h3 style="margin:0 0 14px; font-size:14px; color:#00e5a0;">
          📋 Certificados médicos
          <span class="muted" style="font-size:11px; font-weight:400;">— clic para expandir</span>
        </h3>

        <?php foreach ($empleados as $emp): ?>
          <?php $eid = $emp['id']; $certs = $certificados[$eid] ?? []; ?>
          <div class="emp-acordeon" id="acord-<?=$eid?>">
            <div class="emp-acordeon__header" onclick="toggleAcord('acord-<?=$eid?>')">
              <div>
                <div class="emp-acordeon__nombre"><?=htmlspecialchars($emp['apellido'].' '.$emp['nombre'])?></div>
                <div class="emp-acordeon__meta">
                  Legajo: <?=htmlspecialchars($emp['legajo'] ?? '—')?>  &nbsp;·&nbsp;
                  <?php if (count($certs) > 0): ?>
                    <span style="color:#00e5a0;"><?=count($certs)?> cert<?=count($certs)>1?'s':''?></span>
                  <?php else: ?>
                    <span style="color:#ffa502;">Sin certificados</span>
                  <?php endif; ?>
                </div>
              </div>
              <span class="emp-acordeon__arrow">▼</span>
            </div>
            <div class="emp-acordeon__body">
              <?php if (empty($certs)): ?>
                <p class="muted" style="font-size:12px; margin:4px 0 10px;">Sin certificados cargados.</p>
              <?php else: ?>
                <?php foreach ($certs as $doc): ?>
                  <div class="doc-item">
                    <div style="min-width:0;">
                      <a href="<?=$ruta?>/docs/empleados/<?=$eid?>/certificados/<?=urlencode($doc['nombre_archivo'])?>"
                         target="_blank" style="font-family:monospace; font-size:12px; color:#00e5a0;">
                        📄 <?=htmlspecialchars($doc['nombre_display'])?>
                      </a>
                      <div class="doc-item__meta">
                        📅 <?=date('d/m/Y H:i', strtotime($doc['subido_en']))?>
                        &nbsp;·&nbsp; 👤 <?=htmlspecialchars($doc['subido_por_usuario'] ?? 'desconocido')?>
                      </div>
                    </div>
                    <a href="<?=$ruta?>/empleado/docs?del=<?=urlencode($doc['nombre_archivo'])?>&tipo=certificado&eid=<?=$eid?>"
                       onclick="return confirm('¿Eliminar?')"
                       class="fq-btn fq-btn-danger" style="padding:4px 9px; font-size:11px; white-space:nowrap;">Eliminar</a>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>

              <form method="POST" enctype="multipart/form-data"
                    style="display:flex; gap:6px; align-items:center; margin-top:8px; flex-wrap:wrap;">
                <input type="hidden" name="tipo_doc" value="certificado">
                <input type="hidden" name="empleado_id" value="<?=$eid?>">
                <div style="flex:1; min-width:0; border:1px dashed rgba(0,229,160,.3); border-radius:9px;
                            padding:6px 10px; background:rgba(0,229,160,.03); display:flex; align-items:center; gap:6px;">
                  <span>📎</span>
                  <span class="muted" style="font-size:11px; font-family:monospace; flex:1;" id="clabel-<?=$eid?>">Adjuntar certificado</span>
                  <button type="button" class="fq-btn" style="font-size:10px; padding:3px 8px;"
                          onclick="document.getElementById('cfile-<?=$eid?>').click()">Buscar</button>
                </div>
                <button class="fq-btn fq-btn-primary" type="submit" style="font-size:11px; padding:5px 10px; white-space:nowrap;">↑ Subir</button>
                <input type="file" name="documento" id="cfile-<?=$eid?>" accept=".pdf,.jpg,.jpeg,.png" style="display:none;"
                       onchange="document.getElementById('clabel-<?=$eid?>').textContent = this.files[0]?.name ?? 'Adjuntar certificado'">
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </section>

      <!-- RECIBOS — adjuntar + lista -->
      <section class="fq-card">
        <h3 style="margin:0 0 14px; font-size:14px; color:#a78bfa;">💰 Recibos de sueldo</h3>

        <?php foreach ($empleados as $emp): ?>
          <?php $eid = $emp['id']; $recps = $recibos[$eid] ?? []; ?>
          <div class="recibo-row">
            <div class="recibo-row__header">
              <div>
                <div style="font-weight:700; font-size:13px;"><?=htmlspecialchars($emp['apellido'].' '.$emp['nombre'])?></div>
                <div class="muted" style="font-family:monospace; font-size:11px; margin-top:2px;">
                  <?php if (count($recps) > 0): ?>
                    <span style="color:#00e5a0;">✅ <?=count($recps)?> recibo<?=count($recps)>1?'s':''?></span>
                  <?php else: ?>
                    <span style="color:#ffa502;">⚠ Sin recibos</span>
                  <?php endif; ?>
                </div>
              </div>
              <form method="POST" enctype="multipart/form-data"
                    style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                <input type="hidden" name="tipo_doc" value="recibo">
                <input type="hidden" name="empleado_id" value="<?=$eid?>">
                <div style="border:1px dashed rgba(167,139,250,.3); border-radius:9px;
                            padding:5px 10px; background:rgba(167,139,250,.03); display:flex; align-items:center; gap:6px;">
                  <span>📎</span>
                  <span class="muted" style="font-size:11px; font-family:monospace;" id="rlabel-<?=$eid?>">Elegir</span>
                  <button type="button" class="fq-btn" style="font-size:10px; padding:3px 8px;"
                          onclick="document.getElementById('rfile-<?=$eid?>').click()">Buscar</button>
                </div>
                <button class="fq-btn" style="border-color:rgba(167,139,250,.4); font-size:11px; padding:5px 10px; white-space:nowrap;" type="submit">
                  ↑ Adjuntar
                </button>
                <input type="file" name="documento" id="rfile-<?=$eid?>" accept=".pdf,.jpg,.jpeg,.png" style="display:none;"
                       onchange="document.getElementById('rlabel-<?=$eid?>').textContent = this.files[0]?.name ?? 'Elegir'">
              </form>
            </div>

            <?php if (!empty($recps)): ?>
              <div class="recibo-row__body">
                <?php foreach ($recps as $doc): ?>
                  <div class="doc-item" style="border-color:rgba(167,139,250,.15);">
                    <div style="min-width:0;">
                      <a href="<?=$ruta?>/docs/empleados/<?=$eid?>/recibos/<?=urlencode($doc['nombre_archivo'])?>"
                         target="_blank" style="font-family:monospace; font-size:12px; color:#a78bfa;">
                        📄 <?=htmlspecialchars($doc['nombre_display'])?>
                      </a>
                      <div class="doc-item__meta">
                        📅 <?=date('d/m/Y H:i', strtotime($doc['subido_en']))?>
                        &nbsp;·&nbsp; 👤 <?=htmlspecialchars($doc['subido_por_usuario'] ?? 'desconocido')?>
                        &nbsp;·&nbsp; <span style="color:#00e5a0;">✅ Entregado</span>
                      </div>
                    </div>
                    <a href="<?=$ruta?>/empleado/docs?del=<?=urlencode($doc['nombre_archivo'])?>&tipo=recibo&eid=<?=$eid?>"
                       onclick="return confirm('¿Eliminar?')"
                       class="fq-btn fq-btn-danger" style="padding:4px 9px; font-size:11px; white-space:nowrap;">Eliminar</a>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </section>

    </div>
  </main>
  <?=$footer?>
  <script>
    function toggleAcord(id) { document.getElementById(id).classList.toggle('open'); }
  </script>
</body>
</html>