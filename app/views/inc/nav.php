<?php
  if (session_status() === PHP_SESSION_NONE) session_start();
  $base = \App::baseUrl();
  $authed = isset($_SESSION['user_id']);
  $rol = strtolower(trim((string)($_SESSION['user_rol'] ?? '')));
  $esAdmin = in_array($rol, ['admin','jefe'], true);
?>

<header class="topbar">
  <div class="logo">
    <div class="logo-icon">✓</div>
    <div>
      <div class="logo-title">FichaQR</div>
      <div class="logo-sub">CONTROL · FICHADAS</div>
    </div>
  </div>

  <nav class="toplinks">
    <?php if ($authed): ?>
      <a class="toplink" href="<?=$base?>/terminal/index">Fichar</a>
    <?php endif; ?>

    <?php if ($authed && $esAdmin): ?>
      <a class="toplink" href="<?=$base?>/admin/gestion">Panel</a>
      <a class="toplink" href="<?=$base?>/empleado/listado">Empleados</a>
    <?php elseif ($authed): ?>
      <a class="toplink" href="<?=$base?>/fichada/mis">Mis fichadas</a>
    <?php endif; ?>

    <?php if ($authed): ?>
      <span class="chip"><?=htmlspecialchars($_SESSION['user_usuario'] ?? 'usuario')?></span>
      <a class="toplink danger" href="<?=$base?>/login/logout">Salir</a>
    <?php else: ?>
      <a class="toplink" href="<?=$base?>/login/login">Login</a>
    <?php endif; ?>
  </nav>
</header>