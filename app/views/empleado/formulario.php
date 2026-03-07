<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>
</head>

<body>
  <?=$nav?>

  <main class="fq-container">
    <section class="fq-card" style="max-width:760px;margin:0 auto;">
      <div class="fq-actions" style="justify-content: space-between; align-items:center;">
        <h2 style="margin:0;">Nuevo empleado</h2>
        <a class="fq-btn" href="<?=$ruta?>/empleado/listado">Volver</a>
      </div>

      <div style="height:12px"></div>

      <?php if (!empty($errores['db'])): ?>
        <div class="fq-alert fq-alert-danger" style="margin-bottom:12px;"><?=$errores['db']?></div>
      <?php endif; ?>

      <form method="POST" action="<?=$ruta?>/empleado/formulario" class="form">
        <input type="hidden" name="legajo" value="">

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label">Nombre *</label>
            <input class="input" name="nombre" required value="<?=htmlspecialchars($datos['nombre'] ?? '')?>">
            <?php if (!empty($errores['nombre'])): ?><small style="color:var(--fq-danger)"><?=$errores['nombre']?></small><?php endif; ?>
          </div>
          <div>
            <label class="label">Apellido *</label>
            <input class="input" name="apellido" required value="<?=htmlspecialchars($datos['apellido'] ?? '')?>">
            <?php if (!empty($errores['apellido'])): ?><small style="color:var(--fq-danger)"><?=$errores['apellido']?></small><?php endif; ?>
          </div>
          <div>
            <label class="label">DNI * <small style="color:var(--fq-muted)">(será la contraseña inicial)</small></label>
            <input class="input" name="dni" placeholder="Ej: 12345678" required value="<?=htmlspecialchars($datos['dni'] ?? '')?>">
            <?php if (!empty($errores['dni'])): ?><small style="color:var(--fq-danger)"><?=$errores['dni']?></small><?php endif; ?>
          </div>
          <div>
            <label class="label">Email <small style="color:var(--fq-muted)">(será el usuario de acceso)</small></label>
            <input class="input" name="email" type="email" placeholder="nombre@empresa.com" value="<?=htmlspecialchars($datos['email'] ?? '')?>">
          </div>
        </div>

        <div style="height:10px"></div>

        <label class="label" style="display:flex; align-items:center; gap:8px;">
          <input type="checkbox" name="activo" checked>
          Activo
        </label>

        <div style="height:16px"></div>
        <button class="fq-btn fq-btn-primary" type="submit">Guardar</button>
      </form>
    </section>
  </main>

  <?=$footer?>
</body>
</html>