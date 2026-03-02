<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title>FichaQR | Login</title>

  <style>
    /* LOGIN FICHAQR — SCOPEADO: todo bajo .login-fq */
    .login-fq{
      --border:rgba(255,255,255,.10);
      --text:rgba(255,255,255,.92);
      --muted:rgba(255,255,255,.62);
      --accent:#00e5a0;
      --accent2:#7c3aed;
      --danger:#ff3b3b;
      --shadow:0 18px 55px rgba(0,0,0,.45);
      padding:28px 14px 46px;
      color:var(--text);
    }
    .login-fq__container{max-width:980px;margin:0 auto;}
    .login-fq__wrap{
      border:1px solid var(--border);
      background:
        radial-gradient(1200px 600px at 25% 10%, rgba(124,58,237,.18), transparent 60%),
        radial-gradient(900px 500px at 80% 30%, rgba(0,229,160,.14), transparent 60%),
        linear-gradient(180deg, rgba(18,18,28,.85), rgba(10,10,16,.75));
      border-radius:22px;
      box-shadow:var(--shadow);
      padding:18px;
      overflow:hidden;
    }
    .login-fq__grid{display:grid;grid-template-columns:1.05fr .95fr;gap:14px;align-items:start;}
    .login-fq__kicker{
      font-family:ui-monospace,Menlo,Consolas,"Liberation Mono","Courier New",monospace;
      font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--accent);
      margin:6px 0 8px;
    }
    .login-fq__title{margin:0;font-size:30px;line-height:1.10;}
    .login-fq__lead{margin:10px 0 0;color:var(--muted);line-height:1.6;font-size:14px;max-width:70ch;}

    .login-fq__card{
      border:1px solid rgba(255,255,255,.10);
      background:rgba(10,10,16,.35);
      border-radius:18px;
      padding:16px;
    }
    .login-fq__cardTitle{margin:0;font-size:18px;}
    .login-fq__cardSub{margin:6px 0 14px;color:var(--muted);font-size:13px;line-height:1.5;}

    .login-fq__alert{
      border:1px solid rgba(255,59,59,.35);
      background:rgba(255,59,59,.10);
      padding:10px 12px;border-radius:14px;margin-bottom:12px;
      font-size:13px;line-height:1.45;
    }

    .login-fq__form{display:grid;gap:10px;}
    .login-fq__label{
      font-family:ui-monospace,Menlo,Consolas,"Liberation Mono","Courier New",monospace;
      font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,.78);
    }
    .login-fq__input{
      width:100%;box-sizing:border-box;
      border:1px solid rgba(255,255,255,.12);
      background:rgba(12,12,18,.55);
      color:rgba(255,255,255,.92);
      padding:11px 12px;border-radius:14px;outline:none;font-size:14px;
    }
    .login-fq__input:focus{
      border-color:rgba(0,229,160,.40);
      box-shadow:0 0 0 4px rgba(0,229,160,.10);
      background:rgba(12,12,18,.70);
    }

    .login-fq__actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;align-items:center;}
    .login-fq__btn{
      display:inline-flex;align-items:center;justify-content:center;
      padding:11px 14px;border-radius:14px;border:1px solid var(--border);
      background:rgba(12,12,18,.45);color:rgba(255,255,255,.92);
      text-decoration:none;font-weight:900;font-size:13px;cursor:pointer;
    }
    .login-fq__btn--primary{
      border-color:rgba(0,229,160,.35);
      background:linear-gradient(135deg, rgba(0,229,160,.18), rgba(124,58,237,.12));
    }

    @media (max-width:920px){
      .login-fq__grid{grid-template-columns:1fr;}
      .login-fq__title{font-size:28px;}
    }
  </style>
</head>

<body>
  <?=$nav?>

  <main class="login-fq">
    <div class="login-fq__container">
      <section class="login-fq__wrap">
        <div class="login-fq__grid">

          <div>
            <div class="login-fq__kicker">ACCESO · FICHAQR</div>
            <h1 class="login-fq__title">Entrá a tu panel de fichadas</h1>
            <p class="login-fq__lead">
              Ingresá con tu cuenta. Admin/jefe gestiona empleados y reportes.
              Empleados pueden consultar sus fichadas (según rol).
            </p>
          </div>

          <div class="login-fq__card">
            <h2 class="login-fq__cardTitle">Iniciar sesión</h2>
            <p class="login-fq__cardSub">Ingresá correo y contraseña.</p>

            <?php if (!empty($general_error)): ?>
              <div class="login-fq__alert"><?=htmlspecialchars($general_error)?></div>
            <?php endif; ?>

            <form class="login-fq__form" method="POST">
              <label class="login-fq__label">Correo</label>
              <input class="login-fq__input" type="email" name="mail" value="<?=htmlspecialchars($mail ?? '')?>" required>

              <label class="login-fq__label" style="margin-top:6px;">Contraseña</label>
              <input class="login-fq__input" type="password" name="password" required>

              <div class="login-fq__actions">
                <button class="login-fq__btn login-fq__btn--primary" type="submit" name="ingreso">Entrar</button>
                <a class="login-fq__btn" href="<?=$ruta?>/terminal/index">Terminal QR</a>
              </div>
            </form>

          </div>

        </div>
      </section>
    </div>
  </main>

  <?=$footer?>
</body>
</html>