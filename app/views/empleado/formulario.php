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

      <form method="POST" action="<?=$ruta?>/empleado/guardar" class="form">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label">Legajo</label>
            <input class="input" name="legajo" placeholder="Ej: 1001">
          </div>
          <div>
            <label class="label">DNI</label>
            <input class="input" name="dni" placeholder="Ej: 12345678">
          </div>
          <div>
            <label class="label">Nombre *</label>
            <input class="input" name="nombre" required>
          </div>
          <div>
            <label class="label">Apellido *</label>
            <input class="input" name="apellido" required>
          </div>
          <div style="grid-column: 1 / -1;">
            <label class="label">Email</label>
            <input class="input" name="email" type="email" placeholder="nombre@empresa.com">
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
