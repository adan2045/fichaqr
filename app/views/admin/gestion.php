<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>
  <style>
    /* ── MODALES ── */
    .fq-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,.65); backdrop-filter: blur(4px);
      z-index: 100; place-items: center; padding: 16px;
    }
    .fq-overlay.open { display: grid; }
    .fq-modal {
      background: #13131f; border: 1px solid #1e1e2e;
      border-radius: 18px; padding: 24px; width: 100%;
      max-width: 480px; max-height: 90vh; overflow-y: auto;
      box-shadow: 0 24px 60px rgba(0,0,0,.6);
    }
    .fq-modal__title {
      font-size: 16px; font-weight: 700; margin: 0 0 18px;
      display: flex; justify-content: space-between; align-items: center;
    }
    .fq-modal__close {
      cursor: pointer; background: rgba(255,255,255,.07);
      border: 1px solid #1e1e2e; color: #e8e8f0;
      border-radius: 8px; padding: 4px 10px; font-size: 13px;
    }
    /* ── LIVE DOT ── */
    .fq-live-dot {
      display: inline-block; width: 8px; height: 8px;
      background: #00e5a0; border-radius: 50%;
      animation: pulse 1.5s infinite; vertical-align: middle;
      margin-right: 4px;
    }
    @keyframes pulse {
      0%,100%{ opacity:1; transform:scale(1); }
      50%{ opacity:.4; transform:scale(1.3); }
    }
    /* ── STATS ── */
    .fq-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(110px,1fr));
      gap: 10px; margin-bottom: 16px;
    }
    .fq-stat {
      background: rgba(20,20,32,.88); border: 1px solid #1e1e2e;
      border-radius: 14px; padding: 14px 16px;
    }
    .fq-stat__num { font-size: 22px; font-weight: 800; }
    .fq-stat__lbl { font-family: monospace; font-size: 11px; color: #7b7b9a; margin-top: 2px; }
    /* ── TABLA EN VIVO ── */
    .fq-live-table { width: 100%; border-collapse: collapse; }
    .fq-live-table th {
      font-family: monospace; font-size: 11px; letter-spacing: .08em;
      text-transform: uppercase; color: #7b7b9a;
      padding: 8px 12px; text-align: left;
      border-bottom: 1px solid rgba(255,255,255,.06);
    }
    .fq-live-row td {
      padding: 12px 12px; vertical-align: middle;
      border-bottom: 1px solid rgba(255,255,255,.04);
      font-size: 13px;
    }
    .fq-live-row:last-child td { border-bottom: none; }
    .fq-live-row:hover td { background: rgba(255,255,255,.02); }
    .fq-live-row.en-turno td { background: rgba(0,229,160,.03); }
    /* Nombre */
    .fq-emp-nombre { font-weight: 700; font-size: 14px; }
    .fq-emp-legajo { font-family: monospace; font-size: 11px; color: #7b7b9a; }
    /* Chips EN TURNO / SE FUE */
    .chip-turno {
      display: inline-block; padding: 3px 10px; border-radius: 999px;
      font-size: 11px; font-weight: 700;
      background: rgba(0,229,160,.12); border: 1px solid rgba(0,229,160,.3); color: #00e5a0;
    }
    .chip-fue {
      display: inline-block; padding: 3px 10px; border-radius: 999px;
      font-size: 11px; font-weight: 700;
      background: rgba(255,165,0,.10); border: 1px solid rgba(255,165,0,.25); color: #ffa502;
    }
    /* Hora */
    .fq-hora { font-family: monospace; font-size: 13px; }
    .fq-hora-big { font-family: monospace; font-size: 14px; font-weight: 700; }
    /* Timer */
    .fq-timer { font-family: monospace; font-size: 13px; font-weight: 700; color: #00e5a0; }
    .fq-timer-off { font-family: monospace; font-size: 13px; color: #7b7b9a; }
    /* Botón docs */
    .fq-btn-docs {
      border-color: rgba(0,229,160,.3); color: #00e5a0; font-size: 12px; padding: 5px 14px;
    }
    .fq-btn-docs:hover { background: rgba(0,229,160,.08); }
    /* Badge origen */
    .badge-qr    { border-color: rgba(124,58,237,.35); color: #a78bfa; font-size: 10px; }
    .badge-pin   { border-color: rgba(255,165,0,.35);  color: #ffa502; font-size: 10px; }
    .badge-admin { border-color: rgba(124,58,237,.45); color: #a78bfa; font-size: 10px; }
    /* Edit icons */
    .fq-edit-btn {
      background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.10);
      color: rgba(255,255,255,.7); border-radius: 8px; padding: 4px 9px;
      cursor: pointer; font-size: 12px; transition: background .12s;
    }
    .fq-edit-btn:hover { background: rgba(255,255,255,.12); }
    /* Total horas */
    .fq-total-row td {
      font-family: monospace; font-size: 11px; color: #7b7b9a;
      padding: 4px 12px 10px; border-bottom: 2px solid rgba(124,58,237,.20);
    }
    /* Separador de empleado */
    .fq-sep-row td {
      padding: 14px 12px 6px;
      border-top: 2px solid rgba(124,58,237,.18);
    }
  </style>
</head>
<body>
  <?=$nav?>
  <main class="fq-container">

    <!-- HEADER -->
    <div class="fq-actions" style="justify-content:space-between; align-items:center; margin-bottom:14px;">
      <div>
        <h2 style="margin:0;">Panel de fichadas</h2>
        <div class="muted" style="margin-top:4px; font-family:monospace; font-size:12px;">
          <span class="fq-live-dot"></span>
          actualizado <span id="horaActualizada">—</span>
        </div>
      </div>
      <div class="fq-actions">
        <a class="fq-btn fq-btn-docs" href="<?=$ruta?>/empleado/docs"
           style="padding:10px 22px; font-size:14px; font-weight:700;">
          📁 Documentos
        </a>
      </div>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type']??'')==='ok'?'fq-alert-ok':'fq-alert-danger'?>"
           style="margin-bottom:14px;"><?=$flash['msg']??''?></div>
    <?php endif; ?>

    <!-- FILTROS -->
    <section class="fq-card" style="margin-bottom:16px;">
      <form method="GET" class="form" id="formFiltro">
        <!-- inputs reales ocultos que mandan Y-m-d al server -->
        <input type="hidden" name="desde" id="inputDesde" value="<?=htmlspecialchars($desde)?>">
        <input type="hidden" name="hasta" id="inputHasta" value="<?=htmlspecialchars($hasta)?>">
        <div style="display:grid; gap:10px; grid-template-columns:1fr 1fr 1.5fr auto; align-items:end;">
          <div>
            <label class="label">Desde</label>
            <input class="input" type="text" id="visDesde" placeholder="dd/mm/aaaa" maxlength="10"
                   value="<?=date('d/m/Y', strtotime($desde))?>"
                   oninput="sincFecha(this,'inputDesde')" autocomplete="off">
          </div>
          <div>
            <label class="label">Hasta</label>
            <input class="input" type="text" id="visHasta" placeholder="dd/mm/aaaa" maxlength="10"
                   value="<?=date('d/m/Y', strtotime($hasta))?>"
                   oninput="sincFecha(this,'inputHasta')" autocomplete="off">
          </div>
          <div>
            <label class="label">Empleado (opcional)</label>
            <select class="input" name="empleado_id">
              <option value="">Todos</option>
              <?php foreach(($empleados??[]) as $e): ?>
                <option value="<?=$e['id']?>" <?=($empleadoId===(int)$e['id'])?'selected':''?>>
                  <?=htmlspecialchars(($e['apellido']??'').' '.($e['nombre']??'').' · '.($e['legajo']??''))?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div style="display:flex; gap:8px;">
            <button class="fq-btn fq-btn-primary" type="submit">Mostrar</button>
            <button class="fq-btn" type="button" onclick="irHoy()">Hoy</button>
          </div>
        </div>
      </form>
    </section>

    <?php
    // ── Preparar grupos por empleado ──────────────────────────────────────
    $grupos = [];
    foreach ($fichadas as $f) {
      $eid = $f['empleado_id'];
      if (!isset($grupos[$eid])) $grupos[$eid] = ['info'=>$f,'rows'=>[]];
      $grupos[$eid]['rows'][] = $f;
    }

    // ── Stats ─────────────────────────────────────────────────────────────
    $totalFichadas   = count($fichadas);
    $totalIN         = count(array_filter($fichadas, fn($f)=>$f['tipo']==='IN'));
    $totalOUT        = count(array_filter($fichadas, fn($f)=>$f['tipo']==='OUT'));
    $empleadosUnicos = count($grupos);
    $enTurnoTotal    = 0;
    foreach ($grupos as $g) {
      if (($g['rows'][0]['tipo']??'') === 'IN') $enTurnoTotal++;
    }

    // ── Para cada empleado: parear IN→OUT y calcular horas ───────────────
    // Devuelve array de pares ['in'=>row,'out'=>row|null] + minutos totales
    function parsearPares(array $rows): array {
      $pares   = [];
      $stack   = [];
      foreach (array_reverse($rows) as $r) {
        if ($r['tipo']==='IN')       { $stack[] = $r; }
        elseif (!empty($stack))      { $pares[] = ['in'=>array_pop($stack),'out'=>$r]; }
        else                         { $pares[] = ['in'=>null,'out'=>$r]; }
      }
      // IN sin OUT (en turno)
      while (!empty($stack)) { $pares[] = ['in'=>array_pop($stack),'out'=>null]; }
      // Ordenar por hora de entrada desc
      usort($pares, function($a,$b){
        $ta = strtotime($a['in']['fecha_hora']??$a['out']['fecha_hora']??'0');
        $tb = strtotime($b['in']['fecha_hora']??$b['out']['fecha_hora']??'0');
        return $tb - $ta;
      });
      return $pares;
    }

    function calcularMins(array $pares): int {
      $mins = 0;
      foreach ($pares as $p) {
        if (!$p['in']) continue;
        $fin = $p['out'] ? strtotime($p['out']['fecha_hora']) : time();
        $mins += (int)round(($fin - strtotime($p['in']['fecha_hora'])) / 60);
      }
      return $mins;
    }
    ?>

    <!-- STATS -->
    <div class="fq-stats">
      <div class="fq-stat">
        <div class="fq-stat__num"><?=$totalFichadas?></div>
        <div class="fq-stat__lbl">TOTAL</div>
      </div>
      <div class="fq-stat">
        <div class="fq-stat__num" style="color:#00e5a0;"><?=$enTurnoTotal?></div>
        <div class="fq-stat__lbl">EN TURNO</div>
      </div>
      <div class="fq-stat">
        <div class="fq-stat__num" style="color:#00e5a0;"><?=$totalIN?></div>
        <div class="fq-stat__lbl">ENTRADAS</div>
      </div>
      <div class="fq-stat">
        <div class="fq-stat__num" style="color:#ffa502;"><?=$totalOUT?></div>
        <div class="fq-stat__lbl">SALIDAS</div>
      </div>
      <div class="fq-stat">
        <div class="fq-stat__num" style="color:#a78bfa;"><?=$empleadosUnicos?></div>
        <div class="fq-stat__lbl">EMPLEADOS</div>
      </div>
    </div>

    <!-- TABLA EN VIVO -->
    <section class="fq-card">
      <div class="muted" style="font-family:monospace; font-size:12px; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
        <?=$totalFichadas?> fichadas ·
        <?=date('d/m/Y', strtotime($desde))?> → <?=date('d/m/Y', strtotime($hasta))?>
        · <?=$empleadosUnicos?> empleado(s)
        · <span class="fq-live-dot"></span> <span id="liveInfo">en vivo</span>
      </div>

      <?php if (empty($grupos)): ?>
        <div class="muted" style="text-align:center; padding:30px;">Sin fichadas en el período</div>
      <?php else: ?>
      <table class="fq-live-table">
        <thead>
          <tr>
            <th>EMPLEADO</th>
            <th>FECHA</th>
            <th>INGRESO</th>
            <th>SALIDA</th>
            <th>TRABAJADO</th>
            <th>OBS.</th>
            <th>ACCIONES</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($grupos as $eid => $grupo):
          $info    = $grupo['info'];
          $pares   = parsearPares($grupo['rows']);
          $mins    = calcularMins($pares);
          $hTotal  = sprintf('%02d:%02d', intdiv($mins,60), $mins%60);
          $enTurno = ($grupo['rows'][0]['tipo']??'') === 'IN';
        ?>
          <!-- SEPARADOR EMPLEADO -->
          <tr class="fq-sep-row">
            <td colspan="7">
              <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <span class="fq-emp-nombre">
                  <?=htmlspecialchars(($info['apellido']??'').' '.($info['nombre']??''))?>
                </span>
                <span class="fq-emp-legajo">#<?=htmlspecialchars(ltrim(str_replace('EMP-','',$info['legajo']??''),'0'))?></span>
                <?php if ($enTurno): ?>
                  <span class="chip-turno">EN TURNO</span>
                <?php else: ?>
                  <span class="chip-fue">SE FUE</span>
                <?php endif; ?>
                <span class="muted" style="font-size:11px; font-family:monospace;">Total: <?=$hTotal?></span>
                <a href="<?=$ruta?>/empleado/docs?id=<?=$eid?>"
                   class="fq-btn fq-btn-docs" style="margin-left:auto; padding:3px 12px; font-size:11px;">
                  📁 Docs
                </a>
              </div>
            </td>
          </tr>

          <?php foreach ($pares as $par):
            $rowIn  = $par['in'];
            $rowOut = $par['out'];
            $esActivo = ($rowIn && !$rowOut);
            $parMins = 0;
            if ($rowIn) {
              $fin = $rowOut ? strtotime($rowOut['fecha_hora']) : time();
              $parMins = (int)round(($fin - strtotime($rowIn['fecha_hora'])) / 60);
            }
            $parHora = $parMins > 0 ? sprintf('%02d:%02d', intdiv($parMins,60), $parMins%60) : '—';
            // origen: del IN o del OUT
            $origenIn  = $rowIn['origen']  ?? '';
            $origenOut = $rowOut['origen'] ?? '';
            $comentario = trim(($rowIn['comentario']??'').' '.($rowOut['comentario']??''));
          ?>
          <tr class="fq-live-row <?=$esActivo?'en-turno':''?>">
            <!-- Empleado (vacío, ya está en sep) -->
            <td></td>
            <!-- Fecha -->
            <td class="fq-hora">
              <?php
                $fRef = $rowIn ?? $rowOut;
                echo $fRef ? date('d/m/Y', strtotime($fRef['fecha_hora'])) : '—';
              ?>
            </td>
            <!-- INGRESO -->
            <td>
              <?php if ($rowIn): ?>
                <div style="display:flex; align-items:center; gap:6px;">
                  <span class="fq-hora-big" style="color:#00e5a0;">
                    <?=date('H:i', strtotime($rowIn['fecha_hora']))?>
                  </span>
                  <?php if ($origenIn === 'qr'): ?>
                    <span class="fq-badge badge-qr">QR</span>
                  <?php elseif ($origenIn === 'PIN'): ?>
                    <span class="fq-badge badge-pin">PIN</span>
                  <?php elseif ($origenIn === 'admin'): ?>
                    <span class="fq-badge badge-admin">ADM</span>
                  <?php endif; ?>
                  <button class="fq-edit-btn" title="Editar entrada"
                    onclick="abrirEditar(<?=$rowIn['id']?>,'<?=htmlspecialchars($rowIn['fecha_hora'])?>','IN','<?=htmlspecialchars(addslashes($rowIn['comentario']??''))?>','<?=htmlspecialchars(addslashes(($info['apellido']??'').' '.($info['nombre']??'')))?>') ">
                    ✏
                  </button>
                </div>
              <?php else: ?>
                <span class="muted">—</span>
              <?php endif; ?>
            </td>
            <!-- SALIDA -->
            <td>
              <?php if ($rowOut): ?>
                <div style="display:flex; align-items:center; gap:6px;">
                  <span class="fq-hora-big" style="color:#ffa502;">
                    <?=date('H:i', strtotime($rowOut['fecha_hora']))?>
                  </span>
                  <?php if ($origenOut === 'qr'): ?>
                    <span class="fq-badge badge-qr">QR</span>
                  <?php elseif ($origenOut === 'PIN'): ?>
                    <span class="fq-badge badge-pin">PIN</span>
                  <?php elseif ($origenOut === 'admin'): ?>
                    <span class="fq-badge badge-admin">ADM</span>
                  <?php endif; ?>
                  <button class="fq-edit-btn" title="Editar salida"
                    onclick="abrirEditar(<?=$rowOut['id']?>,'<?=htmlspecialchars($rowOut['fecha_hora'])?>','OUT','<?=htmlspecialchars(addslashes($rowOut['comentario']??''))?>','<?=htmlspecialchars(addslashes(($info['apellido']??'').' '.($info['nombre']??'')))?>') ">
                    ✏
                  </button>
                  <button class="fq-edit-btn" title="Eliminar salida" style="color:#ff6b6b;"
                    onclick="confirmarEliminar(<?=$rowOut['id']?>,'<?=htmlspecialchars(addslashes(($info['apellido']??'').' '.($info['nombre']??'')))?>','<?=htmlspecialchars($rowOut['fecha_hora'])?>') ">
                    🗑
                  </button>
                </div>
              <?php elseif ($rowIn): ?>
                <span class="chip-turno" style="font-size:11px;">EN TURNO</span>
              <?php else: ?>
                <span class="muted">—</span>
              <?php endif; ?>
            </td>
            <!-- TRABAJADO -->
            <td>
              <?php if ($esActivo): ?>
                <span class="fq-timer"
                  data-desde="<?=$rowIn ? strtotime($rowIn['fecha_hora']) : 0?>">—</span>
              <?php else: ?>
                <span class="fq-timer-off"><?=$parHora?></span>
              <?php endif; ?>
            </td>
            <!-- OBS -->
            <td class="muted" style="font-size:12px; max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
              <?=htmlspecialchars($comentario ?: '—')?>
            </td>
            <!-- ACCIONES -->
            <td>
              <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
                <?php if ($rowIn && !$rowOut): ?>
                  <!-- Falta salida: botón para completarla -->
                  <button class="fq-btn fq-btn-warn" style="padding:5px 12px; font-size:12px; font-weight:700;"
                    onclick="abrirCompletarSalida(<?=$eid?>,'<?=htmlspecialchars(addslashes(($info['apellido']??'').' '.($info['nombre']??'')))?>') ">
                    + Salida
                  </button>
                <?php endif; ?>
                <?php if (!$rowIn && $rowOut): ?>
                  <!-- Falta entrada: botón para completarla -->
                  <button class="fq-btn fq-btn-primary" style="padding:5px 12px; font-size:12px; font-weight:700;"
                    onclick="abrirCompletarEntrada(<?=$eid?>,'<?=htmlspecialchars(addslashes(($info['apellido']??'').' '.($info['nombre']??'')))?>') ">
                    + Entrada
                  </button>
                <?php endif; ?>
                <?php if ($rowIn): ?>
                  <button class="fq-btn fq-btn-danger" style="padding:4px 10px; font-size:12px;"
                    onclick="confirmarEliminar(<?=$rowIn['id']?>,'<?=htmlspecialchars(addslashes(($info['apellido']??'').' '.($info['nombre']??'')))?>','<?=htmlspecialchars($rowIn['fecha_hora'])?>') ">
                    ✕ Ent.
                  </button>
                <?php endif; ?>
                <?php if ($rowOut): ?>
                  <button class="fq-btn fq-btn-warn" style="padding:4px 10px; font-size:12px;"
                    onclick="confirmarEliminar(<?=$rowOut['id']?>,'<?=htmlspecialchars(addslashes(($info['apellido']??'').' '.($info['nombre']??'')))?>','<?=htmlspecialchars($rowOut['fecha_hora'])?>') ">
                    ✕ Sal.
                  </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>

          <!-- Fila total del empleado -->
          <tr class="fq-total-row">
            <td colspan="7">Total trabajado: <?=$hTotal?></td>
          </tr>

        <?php endforeach; ?>
        </tbody>
      </table>

      <!-- TOTAL GENERAL -->
      <?php
        $minsTotal = 0;
        foreach ($grupos as $g) {
          $minsTotal += calcularMins(parsearPares($g['rows']));
        }
        $hGen = sprintf('%02d:%02d', intdiv($minsTotal,60), $minsTotal%60);
      ?>
      <div style="text-align:right; margin-top:12px; font-family:monospace; font-size:13px; color:#a78bfa;">
        ⏱ Total general: <?=$hGen?>
      </div>
      <?php endif; ?>
    </section>
  </main>

  <!-- MODAL EDITAR / NUEVA -->
  <div class="fq-overlay" id="fq-modal-overlay" onclick="cerrarModal(event)">
    <div class="fq-modal">
      <div class="fq-modal__title">
        <span id="modal-titulo">Editar fichada</span>
        <button class="fq-modal__close" onclick="cerrarModalDirecto()">✕ Cerrar</button>
      </div>
      <form method="POST" id="fq-modal-form">
        <input type="hidden" name="_action" id="modal-action" value="actualizar">
        <input type="hidden" name="id" id="modal-id" value="">
        <div id="modal-emp-row" style="display:none; margin-bottom:14px;">
          <label class="label">Empleado</label>
          <select class="input" name="empleado_id" id="modal-empleado">
            <option value="">— seleccionar —</option>
            <?php foreach(($empleados??[]) as $e): ?>
              <option value="<?=$e['id']?>">
                <?=htmlspecialchars(($e['apellido']??'').' '.($e['nombre']??'').' · '.($e['legajo']??''))?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="margin-bottom:14px;">
          <label class="label">Fecha y hora</label>
          <input class="input" type="datetime-local" name="fecha_hora" id="modal-fecha" required>
        </div>
        <div style="margin-bottom:14px;">
          <label class="label">Tipo</label>
          <div style="display:flex; gap:10px;">
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
              <input type="radio" name="tipo" value="IN" id="modal-tipo-in" style="accent-color:#00e5a0;">
              <span class="fq-badge fq-badge-in" style="cursor:pointer;">▶ Entrada</span>
            </label>
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
              <input type="radio" name="tipo" value="OUT" id="modal-tipo-out" style="accent-color:#ffa502;">
              <span class="fq-badge fq-badge-out" style="cursor:pointer;">◀ Salida</span>
            </label>
          </div>
        </div>
        <div style="margin-bottom:18px;">
          <label class="label">Comentario (opcional)</label>
          <textarea class="input" name="comentario" id="modal-comentario" rows="2" style="resize:vertical;"></textarea>
        </div>
        <div class="fq-actions">
          <button class="fq-btn fq-btn-primary" type="submit" id="modal-submit-btn">Guardar</button>
          <button class="fq-btn" type="button" onclick="cerrarModalDirecto()">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL ELIMINAR -->
  <div class="fq-overlay" id="fq-modal-del" onclick="cerrarDel(event)">
    <div class="fq-modal" style="max-width:400px; text-align:center;">
      <div style="font-size:32px; margin-bottom:10px;">🗑</div>
      <h3 style="margin:0 0 8px;">Eliminar fichada</h3>
      <p class="muted" style="font-size:13px; margin-bottom:18px;" id="del-msg">¿Confirmar?</p>
      <form method="POST" id="fq-del-form">
        <input type="hidden" name="_action" value="eliminar">
        <input type="hidden" name="id" id="del-id">
        <div class="fq-actions" style="justify-content:center;">
          <button class="fq-btn fq-btn-danger" type="submit">Sí, eliminar</button>
          <button class="fq-btn" type="button"
            onclick="document.getElementById('fq-modal-del').classList.remove('open')">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <?=$footer?>

  <script>
  const RUTA       = <?=json_encode($ruta??'')?>;
  const overlay    = document.getElementById('fq-modal-overlay');
  const delOverlay = document.getElementById('fq-modal-del');
  let autoReload;

  // ── Timers en vivo ──────────────────────────────────────────────────────
  function tickTimers() {
    document.querySelectorAll('[data-desde]').forEach(el => {
      const desde = parseInt(el.dataset.desde);
      if (!desde) return;
      const secs = Math.floor(Date.now()/1000) - desde;
      const h = String(Math.floor(secs/3600)).padStart(2,'0');
      const m = String(Math.floor((secs%3600)/60)).padStart(2,'0');
      const s = String(secs%60).padStart(2,'0');
      el.textContent = h+':'+m+':'+s;
    });
    const ha = document.getElementById('horaActualizada');
    if (ha) ha.textContent = new Date().toLocaleTimeString('es-AR',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
  }
  setInterval(tickTimers, 1000);
  tickTimers();

  // ── Auto-reload 60s ─────────────────────────────────────────────────────
  function resetAutoReload() {
    clearInterval(autoReload);
    autoReload = setInterval(() => {
      if (!overlay.classList.contains('open') && !delOverlay.classList.contains('open')) {
        window.location.reload();
      }
    }, 60000);
  }
  resetAutoReload();

  // ── Convertir dd/mm/aaaa → Y-m-d para el input oculto ──────────────────
  function sincFecha(vis, hiddenId) {
    // Auto-insertar / mientras escribe
    let v = vis.value.replace(/[^\d]/g,'');
    if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2);
    if (v.length > 5) v = v.slice(0,5) + '/' + v.slice(5);
    vis.value = v;
    // Convertir a Y-m-d cuando está completo
    const m = v.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
    if (m) {
      document.getElementById(hiddenId).value = m[3]+'-'+m[2]+'-'+m[1];
    }
  }

  // ── Ir a Hoy ────────────────────────────────────────────────────────────
  function irHoy() {
    const hoy  = new Date();
    const pad  = n => String(n).padStart(2,'0');
    const ymd  = hoy.getFullYear()+'-'+pad(hoy.getMonth()+1)+'-'+pad(hoy.getDate());
    const dma  = pad(hoy.getDate())+'/'+pad(hoy.getMonth()+1)+'/'+hoy.getFullYear();
    document.getElementById('inputDesde').value = ymd;
    document.getElementById('inputHasta').value = ymd;
    document.getElementById('visDesde').value   = dma;
    document.getElementById('visHasta').value   = dma;
    document.getElementById('formFiltro').submit();
  }

  // ── Modales ─────────────────────────────────────────────────────────────
  function abrirEditar(id, fechaHora, tipo, comentario, empleadoNombre) {
    clearInterval(autoReload);
    document.getElementById('modal-titulo').textContent = 'Editar fichada #' + id;
    document.getElementById('modal-action').value = 'actualizar';
    document.getElementById('modal-id').value = id;
    document.getElementById('modal-emp-row').style.display = 'none';
    document.getElementById('modal-fecha').value = fechaHora.replace(' ','T').slice(0,16);
    document.getElementById('modal-tipo-in').checked  = tipo === 'IN';
    document.getElementById('modal-tipo-out').checked = tipo === 'OUT';
    document.getElementById('modal-comentario').value = comentario;
    document.getElementById('modal-submit-btn').textContent = 'Guardar cambios';
    document.getElementById('fq-modal-form').action = RUTA + '/fichada/actualizar';
    overlay.classList.add('open');
  }

  // ── Completar salida faltante ────────────────────────────────────────────
  function abrirCompletarSalida(empId, empNombre) {
    clearInterval(autoReload);
    document.getElementById('modal-titulo').textContent = 'Registrar salida · ' + empNombre;
    document.getElementById('modal-action').value = 'nueva';
    document.getElementById('modal-id').value = '';
    document.getElementById('modal-emp-row').style.display = 'block';
    document.getElementById('modal-empleado').value = empId;
    const now = new Date();
    const pad = n => String(n).padStart(2,'0');
    document.getElementById('modal-fecha').value =
      now.getFullYear()+'-'+pad(now.getMonth()+1)+'-'+pad(now.getDate())
      +'T'+pad(now.getHours())+':'+pad(now.getMinutes());
    document.getElementById('modal-tipo-in').checked  = false;
    document.getElementById('modal-tipo-out').checked = true;
    document.getElementById('modal-comentario').value = 'Salida registrada por admin';
    document.getElementById('modal-submit-btn').textContent = 'Registrar salida';
    document.getElementById('fq-modal-form').action = RUTA + '/fichada/nueva';
    overlay.classList.add('open');
  }

  // ── Completar entrada faltante ───────────────────────────────────────────
  function abrirCompletarEntrada(empId, empNombre) {
    clearInterval(autoReload);
    document.getElementById('modal-titulo').textContent = 'Registrar entrada · ' + empNombre;
    document.getElementById('modal-action').value = 'nueva';
    document.getElementById('modal-id').value = '';
    document.getElementById('modal-emp-row').style.display = 'block';
    document.getElementById('modal-empleado').value = empId;
    const now = new Date();
    const pad = n => String(n).padStart(2,'0');
    document.getElementById('modal-fecha').value =
      now.getFullYear()+'-'+pad(now.getMonth()+1)+'-'+pad(now.getDate())
      +'T'+pad(now.getHours())+':'+pad(now.getMinutes());
    document.getElementById('modal-tipo-in').checked  = true;
    document.getElementById('modal-tipo-out').checked = false;
    document.getElementById('modal-comentario').value = 'Entrada registrada por admin';
    document.getElementById('modal-submit-btn').textContent = 'Registrar entrada';
    document.getElementById('fq-modal-form').action = RUTA + '/fichada/nueva';
    overlay.classList.add('open');
  }

  function abrirNueva() {
    clearInterval(autoReload);
    document.getElementById('modal-titulo').textContent = 'Nueva fichada manual';
    document.getElementById('modal-action').value = 'nueva';
    document.getElementById('modal-id').value = '';
    document.getElementById('modal-emp-row').style.display = 'block';
    document.getElementById('modal-empleado').value = '';
    const now = new Date();
    const pad = n => String(n).padStart(2,'0');
    document.getElementById('modal-fecha').value =
      now.getFullYear()+'-'+pad(now.getMonth()+1)+'-'+pad(now.getDate())
      +'T'+pad(now.getHours())+':'+pad(now.getMinutes());
    document.getElementById('modal-tipo-in').checked  = true;
    document.getElementById('modal-tipo-out').checked = false;
    document.getElementById('modal-comentario').value = '';
    document.getElementById('modal-submit-btn').textContent = 'Registrar';
    document.getElementById('fq-modal-form').action = RUTA + '/fichada/nueva';
    overlay.classList.add('open');
  }

  function confirmarEliminar(id, nombre, fechaHora) {
    clearInterval(autoReload);
    document.getElementById('del-id').value = id;
    document.getElementById('del-msg').textContent =
      '¿Eliminar fichada #' + id + ' de ' + nombre + ' (' + fechaHora + ')?';
    document.getElementById('fq-del-form').action = RUTA + '/fichada/eliminar';
    delOverlay.classList.add('open');
  }

  function cerrarModal(e)       { if (e.target===overlay) { overlay.classList.remove('open'); resetAutoReload(); } }
  function cerrarModalDirecto() { overlay.classList.remove('open'); resetAutoReload(); }
  function cerrarDel(e)         { if (e.target===delOverlay) { delOverlay.classList.remove('open'); resetAutoReload(); } }

  document.addEventListener('keydown', e => {
    if (e.key==='Escape') {
      overlay.classList.remove('open');
      delOverlay.classList.remove('open');
      resetAutoReload();
    }
  });
  </script>
</body>
</html>