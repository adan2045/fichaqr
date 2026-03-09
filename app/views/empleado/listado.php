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
        <h2 style="margin:0;">Empleados</h2>
        <div class="muted" style="margin-top:4px; font-family:monospace; font-size:12px;"></div>
      </div>
      <div class="fq-actions">
        <a class="fq-btn" href="<?=$ruta?>/admin/gestion">Volver</a>
        <a class="fq-btn fq-btn-primary" href="<?=$ruta?>/empleado/formulario">+ Nuevo</a>
      </div>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="fq-alert <?=($flash['type'] ?? '') === 'ok' ? 'alert-ok' : 'alert-danger'?>">
        <?=htmlspecialchars($flash['msg'] ?? '')?>
      </div>
    <?php endif; ?>

    <section class="fq-card">
      <div class="fq-table-wrap">
        <table class="fq-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Legajo</th>
              <th>Apellido y nombre</th>
              <th>DNI</th>
              <th>Email</th>
              <th>Activo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($empleados)): ?>
              <tr><td colspan="7" class="muted">Sin empleados</td></tr>
            <?php else: ?>
              <?php foreach ($empleados as $e): ?>
                <tr>
                  <td><?=$e['id']?></td>
                  <td><?=htmlspecialchars($e['legajo'] ?? '')?></td>
                  <td><?=htmlspecialchars(($e['apellido'] ?? '').' '.($e['nombre'] ?? ''))?></td>
                  <td><?=htmlspecialchars($e['dni'] ?? '')?></td>
                  <td><?=htmlspecialchars($e['email'] ?? '')?></td>
                  <td><?=((int)($e['activo'] ?? 0) === 1) ? 'Sí' : 'No'?></td>
                  <td class="fq-actions">
                    <a class="fq-btn fq-btn-warn" href="<?=$ruta?>/empleado/modificar?id=<?=$e['id']?>">Editar</a>
                    <a class="fq-btn fq-btn-danger" href="<?=$ruta?>/empleado/eliminar?id=<?=$e['id']?>" onclick="return confirm('¿Desactivar empleado #<?=$e['id']?>?')">Desactivar</a>
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
