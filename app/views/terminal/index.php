<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>
  <style>
    /* ── TARJETA EMPLEADO ── */
    .fq-emp-card {
      background: linear-gradient(135deg, rgba(0,229,160,.08), rgba(124,58,237,.06));
      border: 1px solid rgba(0,229,160,.2);
      border-radius: 20px;
      padding: 28px;
      max-width: 480px;
      margin: 0 auto 24px;
    }
    .fq-emp-avatar {
      width: 64px; height: 64px; border-radius: 16px;
      background: linear-gradient(135deg, rgba(0,229,160,.25), rgba(124,58,237,.2));
      border: 1px solid rgba(0,229,160,.3);
      display: flex; align-items: center; justify-content: center;
      font-size: 28px; margin-bottom: 14px;
    }
    .fq-emp-nombre { font-size: 22px; font-weight: 800; margin: 0 0 4px; }
    .fq-emp-legajo { font-family: monospace; font-size: 13px; color: #7b7b9a; }
    .fq-fichar-btns {
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 12px; margin-top: 22px;
    }
    .fq-fichar-btn {
      padding: 18px; border-radius: 14px; border: none;
      font-size: 16px; font-weight: 800; cursor: pointer;
      transition: transform .1s, box-shadow .1s;
    }
    .fq-fichar-btn:active { transform: scale(.97); }
    .fq-fichar-btn--in {
      background: linear-gradient(135deg, #00e5a0, #00c48a);
      color: #0b0b12;
      box-shadow: 0 8px 24px rgba(0,229,160,.25);
    }
    .fq-fichar-btn--out {
      background: linear-gradient(135deg, #ffa502, #e09000);
      color: #0b0b12;
      box-shadow: 0 8px 24px rgba(255,165,2,.2);
    }
    .fq-fichar-btn--in:hover  { box-shadow: 0 12px 30px rgba(0,229,160,.35); }
    .fq-fichar-btn--out:hover { box-shadow: 0 12px 30px rgba(255,165,2,.30); }
    /* ── HISTORIAL HOY ── */
    .fq-hoy-row {
      display: flex; justify-content: space-between; align-items: center;
      padding: 10px 14px;
      border-radius: 10px;
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.06);
      font-family: monospace; font-size: 13px;
      margin-bottom: 8px;
    }
    /* ── PIN MODAL (admin) ── */
    .fq-pin-field { margin-bottom: 14px; }
    .fq-pin-field label { display:block; font-size:11px; letter-spacing:.1em; text-transform:uppercase; color:rgba(255,255,255,.55); margin-bottom:6px; }
    .fq-pin-field input {
      width:100%; box-sizing:border-box;
      background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12);
      color:#fff; border-radius:12px; padding:12px 14px; font-size:16px;
      outline:none; transition:border-color .15s;
    }
    .fq-pin-field input:focus { border-color:rgba(0,229,160,.5); }
    .fq-pin-preview {
      min-height:56px; margin:14px 0;
      border-radius:14px; border:1px solid rgba(255,255,255,.08);
      background:rgba(255,255,255,.04); padding:12px 16px;
      display:flex; align-items:center; gap:14px;
    }
    .fq-pin-error {
      background:rgba(255,59,59,.12); border:1px solid rgba(255,59,59,.3);
      color:#ff6b6b; border-radius:10px; padding:8px 12px;
      font-size:13px; margin-bottom:10px; display:none;
    }
    .fq-pin-error.show { display:block; }
    .fq-prox {
      margin-top:28px; border:1px dashed rgba(255,255,255,.12);
      border-radius:18px; padding:24px; text-align:center; color:rgba(255,255,255,.35);
    }
  </style>
</head>
<body>
  <?=$nav?>
  <main class="fq-container" style="max-width:560px;">

    <div style="margin-bottom:24px; text-align:center;">
      <h2 style="margin:0;">📋 Fichar</h2>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type']??'')==='ok'?'fq-alert-ok':'fq-alert-danger'?>"
           style="margin-bottom:20px;">
        <?=$flash['msg']??''?>
      </div>
    <?php endif; ?>

    <?php if (!$esAdmin && $empleadoPropio): ?>
    <!-- ══════════════════════════════════════════════
         VISTA EMPLEADO: datos fijos, botones directos
         ══════════════════════════════════════════════ -->

      <div class="fq-emp-card">
        <div class="fq-emp-avatar">👤</div>
        <div class="fq-emp-nombre">
          <?=htmlspecialchars(($empleadoPropio['apellido']??'').' '.($empleadoPropio['nombre']??''))?>
        </div>
        <div class="fq-emp-legajo">
          Legajo: <?=htmlspecialchars($empleadoPropio['legajo']??'—')?>
        </div>

        <!-- Estado de hoy -->
        <?php
          $ultimaFichada = $fichadasHoy[0] ?? null;
          $enTurno = $ultimaFichada && $ultimaFichada['tipo'] === 'IN';
        ?>
        <div style="margin-top:14px; font-size:13px; color:rgba(255,255,255,.6);">
          <?php if ($ultimaFichada): ?>
            Última fichada:
            <strong style="color:<?=$enTurno?'#00e5a0':'#ffa502'?>">
              <?=$enTurno ? '↓ Entrada' : '↑ Salida'?>
            </strong>
            a las <?=date('H:i', strtotime($ultimaFichada['fecha_hora']))?>
          <?php else: ?>
            Hoy aún no fichaste.
          <?php endif; ?>
        </div>

        <!-- Botones fichar -->
        <div class="fq-fichar-btns">
          <form method="POST" action="<?=$ruta?>/terminal/ficharpropio">
            <input type="hidden" name="tipo" value="IN">
            <button class="fq-fichar-btn fq-fichar-btn--in" type="submit"
                    style="width:100%;">
              ↓ Entrada
            </button>
          </form>
          <form method="POST" action="<?=$ruta?>/terminal/ficharpropio">
            <input type="hidden" name="tipo" value="OUT">
            <button class="fq-fichar-btn fq-fichar-btn--out" type="submit"
                    style="width:100%;">
              ↑ Salida
            </button>
          </form>
        </div>
      </div>

        <!-- Historial del día -->
      <section class="fq-card">
        <div style="font-family:monospace; font-size:11px; color:#7b7b9a; margin-bottom:12px; text-transform:uppercase; letter-spacing:.08em;">
          Mis fichadas de hoy
        </div>
        <?php if (empty($fichadasHoy)): ?>
          <div style="color:#7b7b9a; font-size:13px;">Sin fichadas hoy.</div>
        <?php else: ?>
          <?php foreach (array_reverse($fichadasHoy) as $f): ?>
            <div class="fq-hoy-row">
              <span>
                <?php if ($f['tipo']==='IN'): ?>
                  <span class="fq-badge fq-badge-in">▶ Entrada</span>
                <?php else: ?>
                  <span class="fq-badge fq-badge-out">◀ Salida</span>
                <?php endif; ?>
              </span>
              <span style="color:#fff; font-weight:700; font-family:monospace;">
                <?=date('H:i', strtotime($f['fecha_hora']))?>
              </span>
              <span style="color:#7b7b9a; font-size:11px; font-family:monospace;">
                <?=htmlspecialchars($f['origen']??'')?>
              </span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>

    <?php elseif ($esAdmin): ?>
    <!-- ══════════════════════════════════════════════
         VISTA ADMIN/JEFE: buscador con PIN
         ══════════════════════════════════════════════ -->

      <section class="fq-card" style="margin-bottom:20px;">
        <div id="pinError" class="fq-pin-error"></div>

        <div class="fq-pin-field">
          <label>N° de Empleado</label>
          <input type="text" id="inputLegajo" placeholder="Ej: 001"
                 inputmode="numeric" maxlength="10" autocomplete="off">
        </div>
        <div class="fq-pin-field">
          <label>PIN (4 dígitos)</label>
          <input type="password" id="inputPin" placeholder="••••"
                 inputmode="numeric" maxlength="4" autocomplete="off">
        </div>

        <div class="fq-pin-preview" id="preview" style="display:none;">
          <div style="flex:1;">
            <div style="font-weight:700; font-size:16px;" id="prevNombre">—</div>
            <div style="font-size:12px; color:#7b7b9a;" id="prevLegajo">—</div>
            <div style="font-size:12px; color:#7b7b9a; margin-top:2px;" id="prevEstado">—</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:6px;">
          <button class="fq-btn fq-btn-primary" onclick="fichar('IN')"
                  style="padding:12px; font-size:15px; font-weight:700;">
            ↓ Entrada
          </button>
          <button class="fq-btn fq-btn-warn" onclick="fichar('OUT')"
                  style="padding:12px; font-size:15px; font-weight:700;">
            ↑ Salida
          </button>
        </div>
      </section>

      <div class="fq-prox">
        <div style="font-size:36px; margin-bottom:8px;">📷</div>
        <strong style="color:rgba(255,255,255,.5);">Terminal QR — Próximamente</strong>
        <div style="font-size:13px; margin-top:6px;">Escaneá el QR de tu carnet para fichar.</div>
      </div>

    <?php endif; ?>
  </main>

  <?=$footer?>

  <?php if ($esAdmin): ?>
  <script>
  const ruta = <?=json_encode($ruta??'')?>;
  const inputLegajo = document.getElementById('inputLegajo');
  const inputPin    = document.getElementById('inputPin');
  const preview     = document.getElementById('preview');
  const pinError    = document.getElementById('pinError');

  function showError(msg) {
    pinError.textContent = msg;
    pinError.classList.add('show');
    setTimeout(() => pinError.classList.remove('show'), 3500);
  }

  inputLegajo.addEventListener('blur', buscarEmpleado);
  inputLegajo.addEventListener('keydown', e => { if(e.key==='Enter'){ buscarEmpleado(); inputPin.focus(); } });

  function buscarEmpleado() {
    const legajo = inputLegajo.value.trim();
    if (!legajo) { preview.style.display='none'; return; }
    fetch(ruta + '/terminal/buscar?legajo=' + encodeURIComponent(legajo))
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          document.getElementById('prevNombre').textContent = data.empleado.apellido + ' ' + data.empleado.nombre;
          document.getElementById('prevLegajo').textContent = 'Legajo: ' + data.empleado.legajo;
          document.getElementById('prevEstado').textContent = data.estado;
          preview.style.display = 'flex';
        } else {
          preview.style.display = 'none';
        }
      }).catch(() => { preview.style.display='none'; });
  }

  function fichar(tipo) {
    const legajo = inputLegajo.value.trim();
    const pin    = inputPin.value.trim();
    if (!legajo) { showError('Ingresá el número de empleado.'); inputLegajo.focus(); return; }
    if (pin.length !== 4) { showError('El PIN debe tener 4 dígitos.'); inputPin.focus(); return; }
    fetch(ruta + '/terminal/ficharpin', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: 'legajo='+encodeURIComponent(legajo)+'&pin='+encodeURIComponent(pin)+'&tipo='+tipo
    })
    .then(r => r.json())
    .then(data => {
      if (data.ok) {
        const flash = document.createElement('div');
        flash.className = 'fq-alert fq-alert-ok';
        flash.style.marginBottom = '16px';
        flash.innerHTML = data.msg;
        document.querySelector('main').insertBefore(flash, document.querySelector('.fq-card'));
        inputLegajo.value = '';
        inputPin.value    = '';
        preview.style.display = 'none';
        setTimeout(() => flash.remove(), 4000);
      } else {
        showError(data.msg || 'Error al fichar.');
      }
    }).catch(() => showError('Error de conexión.'));
  }

  inputPin.addEventListener('keydown', e => { if(e.key==='Enter') fichar('IN'); });
  </script>
  <?php endif; ?>
</body>
</html>