<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title>FichaQR | Login</title>
  <style>
    .login-fq {
      --border: rgba(255,255,255,.10);
      --text: rgba(255,255,255,.92);
      --muted: rgba(255,255,255,.62);
      --accent: #00e5a0;
      --shadow: 0 18px 55px rgba(0,0,0,.45);
      min-height: calc(100vh - 70px);
      display: grid;
      place-items: center;
      padding: 22px 14px;
      color: var(--text);
    }
    .login-fq__container { width: 100%; max-width: 480px; margin: 0 auto; }
    .login-fq__card {
      border: 1px solid var(--border);
      background:
        radial-gradient(900px 500px at 20% 0%, rgba(124,58,237,.16), transparent 60%),
        radial-gradient(700px 450px at 85% 30%, rgba(0,229,160,.12), transparent 60%),
        rgba(10,10,16,.35);
      border-radius: 18px;
      box-shadow: var(--shadow);
      padding: 28px 24px 22px;
    }
    .login-fq__kicker {
      font-family: monospace;
      font-size: 11px;
      letter-spacing: .16em;
      text-transform: uppercase;
      color: var(--accent);
      margin: 0 0 8px;
    }
    .login-fq__title { margin: 0 0 4px; font-size: 22px; }
    .login-fq__sub { margin: 0 0 18px; color: var(--muted); font-size: 13px; line-height: 1.55; }

    .login-fq__field { display: grid; gap: 5px; margin-bottom: 12px; }
    .login-fq__label {
      font-family: monospace;
      font-size: 11px;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: rgba(255,255,255,.7);
    }
    .login-fq__input {
      width: 100%;
      box-sizing: border-box;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(12,12,18,.55);
      color: rgba(255,255,255,.92);
      padding: 12px 13px;
      border-radius: 12px;
      outline: none;
      font-size: 14px;
      transition: border-color .2s, box-shadow .2s;
    }
    .login-fq__input:focus {
      border-color: rgba(0,229,160,.45);
      box-shadow: 0 0 0 4px rgba(0,229,160,.10);
      background: rgba(12,12,18,.75);
    }
    .login-fq__input.has-error {
      border-color: rgba(255,71,87,.50);
      box-shadow: 0 0 0 3px rgba(255,71,87,.10);
    }
    .login-fq__field-error {
      color: #ff8893;
      font-size: 11px;
      font-family: monospace;
      min-height: 16px;
    }
    .login-fq__alert {
      border: 1px solid rgba(255,59,59,.35);
      background: rgba(255,59,59,.10);
      padding: 10px 12px;
      border-radius: 12px;
      margin-bottom: 14px;
      font-size: 13px;
      line-height: 1.45;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .login-fq__actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 16px;
      align-items: center;
    }
    .login-fq__btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 12px 18px;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(12,12,18,.45);
      color: rgba(255,255,255,.92);
      text-decoration: none;
      font-weight: 700;
      font-size: 13px;
      cursor: pointer;
      transition: border-color .2s, background .2s;
    }
    .login-fq__btn--primary {
      border-color: rgba(0,229,160,.40);
      background: linear-gradient(135deg, rgba(0,229,160,.22), rgba(124,58,237,.16));
    }
    .login-fq__btn--primary:hover { background: linear-gradient(135deg, rgba(0,229,160,.32), rgba(124,58,237,.25)); }
    .login-fq__btn:hover { border-color: rgba(0,229,160,.35); }
    .login-fq__helper { margin-top: 14px; color: var(--muted); font-size: 11px; font-family: monospace; line-height: 1.6; }

    /* Indicador de fuerza de contraseña */
    .pwd-meter { display: flex; gap: 4px; margin-top: 5px; }
    .pwd-meter__bar {
      height: 3px;
      flex: 1;
      border-radius: 2px;
      background: rgba(255,255,255,.1);
      transition: background .25s;
    }
    .pwd-meter__bar.weak   { background: #ff4757; }
    .pwd-meter__bar.medium { background: #ffa502; }
    .pwd-meter__bar.strong { background: #00e5a0; }
    .pwd-meter__label { font-family: monospace; font-size: 10px; color: var(--muted); margin-top: 3px; }
  </style>
</head>

<body>
  <?=$nav?>

  <main class="login-fq">
    <div class="login-fq__container">
      <div class="login-fq__card">

        <div class="login-fq__kicker">ACCESO · FICHAQR</div>
        <h1 class="login-fq__title">Iniciar sesión</h1>
        <p class="login-fq__sub">Ingresá usuario y contraseña para acceder al sistema.</p>

        <?php if (!empty($general_error)): ?>
          <div class="login-fq__alert">⚠ <?=htmlspecialchars($general_error)?></div>
        <?php endif; ?>

        <form id="fq-login-form" method="POST" novalidate>

          <div class="login-fq__field">
            <label class="login-fq__label" for="fq-usuario">Usuario</label>
            <input
              id="fq-usuario"
              class="login-fq__input <?=!empty($error_usuario) ? 'has-error' : ''?>"
              type="text"
              name="usuario"
              value="<?=htmlspecialchars($usuario ?? '')?>"
              autocomplete="username"
              minlength="3"
              maxlength="60"
              placeholder="Tu nombre de usuario"
            >
            <span class="login-fq__field-error" id="fq-err-usuario">
              <?=htmlspecialchars($error_usuario ?? '')?>
            </span>
          </div>

          <div class="login-fq__field">
            <label class="login-fq__label" for="fq-password">Contraseña</label>
            <input
              id="fq-password"
              class="login-fq__input <?=!empty($error_pass) ? 'has-error' : ''?>"
              type="password"
              name="password"
              autocomplete="current-password"
              minlength="3"
              maxlength="50"
              placeholder="••••••••"
            >
            <div class="pwd-meter" id="fq-pwd-meter">
              <div class="pwd-meter__bar" id="pm1"></div>
              <div class="pwd-meter__bar" id="pm2"></div>
              <div class="pwd-meter__bar" id="pm3"></div>
              <div class="pwd-meter__bar" id="pm4"></div>
            </div>
            <div class="pwd-meter__label" id="fq-pwd-label"></div>
            <span class="login-fq__field-error" id="fq-err-pass">
              <?=htmlspecialchars($error_pass ?? '')?>
            </span>
          </div>

          <div class="login-fq__actions">
            <button class="login-fq__btn login-fq__btn--primary" type="submit" name="ingreso" id="fq-submit-btn">
              Entrar →
            </button>
            <a class="login-fq__btn" href="<?=$ruta?>/terminal/index">Terminal QR</a>
          </div>
        </form>

        <div class="login-fq__helper">
          Tip: si no tenés usuario, creá admin desde tools/crear_admin.php
        </div>
      </div>
    </div>
  </main>

  <?=$footer?>

  <script>
  (function(){
    const form    = document.getElementById('fq-login-form');
    const uInput  = document.getElementById('fq-usuario');
    const pInput  = document.getElementById('fq-password');
    const errU    = document.getElementById('fq-err-usuario');
    const errP    = document.getElementById('fq-err-pass');
    const bars    = [document.getElementById('pm1'), document.getElementById('pm2'),
                     document.getElementById('pm3'), document.getElementById('pm4')];
    const pwdLbl  = document.getElementById('fq-pwd-label');
    const btn     = document.getElementById('fq-submit-btn');

    function clearErr(el, msgEl) {
      el.classList.remove('has-error');
      msgEl.textContent = '';
    }
    function setErr(el, msgEl, msg) {
      el.classList.add('has-error');
      msgEl.textContent = msg;
    }

    // Medidor de fuerza de contraseña
    pInput.addEventListener('input', function() {
      const v = pInput.value;
      const strength = calcStrength(v);
      const labels = ['', 'Débil', 'Regular', 'Buena', 'Fuerte'];
      const cls    = ['', 'weak',  'medium',  'strong', 'strong'];
      bars.forEach((b, i) => {
        b.className = 'pwd-meter__bar';
        if (i < strength) b.classList.add(cls[strength]);
      });
      pwdLbl.textContent = v.length ? labels[strength] : '';
      clearErr(pInput, errP);
    });

    function calcStrength(v) {
      if (!v) return 0;
      let s = 0;
      if (v.length >= 6)  s++;
      if (v.length >= 10) s++;
      if (/[A-Z]/.test(v) || /[0-9]/.test(v)) s++;
      if (/[^A-Za-z0-9]/.test(v)) s++;
      return Math.min(s, 4) || (v.length >= 3 ? 1 : 0);
    }

    uInput.addEventListener('input', () => clearErr(uInput, errU));

    function validateForm() {
      let ok = true;
      const u = uInput.value.trim();
      const p = pInput.value;

      if (u === '') {
        setErr(uInput, errU, 'El usuario es requerido');
        ok = false;
      } else if (u.length < 3) {
        setErr(uInput, errU, 'Mínimo 3 caracteres');
        ok = false;
      } else if (u.length > 60) {
        setErr(uInput, errU, 'Máximo 60 caracteres');
        ok = false;
      } else {
        clearErr(uInput, errU);
      }

      if (p === '') {
        setErr(pInput, errP, 'La contraseña es requerida');
        ok = false;
      } else if (p.length < 3) {
        setErr(pInput, errP, 'Mínimo 3 caracteres');
        ok = false;
      } else {
        clearErr(pInput, errP);
      }

      return ok;
    }

    form.addEventListener('submit', function(e) {
      if (!validateForm()) {
        e.preventDefault();
        return;
      }
      btn.textContent = 'Entrando…';
      btn.disabled = true;
    });

    // Focus al cargar
    if (uInput.value === '') uInput.focus();
    else pInput.focus();
  })();
  </script>
</body>
</html>
