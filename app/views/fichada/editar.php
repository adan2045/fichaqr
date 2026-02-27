<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>
</head>

<body>
  <?=$nav?>

  <main class="fq-container">
    <section class="fq-card" style="max-width:820px;margin:0 auto;">
      <div class="fq-actions" style="justify-content: space-between; align-items:center;">
        <div>
          <h2 style="margin:0;">Editar fichada #<?=htmlspecialchars($fichada['id'])?></h2>
          <div class="muted" style="margin-top:4px; font-family:monospace; font-size:12px;">
            <?=htmlspecialchars(($fichada['apellido'] ?? '').' '.($fichada['nombre'] ?? '').' · '.($fichada['legajo'] ?? ''))?>
          </div>
        </div>
        <a class="fq-btn" href="<?=$ruta?>/admin/gestion">Volver</a>
      </div>

      <div style="height:12px"></div>

      <form method="POST" action="<?=$ruta?>/fichada/actualizar" class="form">
        <input type="hidden" name="id" value="<?=htmlspecialchars($fichada['id'])?>">

        <label class="label">Fecha y hora</label>
        <input class="input" name="fecha_hora" value="<?=htmlspecialchars($fichada['fecha_hora'])?>" placeholder="YYYY-MM-DD HH:MM:SS" required>

        <div style="height:10px"></div>

        <label class="label">Tipo</label>
        <select class="input" name="tipo">
          <option value="IN" <?=($fichada['tipo'] === 'IN') ? 'selected' : ''?>>IN (Entrada)</option>
          <option value="OUT" <?=($fichada['tipo'] === 'OUT') ? 'selected' : ''?>>OUT (Salida)</option>
        </select>

        <div style="height:10px"></div>

        <label class="label">Comentario</label>
        <input class="input" name="comentario" value="<?=htmlspecialchars($fichada['comentario'] ?? '')?>" placeholder="Motivo / observación">

        <div style="height:16px"></div>
        <button class="fq-btn fq-btn-primary" type="submit">Guardar cambios</button>
      </form>
    </section>
  </main>

  <?=$footer?>
</body>
</html>
