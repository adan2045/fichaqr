<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=htmlspecialchars($title)?></title>
</head>
<body>
  <?=$nav?>

  <main class="fq-container">

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:20px;">
      <div>
        <h2 style="margin:0;">💰 Recibos de sueldo</h2>
        <div class="muted" style="font-family:monospace; font-size:12px; margin-top:4px;">
          Cargá el recibo mensual de cada empleado
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

    <?php
    function nombreLimpio(string $f): string {
        return preg_replace('/^\d{8}_\d{6}_/', '', $f);
    }
    ?>

    <div style="display:grid; gap:12px;">
      <?php foreach ($empleados as $emp): ?>
        <?php
          $eid      = $emp['id'];
          $archivos = $recibos[$eid] ?? [];
        ?>
        <section class="fq-card" style="padding:16px 20px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">

            <!-- Info empleado -->
            <div style="min-width:180px;">
              <div style="font-weight:700; font-size:15px;">
                <?=htmlspecialchars($emp['apellido'].' '.$emp['nombre'])?>
              </div>
              <div class="muted" style="font-family:monospace; font-size:11px; margin-top:3px;">
                Legajo: <?=htmlspecialchars($emp['legajo'] ?? '—')?>
                <?php if (!empty($emp['email'])): ?>
                  · <?=htmlspecialchars($emp['email'])?>
                <?php endif; ?>
              </div>
              <div style="margin-top:6px;">
                <?php if (count($archivos) > 0): ?>
                  <span style="font-size:11px; color:#00e5a0; font-family:monospace;">
                    ✅ <?=count($archivos)?> recibo<?=count($archivos)>1?'s':''?> cargado<?=count($archivos)>1?'s':''?>
                  </span>
                <?php else: ?>
                  <span style="font-size:11px; color:#ffa502; font-family:monospace;">⚠ Sin recibos</span>
                <?php endif; ?>
              </div>
            </div>

            <!-- Formulario adjuntar -->
            <form method="POST" enctype="multipart/form-data"
                  style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; flex:1; min-width:220px;">
              <input type="hidden" name="empleado_id" value="<?=$eid?>">
              <div style="flex:1; min-width:0; border:1px dashed rgba(167,139,250,.35); border-radius:10px;
                          padding:7px 12px; background:rgba(167,139,250,.04); display:flex; align-items:center; gap:8px;">
                <span>📎</span>
                <span class="muted" style="font-size:12px; font-family:monospace; flex:1;" id="label-<?=$eid?>">
                  Elegir archivo
                </span>
                <button type="button" class="fq-btn" style="font-size:11px; padding:4px 9px;"
                        onclick="document.getElementById('file-<?=$eid?>').click()">Buscar</button>
              </div>
              <button class="fq-btn" style="border-color:rgba(167,139,250,.4); white-space:nowrap;" type="submit">
                ↑ Adjuntar
              </button>
              <input type="file" name="documento" id="file-<?=$eid?>" accept=".pdf,.jpg,.jpeg,.png"
                     style="display:none;"
                     onchange="document.getElementById('label-<?=$eid?>').textContent = this.files[0]?.name ?? 'Elegir archivo'">
            </form>

          </div>

          <!-- Lista recibos del empleado -->
          <?php if (!empty($archivos)): ?>
            <ul style="list-style:none; padding:0; margin:14px 0 0; display:grid; gap:6px;">
              <?php foreach ($archivos as $f): ?>
                <li style="display:flex; justify-content:space-between; align-items:center; gap:8px;
                            padding:7px 10px; background:rgba(10,10,15,.4); border:1px solid rgba(167,139,250,.15); border-radius:9px;">
                  <div style="display:flex; flex-direction:column; gap:2px; min-width:0;">
                    <a href="<?=$ruta?>/docs/empleados/<?=$eid?>/recibos/<?=urlencode($f)?>"
                       target="_blank"
                       style="font-family:monospace; font-size:12px; color:#a78bfa; word-break:break-all;">
                      📄 <?=htmlspecialchars(nombreLimpio($f))?>
                    </a>
                    <span style="font-size:10px; color:#00e5a0; font-family:monospace;">✅ Recibo entregado</span>
                  </div>
                  <a href="<?=$ruta?>/empleado/recibos?del=<?=urlencode($f)?>&eid=<?=$eid?>"
                     onclick="return confirm('¿Eliminar este recibo?')"
                     class="fq-btn fq-btn-danger" style="padding:4px 10px; font-size:11px; white-space:nowrap;">
                    Eliminar
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

        </section>
      <?php endforeach; ?>
    </div>

  </main>
  <?=$footer?>
</body>
</html>