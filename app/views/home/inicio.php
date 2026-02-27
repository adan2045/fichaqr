<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>
</head>

<body>
  <?=$nav?>

  <main class="fq-container">
    <section class="fq-card" style="padding:22px;">
      <h2 style="margin-top:0;">Sistema de Fichadas · QR</h2>
      <p class="muted" style="margin-top:6px;">Básico, en PHP puro + HTML/CSS. Login, panel admin/jefe y terminal QR.</p>

      <div style="height:14px"></div>

      <div class="fq-actions">
        <a class="fq-btn fq-btn-primary" href="<?=$ruta?>/login/login">Entrar</a>
        <a class="fq-btn" href="<?=$ruta?>/terminal/index">Terminal QR</a>
        <a class="fq-btn" href="<?=$ruta?>/admin/gestion">Panel (admin/jefe)</a>
      </div>

      <div style="height:18px"></div>

      <div class="muted" style="font-family:monospace; font-size:12px; line-height:1.5;">
        <strong>QR esperado:</strong> EMP:&lt;id_empleado&gt; (ej: EMP:12).<br>
        Para comenzar: importá <code>fichaqr.sql</code>, creá un usuario admin y cargá empleados.
      </div>
    </section>
  </main>

  <?=$footer?>
</body>
</html>
