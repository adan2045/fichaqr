<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>
  <style>
    /* ── MODAL ── */
    .fq-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.65);
      backdrop-filter: blur(4px);
      z-index: 100;
      place-items: center;
      padding: 16px;
    }
    .fq-overlay.open { display: grid; }
    .fq-modal {
      background: #13131f;
      border: 1px solid #1e1e2e;
      border-radius: 18px;
      padding: 24px;
      width: 100%;
      max-width: 480px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 24px 60px rgba(0,0,0,.6);
    }
    .fq-modal__title {
      font-size: 16px;
      font-weight: 700;
      margin: 0 0 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .fq-modal__close {
      cursor: pointer;
      background: rgba(255,255,255,.07);
      border: 1px solid #1e1e2e;
      color: #e8e8f0;
      border-radius: 8px;
      padding: 4px 10px;
      font-size: 13px;
    }
    /* ── TABLA badge manual ── */
    .fq-badge-admin { border-color: rgba(124,58,237,.45); color: #a78bfa; }
    /* ── SEPARADOR EMPLEADO ── */
    .fq-row-sep td {
      background: rgba(124,58,237,.07);
      border-top: 2px solid rgba(124,58,237,.25) !important;
      font-size: 12px;
      font-family: monospace;
      color: #a78bfa;
      padding: 6px 10px !important;
    }
    /* ── DOCS button ── */
    .fq-btn-docs { border-color: rgba(0,229,160,.25); color: #00e5a0; font-size: 11px; }
    .fq-btn-docs:hover { background: rgba(0,229,160,.08); }
    /* ── Quick stats ── */
    .fq-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 10px;
      margin-bottom: 16px;
    }
    .fq-stat {
      background: rgba(20,20,32,.88);
      border: 1px solid #1e1e2e;
      border-radius: 14px;
      padding: 14px 16px;
    }
    .fq-stat__num { font-size: 22px; font-weight: 800; }
    .fq-stat__lbl { font-family: monospace; font-size: 11px; color: #7b7b9a; margin-top: 2px; }
  </style>
</head>

<body>
  <?=$nav?>

  <main class="fq-container">

    <!-- HEADER -->
    <div class="fq-actions" style="justify-content:space-between; align-items:center; margin-bottom:14px;">
      <div>
        <h2 style="margin:0;">Panel de fichadas</h2>
        <div class="muted" style="margin-top:4px; font-family:monospace; font-size:12px;">Admin / Jefe</div>
      </div>
      <div class="fq-actions">
        <a class="fq-btn fq-btn-docs" href="<?=$ruta?>/empleado/listado">👥 Empleados</a>
        <a class="fq-btn" href="<?=$ruta?>/terminal/index">Terminal QR</a>
        <button class="fq-btn fq-btn-primary" onclick="abrirNueva()">+ Nueva fichada</button>
      </div>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type'] ?? '') === 'ok' ? 'fq-alert-ok' : 'fq-alert-danger'?>" style="margin-bottom:14px;">
        <?=htmlspecialchars($flash['msg'] ?? '')?>
      </div>
    <?php endif; ?>

    <!-- FILTROS -->
    <section class="fq-card" style="margin-bottom:16px;">
      <form method="GET" class="form">
        <div style="display:grid; gap:10px; grid-template-columns: 1fr 1fr 1.5fr auto; align-items:end;">
          <div>
            <label class="label">Desde</label>
            <input class="input" type="date" name="desde" value="<?=htmlspecialchars($desde)?>">
          </div>
          <div>
            <label class="label">Hasta</label>
            <input class="input" type="date" name="hasta" value="<?=htmlspecialchars($hasta)?>">
          </div>
          <div>
            <label class="label">Empleado</label>
            <select class="input" name="empleado_id">
              <option value="">Todos</option>
              <?php foreach (($empleados ?? []) as $e): ?>
                <option value="<?=$e['id']?>" <?=($empleadoId === (int)$e['id']) ? 'selected' : ''?>>
                  <?=htmlspecialchars(($e['apellido'] ?? '').' '.($e['nombre'] ?? '').' · '.($e['legajo'] ?? ''))?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <button class="fq-btn fq-btn-primary" type="submit">Filtrar</button>
          </div>
        </div>
      </form>
    </section>

    <!-- STATS RÁPIDAS -->
    <?php
      $totalFichadas = count($fichadas ?? []);
      $totalIN  = count(array_filter($fichadas ?? [], fn($f) => $f['tipo']==='IN'));
      $totalOUT = count(array_filter($fichadas ?? [], fn($f) => $f['tipo']==='OUT'));
      $empleadosUnicos = count(array_unique(array_column($fichadas ?? [], 'empleado_id')));
    ?>
    <div class="fq-stats">
      <div class="fq-stat">
        <div class="fq-stat__num"><?=$totalFichadas?></div>
        <div class="fq-stat__lbl">TOTAL</div>
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

    <!-- TABLA -->
    <section class="fq-card">
      <div class="muted" style="font-family:monospace; font-size:12px; margin-bottom:10px;">
        <?=$totalFichadas?> fichadas · <?=htmlspecialchars($desde)?> → <?=htmlspecialchars($hasta)?>
      </div>

      <div class="fq-table-wrap">
        <table class="fq-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Fecha/Hora</th>
              <th>Empleado</th>
              <th>Tipo</th>
              <th>Origen</th>
              <th>Comentario</th>
              <th>Docs</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($fichadas)): ?>
              <tr><td colspan="8" class="muted" style="text-align:center; padding:20px;">Sin resultados en el período</td></tr>
            <?php else:
              $lastEmp = null;
              foreach ($fichadas as $f):
                $empKey = $f['empleado_id'] ?? 0;
                if ($empKey !== $lastEmp):
                  $lastEmp = $empKey; ?>
                  <tr class="fq-row-sep">
                    <td colspan="8">
                      👤 <?=htmlspecialchars(($f['apellido'] ?? '').' '.($f['nombre'] ?? ''))?>
                      &nbsp;·&nbsp; Legajo: <?=htmlspecialchars($f['legajo'] ?? '-')?>
                      &nbsp;
                      <a href="<?=$ruta?>/empleado/docs?id=<?=$f['empleado_id']?>" class="fq-btn fq-btn-docs" style="padding:3px 10px; font-size:10px;">📁 Documentos</a>
                    </td>
                  </tr>
                <?php endif; ?>
                <tr>
                  <td class="muted" style="font-family:monospace;"><?=$f['id']?></td>
                  <td><?=htmlspecialchars($f['fecha_hora'])?></td>
                  <td style="font-size:12px;"><?=htmlspecialchars(($f['apellido'] ?? '').' '.($f['nombre'] ?? ''))?></td>
                  <td>
                    <?php if ($f['tipo'] === 'IN'): ?>
                      <span class="fq-badge fq-badge-in">▶ IN</span>
                    <?php else: ?>
                      <span class="fq-badge fq-badge-out">◀ OUT</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (($f['origen'] ?? '') === 'admin'): ?>
                      <span class="fq-badge fq-badge-admin">admin</span>
                    <?php else: ?>
                      <span class="muted" style="font-size:12px;"><?=htmlspecialchars($f['origen'] ?? '')?></span>
                    <?php endif; ?>
                  </td>
                  <td style="font-size:12px; max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    <?=htmlspecialchars($f['comentario'] ?? '—')?>
                  </td>
                  <td>
                    <a href="<?=$ruta?>/empleado/docs?id=<?=$f['empleado_id']?>" class="fq-btn fq-btn-docs" style="padding:5px 8px;">📁</a>
                  </td>
                  <td class="fq-actions">
                    <button class="fq-btn fq-btn-warn"
                      onclick="abrirEditar(<?=$f['id']?>, '<?=htmlspecialchars($f['fecha_hora'])?>', '<?=$f['tipo']?>', '<?=htmlspecialchars(addslashes($f['comentario'] ?? ''))?>', '<?=htmlspecialchars(($f['apellido'] ?? '').' '.($f['nombre'] ?? ''))?>')">
                      Editar
                    </button>
                    <button class="fq-btn fq-btn-danger"
                      onclick="confirmarEliminar(<?=$f['id']?>, '<?=htmlspecialchars(addslashes(($f['apellido'] ?? '').' '.($f['nombre'] ?? '')))?>', '<?=htmlspecialchars($f['fecha_hora'])?>') ">
                      Eliminar
                    </button>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <!-- MODAL EDITAR / NUEVA -->
  <div class="fq-overlay" id="fq-modal-overlay" onclick="cerrarModal(event)">
    <div class="fq-modal" id="fq-modal">
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
            <?php foreach (($empleados ?? []) as $e): ?>
              <option value="<?=$e['id']?>">
                <?=htmlspecialchars(($e['apellido'] ?? '').' '.($e['nombre'] ?? '').' · '.($e['legajo'] ?? ''))?>
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
              <span class="fq-badge fq-badge-in" style="cursor:pointer;">▶ IN</span>
            </label>
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
              <input type="radio" name="tipo" value="OUT" id="modal-tipo-out" style="accent-color:#ffa502;">
              <span class="fq-badge fq-badge-out" style="cursor:pointer;">◀ OUT</span>
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
          <button class="fq-btn" type="button" onclick="document.getElementById('fq-modal-del').classList.remove('open')">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <?=$footer?>

  <script>
  const RUTA = '<?=$ruta?>';
  const overlay = document.getElementById('fq-modal-overlay');
  const delOverlay = document.getElementById('fq-modal-del');

  function abrirEditar(id, fechaHora, tipo, comentario, empleadoNombre) {
    document.getElementById('modal-titulo').textContent = 'Editar fichada #' + id;
    document.getElementById('modal-action').value = 'actualizar';
    document.getElementById('modal-id').value = id;
    document.getElementById('modal-emp-row').style.display = 'none';

    // Convertir "2026-03-04 18:49:00" → "2026-03-04T18:49"
    const dt = fechaHora.replace(' ', 'T').slice(0, 16);
    document.getElementById('modal-fecha').value = dt;
    document.getElementById('modal-tipo-in').checked  = tipo === 'IN';
    document.getElementById('modal-tipo-out').checked = tipo === 'OUT';
    document.getElementById('modal-comentario').value = comentario;
    document.getElementById('modal-submit-btn').textContent = 'Guardar cambios';
    document.getElementById('fq-modal-form').action = RUTA + '/fichada/actualizar';
    overlay.classList.add('open');
  }

  function abrirNueva() {
    document.getElementById('modal-titulo').textContent = 'Nueva fichada manual';
    document.getElementById('modal-action').value = 'nueva';
    document.getElementById('modal-id').value = '';
    document.getElementById('modal-emp-row').style.display = 'block';
    document.getElementById('modal-empleado').value = '';
    const now = new Date();
    const pad = n => String(n).padStart(2,'0');
    document.getElementById('modal-fecha').value =
      now.getFullYear()+'-'+pad(now.getMonth()+1)+'-'+pad(now.getDate())+'T'+pad(now.getHours())+':'+pad(now.getMinutes());
    document.getElementById('modal-tipo-in').checked = true;
    document.getElementById('modal-tipo-out').checked = false;
    document.getElementById('modal-comentario').value = '';
    document.getElementById('modal-submit-btn').textContent = 'Registrar';
    document.getElementById('fq-modal-form').action = RUTA + '/fichada/nueva';
    overlay.classList.add('open');
  }

  function confirmarEliminar(id, nombre, fechaHora) {
    document.getElementById('del-id').value = id;
    document.getElementById('del-msg').textContent =
      '¿Eliminar fichada #' + id + ' de ' + nombre + ' (' + fechaHora + ')?';
    document.getElementById('fq-del-form').action = RUTA + '/fichada/eliminar';
    delOverlay.classList.add('open');
  }

  function cerrarModal(e) {
    if (e.target === overlay) overlay.classList.remove('open');
  }
  function cerrarModalDirecto() { overlay.classList.remove('open'); }
  function cerrarDel(e) {
    if (e.target === delOverlay) delOverlay.classList.remove('open');
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      overlay.classList.remove('open');
      delOverlay.classList.remove('open');
    }
  });
  </script>
</body>
</html>
