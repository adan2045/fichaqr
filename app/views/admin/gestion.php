<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>
</head>

<body>
  <?=$nav?>

  <main class="fq-container">
    <div class="fq-actions" style="justify-content: space-between; align-items:center; margin-bottom:14px;">
      <div>
        <h2 style="margin:0;">Panel de fichadas</h2>
        <div class="muted" style="margin-top:4px; font-family:monospace; font-size:12px;">Admin/Jefe</div>
      </div>
      <div class="fq-actions">
        <a class="fq-btn" href="<?=$ruta?>/terminal/index">Terminal QR</a>
        <a class="fq-btn fq-btn-primary" href="<?=$ruta?>/empleado/formulario">+ Empleado</a>
      </div>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type'] ?? '') === 'ok' ? 'alert-ok' : 'alert-danger'?>">
        <?=htmlspecialchars($flash['msg'] ?? '')?>
      </div>
    <?php endif; ?>

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

    <section class="fq-card">
      <div class="muted" style="font-family:monospace; font-size:12px; margin-bottom:10px;">
        Mostrando: <?=count($fichadas ?? [])?> fichadas
      </div>

      <div class="fq-table-wrap">
        <table class="fq-table">
          <thead>
            <tr>
              <th>Fecha/Hora</th>
              <th>Empleado</th>
              <th>Tipo</th>
              <th>Origen</th>
              <th>Comentario</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($fichadas)): ?>
              <tr>
                <td colspan="6" class="muted">Sin resultados</td>
              </tr>
            <?php else: ?>
              <?php foreach ($fichadas as $f): ?>
                <tr>
                  <td><?=htmlspecialchars($f['fecha_hora'])?></td>
                  <td><?=htmlspecialchars(($f['apellido'] ?? '').' '.($f['nombre'] ?? '').' · '.($f['legajo'] ?? ''))?></td>
                  <td>
                    <?php if (($f['tipo'] ?? '') === 'IN'): ?>
                      <span class="fq-badge fq-badge-in">IN</span>
                    <?php else: ?>
                      <span class="fq-badge fq-badge-out">OUT</span>
                    <?php endif; ?>
                  </td>
                  <td><?=htmlspecialchars($f['origen'] ?? '')?></td>
                  <td><?=htmlspecialchars($f['comentario'] ?? '')?></td>
                  <td class="fq-actions">
                    <a class="fq-btn fq-btn-warn" href="<?=$ruta?>/fichada/editar?id=<?=$f['id']?>">Editar</a>
                    <a class="fq-btn fq-btn-danger" href="<?=$ruta?>/fichada/eliminar?id=<?=$f['id']?>" onclick="return confirm('¿Eliminar fichada #<?=$f['id']?>?')">Eliminar</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <?=$footer?>
</body>
</html>
