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

          <?php
            $rolSesion = strtolower(trim($_SESSION['user_rol'] ?? ''));
            $esAdmin   = $rolSesion === 'admin';
            $rolActual = $datos['rol'] ?? 'empleado';
          ?>
          <?php if ($esAdmin): ?>
          <!-- Solo admin puede asignar rol -->
          <div style="grid-column: 1 / -1;">
            <label class="label" style="margin-bottom:8px;">Rol</label>
            <div style="display:flex; gap:10px;" id="rol-opciones">

              <label id="lbl-empleado" style="
                display:flex; align-items:center; gap:10px; cursor:pointer;
                padding:11px 16px; border-radius:12px; flex:1;
                border:2px solid <?=$rolActual==='empleado'?'rgba(0,229,160,.5)':'rgba(255,255,255,.1)'?>;
                background:<?=$rolActual==='empleado'?'rgba(0,229,160,.06)':'rgba(255,255,255,.02)'?>;
                transition: border-color .15s, background .15s;">
                <input type="radio" name="rol" value="empleado"
                       <?=$rolActual==='empleado'?'checked':''?>
                       style="accent-color:#00e5a0;"
                       onchange="resaltarRol()">
                <div>
                  <div style="font-weight:700; font-size:13px; color:#00e5a0;">👷 Empleado</div>
                  <div style="font-size:11px; color:#7b7b9a; margin-top:1px;">Solo puede fichar y ver sus fichadas</div>
                </div>
              </label>

              <label id="lbl-jefe" style="
                display:flex; align-items:center; gap:10px; cursor:pointer;
                padding:11px 16px; border-radius:12px; flex:1;
                border:2px solid <?=$rolActual==='jefe'?'rgba(167,139,250,.5)':'rgba(255,255,255,.1)'?>;
                background:<?=$rolActual==='jefe'?'rgba(167,139,250,.06)':'rgba(255,255,255,.02)'?>;
                transition: border-color .15s, background .15s;">
                <input type="radio" name="rol" value="jefe"
                       <?=$rolActual==='jefe'?'checked':''?>
                       style="accent-color:#a78bfa;"
                       onchange="resaltarRol()">
                <div>
                  <div style="font-weight:700; font-size:13px; color:#a78bfa;">🧑‍💼 Jefe</div>
                  <div style="font-size:11px; color:#7b7b9a; margin-top:1px;">Puede gestionar fichadas y el panel admin</div>
                </div>
              </label>

            </div>
          </div>
          <?php else: ?>
          <!-- Jefe: siempre crea empleados, sin opción -->
          <input type="hidden" name="rol" value="empleado">
          <?php endif; ?>

        </div>

        <div style="height:10px"></div>

        <label class="label" style="display:flex; align-items:center; gap:8px;">
          <input type="checkbox" name="activo" <?=!empty($datos['activo'])?'checked':''?>>
          Activo
        </label>

        <div style="height:16px"></div>
        <button class="fq-btn fq-btn-primary" type="submit">Guardar</button>
      </form>
    </section>
  </main>

  <?=$footer?>

  <script>
  function resaltarRol() {
    const esJefe = document.querySelector('input[name="rol"][value="jefe"]')?.checked;
    const lEmp  = document.getElementById('lbl-empleado');
    const lJefe = document.getElementById('lbl-jefe');
    if (!lEmp || !lJefe) return;
    lEmp.style.borderColor  = esJefe ? 'rgba(255,255,255,.1)' : 'rgba(0,229,160,.5)';
    lEmp.style.background   = esJefe ? 'rgba(255,255,255,.02)' : 'rgba(0,229,160,.06)';
    lJefe.style.borderColor = esJefe ? 'rgba(167,139,250,.5)' : 'rgba(255,255,255,.1)';
    lJefe.style.background  = esJefe ? 'rgba(167,139,250,.06)' : 'rgba(255,255,255,.02)';
  }
  </script>
</body>
</html>