<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>
</head>

<body>
  <?=$nav?>

  <main class="fq-container">
    <section class="fq-card" style="max-width:520px;margin:40px auto;">
      <h2 style="margin-bottom:6px;">Ingresar</h2>
      <p class="muted" style="margin-bottom:18px;">Acceso a FichaQR (admin/jefe y empleados)</p>

      <?php if (!empty($general_error)): ?>
        <div class="fq-alert fq-alert-danger"><?=htmlspecialchars($general_error)?></div>
      <?php endif; ?>

      <form method="POST" class="form">
        <label class="label">Usuario</label>
        <input class="input" type="text" name="usuario" autocomplete="username" value="<?=htmlspecialchars($usuario ?? '')?>" required>
        <?php if (!empty($error_usuario)): ?>
          <div class="field-error"><?=htmlspecialchars($error_usuario)?></div>
        <?php endif; ?>

        <div style="height:10px"></div>

        <label class="label">Contraseña</label>
        <input class="input" type="password" name="password" autocomplete="current-password" required>
        <?php if (!empty($error_pass)): ?>
          <div class="field-error"><?=htmlspecialchars($error_pass)?></div>
        <?php endif; ?>

        <div style="height:16px"></div>

        <button class="fq-btn fq-btn-primary" type="submit">Entrar</button>
      </form>

      <p class="muted" style="margin-top:14px;font-size:12px;">
        Si es tu primera vez: importá la DB y creá el usuario admin.
      </p>
    </section>
  </main>

  <?=$footer?>
</body>
</html>
