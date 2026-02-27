<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>
</head>

<body>
  <?=$nav?>

  <main class="fq-container">
    <div style="margin-bottom:14px;">
      <h2 style="margin:0;">Terminal QR</h2>
      <div class="muted" style="margin-top:4px; font-family:monospace; font-size:12px;">Escaneá el QR (ej: EMP:12) y registrá ENTRADA / SALIDA</div>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type'] ?? '') === 'ok' ? 'alert-ok' : 'alert-danger'?>">
        <?=htmlspecialchars($flash['msg'] ?? '')?>
      </div>
    <?php endif; ?>

    <section class="fq-card" style="margin-bottom:16px;">
      <form method="GET" class="form" style="display:flex; gap:10px; flex-wrap:wrap; align-items:end;">
        <div style="flex:1; min-width:240px;">
          <label class="label">Código</label>
          <input class="input" name="codigo" value="<?=htmlspecialchars($codigo ?? '')?>" placeholder="Escanear/pegar aquí" autofocus>
        </div>
        <div>
          <button class="fq-btn fq-btn-primary" type="submit">Buscar</button>
        </div>
      </form>
    </section>

    <?php if (!empty($codigo) && empty($empleado)): ?>
      <div class="fq-alert fq-alert-danger">Código inválido o empleado no encontrado.</div>
    <?php endif; ?>

    <?php if (!empty($empleado)): ?>
      <section class="fq-card" style="margin-bottom:16px;">
        <div class="fq-actions" style="justify-content: space-between; align-items:center;">
          <div>
            <h3 style="margin:0;"><?=htmlspecialchars(($empleado['apellido'] ?? '').' '.($empleado['nombre'] ?? ''))?></h3>
            <div class="muted" style="margin-top:4px; font-family:monospace; font-size:12px;">Legajo: <?=htmlspecialchars($empleado['legajo'] ?? '-')?> · ID: <?=htmlspecialchars($empleado['id'])?></div>
          </div>
          <span class="chip">EMP:<?=htmlspecialchars($empleado['id'])?></span>
        </div>

        <div style="height:12px"></div>

        <div class="fq-actions">
          <form method="POST" action="<?=$ruta?>/fichada/registrarqr" style="display:inline;">
            <input type="hidden" name="empleado_id" value="<?=htmlspecialchars($empleado['id'])?>">
            <input type="hidden" name="tipo" value="IN">
            <button class="fq-btn fq-btn-primary" type="submit">ENTRADA (IN)</button>
          </form>

          <form method="POST" action="<?=$ruta?>/fichada/registrarqr" style="display:inline;">
            <input type="hidden" name="empleado_id" value="<?=htmlspecialchars($empleado['id'])?>">
            <input type="hidden" name="tipo" value="OUT">
            <button class="fq-btn fq-btn-warn" type="submit">SALIDA (OUT)</button>
          </form>
        </div>

        <div style="height:10px"></div>
        <form method="POST" action="<?=$ruta?>/fichada/registrarqr" class="form" style="display:flex; gap:10px; flex-wrap:wrap; align-items:end;">
          <input type="hidden" name="empleado_id" value="<?=htmlspecialchars($empleado['id'])?>">
          <div style="flex:1; min-width:240px;">
            <label class="label">Comentario (opcional)</label>
            <input class="input" name="comentario" placeholder="Ej: Llegó tarde / Salida a almorzar">
          </div>
          <div>
            <label class="label">Tipo</label>
            <select class="input" name="tipo" style="min-width:140px;">
              <option value="IN">IN</option>
              <option value="OUT">OUT</option>
            </select>
          </div>
          <div>
            <button class="fq-btn" type="submit">Fichar con comentario</button>
          </div>
        </form>
      </section>

      <section class="fq-card">
        <div class="muted" style="font-family:monospace; font-size:12px; margin-bottom:10px;">Fichadas de hoy</div>
        <div class="fq-table-wrap">
          <table class="fq-table" style="min-width:620px;">
            <thead>
              <tr>
                <th>Fecha/Hora</th>
                <th>Tipo</th>
                <th>Origen</th>
                <th>Comentario</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($ultimas)): ?>
                <tr><td colspan="4" class="muted">Sin fichadas hoy</td></tr>
              <?php else: ?>
                <?php foreach ($ultimas as $f): ?>
                  <tr>
                    <td><?=htmlspecialchars($f['fecha_hora'])?></td>
                    <td>
                      <?php if (($f['tipo'] ?? '') === 'IN'): ?>
                        <span class="fq-badge fq-badge-in">IN</span>
                      <?php else: ?>
                        <span class="fq-badge fq-badge-out">OUT</span>
                      <?php endif; ?>
                    </td>
                    <td><?=htmlspecialchars($f['origen'] ?? '')?></td>
                    <td><?=htmlspecialchars($f['comentario'] ?? '')?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>
  </main>

  <?=$footer?>

  <script>
    // UX: si se escanea con lector, al finalizar suele mandar Enter.
    // Con esto, cuando el input tiene valor y se presiona Enter, se envía el form.
    const input = document.querySelector('input[name="codigo"]');
    if (input) {
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          // Dejar que el form haga submit
        }
      });
    }
  </script>
</body>
</html>
