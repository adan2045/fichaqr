<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=htmlspecialchars($title)?></title>
  <style>
    /* Stats del empleado */
    .emp-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
      gap: 10px;
      margin-bottom: 16px;
    }
    .emp-stat {
      background: linear-gradient(180deg, rgba(20,20,32,.9), rgba(15,15,26,.75));
      border: 1px solid #1e1e2e;
      border-radius: 14px;
      padding: 14px 16px;
    }
    .emp-stat__num  { font-size: 22px; font-weight: 800; }
    .emp-stat__lbl  { font-family: monospace; font-size: 11px; color: #7b7b9a; margin-top: 2px; }

    /* Par IN/OUT */
    .par-bloque {
      border: 1px solid #1e1e2e;
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 8px;
    }
    .par-bloque__header {
      padding: 8px 12px;
      background: rgba(10,10,15,.55);
      font-family: monospace;
      font-size: 12px;
      color: #7b7b9a;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .par-bloque__row {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 12px;
      border-top: 1px solid rgba(30,30,46,.7);
      font-size: 13px;
    }
    .par-bloque__row:first-child { border-top: none; }
    .par-bloque__horas {
      font-family: monospace;
      font-size: 12px;
      color: #00e5a0;
      margin-left: auto;
    }
    .par-bloque__sin-out {
      color: #ffa502;
      font-family: monospace;
      font-size: 11px;
      margin-left: auto;
    }
    /* Docs badge */
    .emp-card {
      background: linear-gradient(135deg, rgba(124,58,237,.12), rgba(0,229,160,.07));
      border: 1px solid rgba(124,58,237,.25);
      border-radius: 16px;
      padding: 16px 20px;
      margin-bottom: 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
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
          👤 <?=htmlspecialchars(($empleado['apellido'] ?? '').' '.($empleado['nombre'] ?? ''))?>
        </div>
        <div class="muted" style="font-family:monospace; font-size:12px; margin-top:4px;">
          Legajo: <?=htmlspecialchars($empleado['legajo'] ?? '—')?>
          &nbsp;·&nbsp;
          DNI: <?=htmlspecialchars($empleado['dni'] ?? '—')?>
          <?php if(!empty($empleado['email'])): ?>
          &nbsp;·&nbsp; <?=htmlspecialchars($empleado['email'])?>
          <?php endif; ?>
        </div>
      </div>
      <div class="fq-actions">
        <a class="fq-btn" style="border-color:rgba(0,229,160,.25); color:#00e5a0;"
           href="<?=$ruta?>/empleado/misdocs">
          📁 Mis documentos
        </a>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type'] ?? '') === 'ok' ? 'fq-alert-ok' : 'fq-alert-danger'?>" style="margin-bottom:14px;">
        <?=htmlspecialchars($flash['msg'] ?? '')?>
      </div>
    <?php endif; ?>

    <!-- FILTROS -->
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

    <?php
    // ── Calcular estadísticas ──
    $totalIN  = 0;
    $totalOUT = 0;
    $minutosTotal = 0;
    $pares = [];   // agrupar por día

    foreach (($lista ?? []) as $f) {
        if ($f['tipo'] === 'IN')  $totalIN++;
        if ($f['tipo'] === 'OUT') $totalOUT++;
        $dia = substr($f['fecha_hora'], 0, 10);
        $pares[$dia][] = $f;
    }

    // Calcular horas por día (primer IN → primer OUT)
    foreach ($pares as $dia => &$registros) {
        usort($registros, fn($a,$b) => strcmp($a['fecha_hora'], $b['fecha_hora']));
        $ins  = array_values(array_filter($registros, fn($r) => $r['tipo']==='IN'));
        $outs = array_values(array_filter($registros, fn($r) => $r['tipo']==='OUT'));
        // emparejar
        for ($i = 0; $i < count($ins); $i++) {
            if (isset($outs[$i])) {
                $t1 = strtotime($ins[$i]['fecha_hora']);
                $t2 = strtotime($outs[$i]['fecha_hora']);
                if ($t2 > $t1) $minutosTotal += (int)(($t2 - $t1) / 60);
            }
        }
    }
    unset($registros);

    $horasTotal  = intdiv($minutosTotal, 60);
    $minsResto   = $minutosTotal % 60;
    $diasTrabajados = count(array_filter($pares, fn($d) => count(array_filter($d, fn($r) => $r['tipo']==='IN')) > 0));
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
        <?=count($lista ?? [])?> fichadas · <?=htmlspecialchars($desde)?> → <?=htmlspecialchars($hasta)?>
      </div>

      <?php if (empty($lista)): ?>
        <p class="muted" style="text-align:center; padding:20px;">Sin fichadas en el período seleccionado.</p>
      <?php else: ?>
        <?php foreach (array_reverse($pares, true) as $dia => $registros): ?>
          <?php
            $insD  = array_values(array_filter($registros, fn($r) => $r['tipo']==='IN'));
            $outsD = array_values(array_filter($registros, fn($r) => $r['tipo']==='OUT'));
            // horas del día
            $minsDia = 0;
            for ($i=0; $i<count($insD); $i++) {
                if (isset($outsD[$i])) {
                    $t1 = strtotime($insD[$i]['fecha_hora']);
                    $t2 = strtotime($outsD[$i]['fecha_hora']);
                    if ($t2 > $t1) $minsDia += (int)(($t2-$t1)/60);
                }
            }
            $labelDia = date('d/m/Y · D', strtotime($dia));
            $labelDia = str_replace(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                                     ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'], $labelDia);
          ?>
          <div class="par-bloque">
            <div class="par-bloque__header">
              <span>📅 <?=$labelDia?></span>
              <?php if ($minsDia > 0): ?>
                <span style="color:#00e5a0;">⏱ <?=intdiv($minsDia,60)?>h <?=$minsDia%60?>m</span>
              <?php endif; ?>
            </div>
            <?php foreach ($registros as $f): ?>
              <div class="par-bloque__row">
                <?php if ($f['tipo'] === 'IN'): ?>
                  <span class="fq-badge fq-badge-in">▶ IN</span>
                <?php else: ?>
                  <span class="fq-badge fq-badge-out">◀ OUT</span>
                <?php endif; ?>
                <span style="font-family:monospace;"><?=substr($f['fecha_hora'],11,5)?></span>
                <?php if (!empty($f['comentario'])): ?>
                  <span class="muted" style="font-size:12px;">— <?=htmlspecialchars($f['comentario'])?></span>
                <?php endif; ?>
                <span class="muted" style="font-size:11px; margin-left:auto; font-family:monospace;"><?=$f['origen']?></span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

  </main>
  <?=$footer?>
</body>
</html>
