<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=htmlspecialchars($title)?></title>
  <style>
    .emp-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
      gap: 10px; margin-bottom: 16px;
    }
    .emp-stat {
      background: linear-gradient(180deg, rgba(20,20,32,.9), rgba(15,15,26,.75));
      border: 1px solid #1e1e2e; border-radius: 14px; padding: 14px 16px;
    }
    .emp-stat__num { font-size: 22px; font-weight: 800; }
    .emp-stat__lbl { font-family: monospace; font-size: 11px; color: #7b7b9a; margin-top: 2px; }
    .par-bloque { border: 1px solid #1e1e2e; border-radius: 12px; overflow: hidden; margin-bottom: 8px; }
    .par-bloque__header {
      padding: 8px 12px; background: rgba(10,10,15,.55);
      font-family: monospace; font-size: 12px; color: #7b7b9a;
      display: flex; justify-content: space-between; align-items: center;
    }
    .par-bloque__row {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 12px; border-top: 1px solid rgba(30,30,46,.7); font-size: 13px;
    }
    .emp-card {
      background: linear-gradient(135deg, rgba(124,58,237,.12), rgba(0,229,160,.07));
      border: 1px solid rgba(124,58,237,.25); border-radius: 16px;
      padding: 16px 20px; margin-bottom: 16px;
      display: flex; justify-content: space-between; align-items: center;
      flex-wrap: wrap; gap: 12px;
    }
  </style>
</head>
<body>
  <?=$nav?>
  <main class="fq-container">

    <!-- TARJETA EMPLEADO -->
    <?php if (!empty($empleado)): ?>
    <div class="emp-card">
      <div>
        <div style="font-size:18px; font-weight:800;">
          👤 <?=htmlspecialchars(($empleado['apellido']??'').' '.($empleado['nombre']??''))?>
        </div>
        <div class="muted" style="font-family:monospace; font-size:12px; margin-top:4px;">
          Legajo: <?=htmlspecialchars($empleado['legajo']??'—')?>
          &nbsp;·&nbsp; DNI: <?=htmlspecialchars($empleado['dni']??'—')?>
          <?php if(!empty($empleado['email'])): ?>
            &nbsp;·&nbsp; <?=htmlspecialchars($empleado['email'])?>
          <?php endif; ?>
        </div>
      </div>
      <a class="fq-btn" style="border-color:rgba(0,229,160,.25); color:#00e5a0;"
         href="<?=$ruta?>/empleado/misdocs">📁 Mis documentos</a>
    </div>
    <?php endif; ?>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type']??'')==='ok'?'fq-alert-ok':'fq-alert-danger'?>"
           style="margin-bottom:14px;"><?=htmlspecialchars($flash['msg']??'')?></div>
    <?php endif; ?>

    <!-- FILTROS con formato dd/mm/aaaa -->
    <section class="fq-card" style="margin-bottom:16px;">
      <form method="GET" class="form" id="formFiltro">
        <input type="hidden" name="desde" id="hidDesde" value="<?=htmlspecialchars($desde)?>">
        <input type="hidden" name="hasta" id="hidHasta" value="<?=htmlspecialchars($hasta)?>">
        <div style="display:grid; gap:10px; grid-template-columns:1fr 1fr auto; align-items:end;">
          <div>
            <label class="label">Desde</label>
            <input class="input" type="text" id="visDesde" placeholder="dd/mm/aaaa" maxlength="10"
                   value="<?=date('d/m/Y', strtotime($desde))?>"
                   oninput="sincFecha(this,'hidDesde')" autocomplete="off">
          </div>
          <div>
            <label class="label">Hasta</label>
            <input class="input" type="text" id="visHasta" placeholder="dd/mm/aaaa" maxlength="10"
                   value="<?=date('d/m/Y', strtotime($hasta))?>"
                   oninput="sincFecha(this,'hidHasta')" autocomplete="off">
          </div>
          <div>
            <button class="fq-btn fq-btn-primary" type="submit">Filtrar</button>
          </div>
        </div>
      </form>
    </section>

    <?php
    // ── Calcular pares IN→OUT globales (sin importar el día) ──────────────
    $lista    = $lista ?? [];
    $totalIN  = 0;
    $totalOUT = 0;

    // Ordenar cronológicamente ASC
    usort($lista, fn($a,$b) => strcmp($a['fecha_hora'], $b['fecha_hora']));

    // Parear globalmente: stack de INs, cada OUT consume el IN más antiguo
    $stack        = [];
    $paresGlobal  = [];   // ['in'=>row, 'out'=>row|null]
    foreach ($lista as $f) {
        if ($f['tipo'] === 'IN') {
            $totalIN++;
            $stack[] = $f;
        } else {
            $totalOUT++;
            if (!empty($stack)) {
                $paresGlobal[] = ['in' => array_shift($stack), 'out' => $f];
            } else {
                // OUT sin IN previo (dato raro, igual mostrarlo)
                $paresGlobal[] = ['in' => null, 'out' => $f];
            }
        }
    }
    // INs sin OUT (en turno o incompletos)
    foreach ($stack as $in) {
        $paresGlobal[] = ['in' => $in, 'out' => null];
    }

    // ── Calcular minutos totales (pares completos) ─────────────────────────
    $minutosTotal = 0;
    foreach ($paresGlobal as $p) {
        if ($p['in'] && $p['out']) {
            $diff = strtotime($p['out']['fecha_hora']) - strtotime($p['in']['fecha_hora']);
            if ($diff > 0) $minutosTotal += (int)($diff / 60);
        }
    }
    $horasTotal = intdiv($minutosTotal, 60);
    $minsResto  = $minutosTotal % 60;

    // ── Agrupar por DÍA para mostrar (usar la fecha del IN, o del OUT si no hay IN) ─
    $porDia = [];
    foreach ($paresGlobal as $p) {
        $ref = $p['in'] ?? $p['out'];
        $dia = substr($ref['fecha_hora'], 0, 10);
        $porDia[$dia][] = $p;
    }
    krsort($porDia);  // más reciente primero

    $diasTrabajados = count(array_filter($porDia, fn($d) =>
        count(array_filter($d, fn($p) => $p['in'] !== null)) > 0
    ));

    // Nombres de días en español
    $diasES = ['Mon'=>'Lun','Tue'=>'Mar','Wed'=>'Mié','Thu'=>'Jue','Fri'=>'Vie','Sat'=>'Sáb','Sun'=>'Dom'];
    ?>

    <!-- STATS -->
    <div class="emp-stats">
      <div class="emp-stat">
        <div class="emp-stat__num"><?=$diasTrabajados?></div>
        <div class="emp-stat__lbl">DÍAS TRABAJADOS</div>
      </div>
      <div class="emp-stat">
        <div class="emp-stat__num" style="color:#00e5a0;"><?=$totalIN?></div>
        <div class="emp-stat__lbl">ENTRADAS</div>
      </div>
      <div class="emp-stat">
        <div class="emp-stat__num" style="color:#ffa502;"><?=$totalOUT?></div>
        <div class="emp-stat__lbl">SALIDAS</div>
      </div>
      <div class="emp-stat">
        <div class="emp-stat__num" style="color:#a78bfa;"><?=$horasTotal?>h <?=$minsResto?>m</div>
        <div class="emp-stat__lbl">HORAS APROX.</div>
      </div>
    </div>

    <!-- FICHADAS POR DÍA -->
    <section class="fq-card">
      <div class="muted" style="font-family:monospace; font-size:12px; margin-bottom:12px;">
        <?=count($lista)?> fichadas ·
        <?=date('d/m/Y', strtotime($desde))?> → <?=date('d/m/Y', strtotime($hasta))?>
      </div>

      <?php if (empty($lista)): ?>
        <p class="muted" style="text-align:center; padding:20px;">Sin fichadas en el período.</p>
      <?php else: ?>

        <?php foreach ($porDia as $dia => $pares): ?>
          <?php
            // Horas del día (solo pares completos de este día)
            $minsDia = 0;
            foreach ($pares as $p) {
                if ($p['in'] && $p['out']) {
                    $diff = strtotime($p['out']['fecha_hora']) - strtotime($p['in']['fecha_hora']);
                    if ($diff > 0) $minsDia += (int)($diff / 60);
                }
            }
            $labelDia = date('d/m/Y · D', strtotime($dia));
            $labelDia = str_replace(array_keys($diasES), array_values($diasES), $labelDia);
          ?>
          <div class="par-bloque">
            <div class="par-bloque__header">
              <span>📅 <?=$labelDia?></span>
              <?php if ($minsDia > 0): ?>
                <span style="color:#00e5a0;">⏱ <?=intdiv($minsDia,60)?>h <?=$minsDia%60?>m</span>
              <?php endif; ?>
            </div>

            <?php foreach ($pares as $p):
              $rowIn  = $p['in'];
              $rowOut = $p['out'];
              // calcular tiempo del par
              $tiempoPar = '';
              if ($rowIn && $rowOut) {
                $diffPar = strtotime($rowOut['fecha_hora']) - strtotime($rowIn['fecha_hora']);
                if ($diffPar > 0) {
                  $hP = intdiv((int)($diffPar/60), 60);
                  $mP = (int)($diffPar/60) % 60;
                  $tiempoPar = "⏱ {$hP}h {$mP}m";
                }
              }
            ?>
              <div class="par-bloque__row" style="display:grid; grid-template-columns:1fr 1fr; gap:0; padding:0;">
                <!-- ENTRADA -->
                <div style="display:flex; align-items:center; gap:8px; padding:10px 12px; border-right:1px solid rgba(30,30,46,.7);">
                  <?php if ($rowIn): ?>
                    <span class="fq-badge fq-badge-in" style="font-size:11px;">▶ Entrada</span>
                    <span style="font-family:monospace; font-weight:700; font-size:14px; color:#00e5a0;">
                      <?=date('H:i', strtotime($rowIn['fecha_hora']))?>
                    </span>
                    <?php if (!empty($rowIn['comentario'])): ?>
                      <span class="muted" style="font-size:11px;">— <?=htmlspecialchars($rowIn['comentario'])?></span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="muted" style="font-size:12px;">—</span>
                  <?php endif; ?>
                </div>
                <!-- SALIDA -->
                <div style="display:flex; align-items:center; gap:8px; padding:10px 12px;">
                  <?php if ($rowOut): ?>
                    <span class="fq-badge fq-badge-out" style="font-size:11px;">◀ Salida</span>
                    <span style="font-family:monospace; font-weight:700; font-size:14px; color:#ffa502;">
                      <?=date('H:i', strtotime($rowOut['fecha_hora']))?>
                    </span>
                    <?php if (!empty($rowOut['comentario'])): ?>
                      <span class="muted" style="font-size:11px;">— <?=htmlspecialchars($rowOut['comentario'])?></span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span style="font-size:11px; color:#ffa502; font-family:monospace;">Sin salida</span>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>

      <?php endif; ?>
    </section>

  </main>
  <?=$footer?>

  <script>
  function sincFecha(vis, hiddenId) {
    let v = vis.value.replace(/[^\d]/g,'');
    if (v.length > 2) v = v.slice(0,2)+'/'+v.slice(2);
    if (v.length > 5) v = v.slice(0,5)+'/'+v.slice(5);
    if (v.length > 10) v = v.slice(0,10);
    vis.value = v;
    const m = v.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
    if (m) document.getElementById(hiddenId).value = m[3]+'-'+m[2]+'-'+m[1];
  }
  </script>
</body>
</html>