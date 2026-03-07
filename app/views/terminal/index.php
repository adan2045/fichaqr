<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>
  <style>
    /* ── PIN MODAL ── */
    .fq-pin-overlay {
      position: fixed; inset: 0;
      background: rgba(0,0,0,.55);
      backdrop-filter: blur(4px);
      display: flex; align-items: center; justify-content: center;
      z-index: 1000;
    }
    .fq-pin-modal {
      background: var(--fq-surface, #1a1a2e);
      border: 1px solid rgba(255,255,255,.10);
      border-radius: 20px;
      padding: 28px 28px 24px;
      width: 100%; max-width: 420px;
      box-shadow: 0 24px 60px rgba(0,0,0,.55);
    }
    .fq-pin-header {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 20px;
    }
    .fq-pin-header h2 { margin: 0; font-size: 20px; }
    .fq-pin-close {
      margin-left: auto; background: none; border: none;
      color: rgba(255,255,255,.5); font-size: 22px;
      cursor: pointer; line-height: 1; padding: 2px 6px;
      border-radius: 8px; transition: color .15s;
    }
    .fq-pin-close:hover { color: #fff; }

    .fq-pin-field { margin-bottom: 14px; }
    .fq-pin-field label { display: block; font-size: 11px; letter-spacing: .1em; text-transform: uppercase; color: rgba(255,255,255,.55); margin-bottom: 6px; }
    .fq-pin-field input {
      width: 100%; box-sizing: border-box;
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.12);
      color: #fff; border-radius: 12px;
      padding: 12px 14px; font-size: 16px;
      outline: none; transition: border-color .15s;
    }
    .fq-pin-field input:focus { border-color: rgba(0,229,160,.5); }

    .fq-pin-preview {
      min-height: 64px; margin: 14px 0;
      border-radius: 14px;
      border: 1px solid rgba(255,255,255,.08);
      background: rgba(255,255,255,.04);
      padding: 12px 16px;
      display: flex; align-items: center; gap: 14px;
    }
    .fq-pin-preview-name { font-weight: 700; font-size: 17px; }
    .fq-pin-preview-sub  { font-size: 12px; color: rgba(255,255,255,.5); margin-top: 2px; }
    .fq-pin-preview-badge {
      margin-left: auto;
      background: rgba(0,229,160,.12);
      border: 1px solid rgba(0,229,160,.25);
      color: #00e5a0; border-radius: 8px;
      padding: 4px 10px; font-size: 12px; font-weight: 700;
    }
    .fq-pin-timer { font-size: 22px; font-weight: 800; font-family: monospace; color: #00e5a0; }

    .fq-pin-actions { display: flex; gap: 10px; margin-top: 6px; }
    .fq-pin-actions .fq-btn { flex: 1; justify-content: center; }

    .fq-pin-error {
      background: rgba(255,59,59,.12);
      border: 1px solid rgba(255,59,59,.3);
      color: #ff6b6b; border-radius: 10px;
      padding: 8px 12px; font-size: 13px; margin-bottom: 10px;
      display: none;
    }
    .fq-pin-error.show { display: block; }

    /* ── PRÓXIMAMENTE QR ── */
    .fq-prox {
      margin-top: 32px;
      border: 1px dashed rgba(255,255,255,.15);
      border-radius: 18px;
      padding: 28px;
      text-align: center;
      color: rgba(255,255,255,.4);
    }
    .fq-prox-icon { font-size: 40px; margin-bottom: 10px; }
    .fq-prox h3 { margin: 0 0 6px; font-size: 16px; color: rgba(255,255,255,.6); }
    .fq-prox p  { margin: 0; font-size: 13px; }
  </style>
</head>

<body>
  <?=$nav?>

  <main class="fq-container" style="max-width:600px;">

    <div style="margin-bottom:20px;">
      <h2 style="margin:0;">📋 Fichar</h2>
      <div class="muted" style="margin-top:4px; font-size:12px; font-family:monospace;">
        Registrá tu entrada o salida con tu número de empleado y PIN
      </div>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type'] ?? '') === 'ok' ? 'alert-ok' : 'fq-alert-danger'?>"
           style="margin-bottom:16px;">
        <?=$flash['msg'] ?? ''?>
      </div>
    <?php endif; ?>

    <!-- ── PANEL FICHADA MANUAL ── -->
    <section class="fq-card" id="panelFichar">

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

      <!-- Preview del empleado (se llena vía JS al buscar) -->
      <div class="fq-pin-preview" id="preview" style="display:none;">
        <div>
          <div class="fq-pin-preview-name" id="prevNombre">—</div>
          <div class="fq-pin-preview-sub"  id="prevLegajo">—</div>
        </div>
        <div>
          <div class="fq-pin-timer" id="prevTimer">—</div>
          <div style="font-size:11px; color:rgba(255,255,255,.4); text-align:center;" id="prevEstado">—</div>
        </div>
        <span class="fq-pin-preview-badge" id="prevBadge">EMP</span>
      </div>

      <div class="fq-pin-actions">
        <button class="fq-btn fq-btn-primary" onclick="fichar('IN')">↓ Entrada</button>
        <button class="fq-btn fq-btn-warn"    onclick="fichar('OUT')">↑ Salida</button>
      </div>
    </section>

    <!-- ── PRÓXIMAMENTE: QR ── -->
    <div class="fq-prox">
      <div class="fq-prox-icon">📷</div>
      <h3>Terminal QR — Próximamente</h3>
      <p>Escaneá el QR de tu carnet para fichar sin escribir nada.</p>
    </div>

  </main>

  <?=$footer?>

  <script>
    const ruta  = <?=json_encode($ruta ?? '')?>;
    let empleadoEncontrado = null;
    let timerInterval = null;

    const inputLegajo = document.getElementById('inputLegajo');
    const inputPin    = document.getElementById('inputPin');
    const preview     = document.getElementById('preview');
    const pinError    = document.getElementById('pinError');

    function showError(msg) {
      pinError.textContent = msg;
      pinError.classList.add('show');
      setTimeout(() => pinError.classList.remove('show'), 3500);
    }

    // Buscar empleado al salir del campo legajo
    inputLegajo.addEventListener('blur', buscarEmpleado);
    inputLegajo.addEventListener('keydown', e => { if (e.key === 'Enter') { inputPin.focus(); buscarEmpleado(); } });

    function buscarEmpleado() {
      const legajo = inputLegajo.value.trim();
      if (!legajo) { preview.style.display = 'none'; empleadoEncontrado = null; return; }

      fetch(ruta + '/terminal/buscar?legajo=' + encodeURIComponent(legajo))
        .then(r => r.json())
        .then(data => {
          if (data.ok) {
            empleadoEncontrado = data.empleado;
            document.getElementById('prevNombre').textContent = data.empleado.apellido + ' ' + data.empleado.nombre;
            document.getElementById('prevLegajo').textContent = 'Legajo: ' + data.empleado.legajo;
            document.getElementById('prevBadge').textContent  = 'EMP:' + data.empleado.id;
            document.getElementById('prevEstado').textContent = data.estado;
            // timer
            clearInterval(timerInterval);
            if (data.ultima_hora) {
              let base = new Date('1970-01-01T' + data.ultima_hora + 'Z').getTime();
              let ahora = Date.now();
              timerInterval = setInterval(() => {
                let diff = Math.floor((Date.now() - ahora) / 1000);
                let total = Math.floor(base / 1000) + diff;
                // simplificado: solo mostrar hora actual
                document.getElementById('prevTimer').textContent = new Date().toLocaleTimeString('es-AR', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
              }, 1000);
              document.getElementById('prevTimer').textContent = new Date().toLocaleTimeString('es-AR', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
            } else {
              document.getElementById('prevTimer').textContent = 'Sin fichadas hoy';
            }
            preview.style.display = 'flex';
          } else {
            empleadoEncontrado = null;
            preview.style.display = 'none';
          }
        })
        .catch(() => { empleadoEncontrado = null; });
    }

    function fichar(tipo) {
      const legajo = inputLegajo.value.trim();
      const pin    = inputPin.value.trim();

      if (!legajo) { showError('Ingresá el número de empleado.'); inputLegajo.focus(); return; }
      if (pin.length !== 4) { showError('El PIN debe tener 4 dígitos.'); inputPin.focus(); return; }

      // POST al servidor
      fetch(ruta + '/terminal/ficharpin', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'legajo=' + encodeURIComponent(legajo) + '&pin=' + encodeURIComponent(pin) + '&tipo=' + tipo
      })
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          // Mostrar flash verde y resetear
          const flash = document.createElement('div');
          flash.className = 'fq-alert alert-ok';
          flash.style.marginBottom = '16px';
          flash.innerHTML = data.msg;
          document.querySelector('main').insertBefore(flash, document.getElementById('panelFichar'));
          inputLegajo.value = '';
          inputPin.value    = '';
          preview.style.display = 'none';
          empleadoEncontrado = null;
          setTimeout(() => flash.remove(), 4000);
        } else {
          showError(data.msg || 'Error al fichar.');
        }
      })
      .catch(() => showError('Error de conexión.'));
    }

    // Enter en PIN → ficha entrada por defecto
    inputPin.addEventListener('keydown', e => {
      if (e.key === 'Enter') fichar('IN');
    });
  </script>
</body>
</html>