<?php
// ==========================
// LANDING (HOME) — FICHAQR
// CSS EMBEBIDO y "SCOPEADO" bajo .landing-fq
// para evitar choques con otros estilos del proyecto.
// ==========================
$BASE = $ruta ?? ''; // respeta tu variable $ruta si existe
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <?=$head?>
  <title><?=$title?></title>

  <style>
    /* ==========================================
       LANDING FICHAQR — CSS EMBEBIDO (SCOPEADO)
       IMPORTANTE: todo cuelga de .landing-fq
       ========================================== */

    .landing-fq{
      /* variables SOLO para la landing (no global) */
      --lfq-bg: #0b0b12;
      --lfq-surface: rgba(18,18,28,.65);
      --lfq-border: rgba(255,255,255,.08);
      --lfq-text: rgba(255,255,255,.92);
      --lfq-muted: rgba(255,255,255,.62);
      --lfq-accent: #00e5a0;
      --lfq-accent2: #7c3aed;
      --lfq-danger: #ff3b3b;
      --lfq-shadow: 0 18px 50px rgba(0,0,0,.45);

      color: var(--lfq-text);
      padding: 26px 14px 40px;
    }

    .landing-fq__container{
      max-width: 1080px;
      margin: 0 auto;
    }

    .landing-fq__hero{
      border: 1px solid var(--lfq-border);
      background:
        radial-gradient(1200px 600px at 25% 10%, rgba(124,58,237,.18), transparent 60%),
        radial-gradient(900px 500px at 80% 30%, rgba(0,229,160,.14), transparent 60%),
        linear-gradient(180deg, rgba(18,18,28,.85), rgba(10,10,16,.75));
      border-radius: 22px;
      box-shadow: var(--lfq-shadow);
      padding: 22px;
      overflow: hidden;
    }

    .landing-fq__heroGrid{
      display: grid;
      grid-template-columns: 1.15fr .85fr;
      gap: 16px;
      align-items: start;
    }

    .landing-fq__kicker{
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono","Courier New", monospace;
      font-size: 12px;
      letter-spacing: .14em;
      text-transform: uppercase;
      color: var(--lfq-accent);
      margin-bottom: 8px;
    }

    .landing-fq__title{
      margin: 0;
      font-size: 34px;
      line-height: 1.08;
      letter-spacing: .01em;
    }

    .landing-fq__lead{
      margin: 10px 0 0;
      color: var(--lfq-muted);
      line-height: 1.65;
      max-width: 70ch;
      font-size: 14px;
    }

    .landing-fq__pills{
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 14px;
    }

    .landing-fq__pill{
      border: 1px solid var(--lfq-border);
      background: rgba(12,12,18,.55);
      padding: 7px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono","Courier New", monospace;
      color: rgba(255,255,255,.86);
    }

    .landing-fq__actions{
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 16px;
    }

    .landing-fq__btn{
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 11px 14px;
      border-radius: 14px;
      border: 1px solid var(--lfq-border);
      background: rgba(12,12,18,.45);
      color: rgba(255,255,255,.92);
      text-decoration: none;
      font-weight: 800;
      font-size: 13px;
      transition: transform .12s ease, background .12s ease, border-color .12s ease, box-shadow .12s ease;
      will-change: transform;
      user-select: none;
    }

    .landing-fq__btn:hover{
      transform: translateY(-1px);
      border-color: rgba(255,255,255,.14);
      background: rgba(12,12,18,.60);
      box-shadow: 0 10px 25px rgba(0,0,0,.35);
    }

    .landing-fq__btn--primary{
      border-color: rgba(0,229,160,.35);
      background: linear-gradient(135deg, rgba(0,229,160,.18), rgba(124,58,237,.12));
    }

    .landing-fq__btn--primary:hover{
      border-color: rgba(0,229,160,.55);
      box-shadow: 0 12px 28px rgba(0,229,160,.08), 0 12px 28px rgba(124,58,237,.10);
    }

    .landing-fq__note{
      margin-top: 14px;
      border: 1px dashed rgba(255,255,255,.16);
      background: rgba(10,10,16,.35);
      padding: 10px 12px;
      border-radius: 14px;
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono","Courier New", monospace;
      font-size: 12px;
      line-height: 1.55;
      color: rgba(255,255,255,.84);
    }

    .landing-fq__note code{
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.10);
      padding: 2px 6px;
      border-radius: 10px;
      color: rgba(255,255,255,.92);
    }

    .landing-fq__sideCard{
      border: 1px solid var(--lfq-border);
      background: rgba(10,10,16,.35);
      border-radius: 18px;
      padding: 16px;
    }

    .landing-fq__sideTitle{
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono","Courier New", monospace;
      font-size: 12px;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--lfq-muted);
      margin: 0 0 10px;
    }

    .landing-fq__step{
      display: flex;
      gap: 10px;
      padding: 10px;
      border-radius: 14px;
      border: 1px solid rgba(255,255,255,.10);
      background: rgba(12,12,18,.40);
      margin-bottom: 10px;
    }

    .landing-fq__stepN{
      width: 28px;
      height: 28px;
      border-radius: 10px;
      display: grid;
      place-items: center;
      font-weight: 900;
      color: #0b0b12;
      background: linear-gradient(135deg, rgba(124,58,237,.95), rgba(0,229,160,.85));
      flex: 0 0 auto;
    }

    .landing-fq__stepT{
      font-weight: 900;
      margin: 0;
      font-size: 13px;
    }

    .landing-fq__stepD{
      margin: 3px 0 0;
      color: var(--lfq-muted);
      font-size: 12px;
      line-height: 1.45;
    }

    .landing-fq__section{
      margin-top: 18px;
    }

    .landing-fq__sectionTitle{
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono","Courier New", monospace;
      font-size: 12px;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--lfq-muted);
      margin: 10px 0 12px;
    }

    .landing-fq__grid{
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
    }

    .landing-fq__card{
      border: 1px solid var(--lfq-border);
      background: rgba(12,12,18,.40);
      border-radius: 18px;
      padding: 16px;
    }

    .landing-fq__card h3{
      margin: 0 0 6px;
      font-size: 14px;
    }

    .landing-fq__card p{
      margin: 0;
      color: var(--lfq-muted);
      line-height: 1.55;
      font-size: 13px;
    }

    .landing-fq__split{
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .landing-fq__list{
      list-style: none;
      padding: 0;
      margin: 10px 0 0;
      display: grid;
      gap: 8px;
    }

    .landing-fq__li{
      display: flex;
      gap: 10px;
      align-items: flex-start;
      color: rgba(255,255,255,.90);
      font-size: 13px;
      line-height: 1.45;
    }

    .landing-fq__check{
      width: 18px;
      height: 18px;
      border-radius: 6px;
      display: grid;
      place-items: center;
      background: rgba(0,229,160,.12);
      border: 1px solid rgba(0,229,160,.25);
      color: var(--lfq-accent);
      font-weight: 900;
      flex: 0 0 auto;
      margin-top: 1px;
    }

    .landing-fq__cta{
      margin-top: 18px;
      border: 1px solid rgba(0,229,160,.20);
      background: linear-gradient(135deg, rgba(0,229,160,.10), rgba(124,58,237,.08));
      border-radius: 18px;
      padding: 16px;
      display: flex;
      gap: 12px;
      align-items: center;
      justify-content: space-between;
    }

    .landing-fq__cta h2{
      margin: 0;
      font-size: 18px;
    }

    .landing-fq__cta p{
      margin: 6px 0 0;
      color: var(--lfq-muted);
      line-height: 1.55;
      font-size: 13px;
    }

    /* Responsive */
    @media (max-width: 920px){
      .landing-fq__heroGrid{ grid-template-columns: 1fr; }
      .landing-fq__title{ font-size: 30px; }
      .landing-fq__grid{ grid-template-columns: repeat(2, 1fr); }
      .landing-fq__split{ grid-template-columns: 1fr; }
      .landing-fq__cta{ flex-direction: column; align-items: stretch; }
      .landing-fq__actions{ justify-content: flex-start; }
    }
    @media (max-width: 520px){
      .landing-fq__grid{ grid-template-columns: 1fr; }
    }
  </style>
</head>

<body>
  <?=$nav?>

  <!-- LANDING HOME -->
  <main class="landing-fq">
    <div class="landing-fq__container">

      <!-- HERO -->
      <section class="landing-fq__hero">
        <div class="landing-fq__heroGrid">

          <!-- Columna izquierda -->
          <div>
            <div class="landing-fq__kicker">CONTROL HORARIO · QR</div>
            <h1 class="landing-fq__title">Fichadas simples, rápidas y verificables</h1>

            <p class="landing-fq__lead">
              FichaQR registra <strong>ingresos</strong> y <strong>salidas</strong> con QR,
              deja historial y facilita el control de horas. Ideal para comercios y equipos con rotación.
            </p>

            <div class="landing-fq__pills">
              <span class="landing-fq__pill">✅ PHP puro + MVC</span>
              <span class="landing-fq__pill">📱 Sin equipos especiales</span>
              <span class="landing-fq__pill">🧾 Historial y auditoría</span>
              <span class="landing-fq__pill">⚡ Implementación rápida</span>
            </div>

            <div class="landing-fq__actions">
              <a class="landing-fq__btn landing-fq__btn--primary" href="<?=$BASE?>/login/login">Entrar</a>
              <a class="landing-fq__btn" href="<?=$BASE?>/terminal/index">Terminal QR</a>
              <a class="landing-fq__btn" href="<?=$BASE?>/admin/gestion">Panel (admin/jefe)</a>
            </div>

            <div class="landing-fq__note">
              <strong>QR esperado:</strong> <code>EMP:&lt;id_empleado&gt;</code> (ej: <code>EMP:12</code>).<br>
              <span style="color: rgba(255,255,255,.62);">
                Para comenzar: importá <code>fichaqr.sql</code>, creá un usuario admin y cargá empleados.
              </span>
            </div>
          </div>

          <!-- Columna derecha -->
          <aside class="landing-fq__sideCard">
            <p class="landing-fq__sideTitle">En 3 pasos</p>

            <div class="landing-fq__step">
              <div class="landing-fq__stepN">1</div>
              <div>
                <p class="landing-fq__stepT">Cargá empleados</p>
                <p class="landing-fq__stepD">Alta rápida desde el panel admin/jefe.</p>
              </div>
            </div>

            <div class="landing-fq__step">
              <div class="landing-fq__stepN">2</div>
              <div>
                <p class="landing-fq__stepT">Fichá con QR</p>
                <p class="landing-fq__stepD">Entrada y salida en segundos, sin fricción.</p>
              </div>
            </div>

            <div class="landing-fq__step" style="margin-bottom:0;">
              <div class="landing-fq__stepN">3</div>
              <div>
                <p class="landing-fq__stepT">Consultá historial</p>
                <p class="landing-fq__stepD">Registros claros para control y auditoría.</p>
              </div>
            </div>

            <div style="height:10px"></div>
            <a class="landing-fq__btn" style="width:100%;" href="<?=$BASE?>/login/login">Ir a Login</a>
          </aside>

        </div>
      </section>

      <!-- VIRTUDES -->
      <section class="landing-fq__section">
        <div class="landing-fq__sectionTitle">Virtudes del servicio</div>

        <div class="landing-fq__grid">
          <article class="landing-fq__card">
            <h3>⏱️ Registro inmediato</h3>
            <p>Entrada/salida en segundos. Menos fricción, menos errores y menos excusas.</p>
          </article>

          <article class="landing-fq__card">
            <h3>🧾 Trazabilidad</h3>
            <p>Cada fichada queda guardada con fecha/hora y empleado. Fácil de auditar y revisar.</p>
          </article>

          <article class="landing-fq__card">
            <h3>📱 Sin hardware extra</h3>
            <p>Terminal QR en navegador. Podés usar PC, tablet o celular según tu necesidad.</p>
          </article>

          <article class="landing-fq__card">
            <h3>🔐 Accesos por rol</h3>
            <p>Admin/jefe gestiona empleados y reportes. Empleado consulta su propio historial.</p>
          </article>

          <article class="landing-fq__card">
            <h3>🧠 Orden administrativo</h3>
            <p>Chau planillas y papel. Todo queda en el sistema y se consulta cuando haga falta.</p>
          </article>

          <article class="landing-fq__card">
            <h3>📈 Base para KPI</h3>
            <p>Datos listos para calcular horas, ausencias, puntualidad y reportes de asistencia.</p>
          </article>
        </div>
      </section>

      <!-- BENEFICIOS -->
      <section class="landing-fq__section">
        <div class="landing-fq__sectionTitle">Beneficios claros</div>

        <div class="landing-fq__split">
          <div class="landing-fq__card">
            <h3>Para empleadores / jefes</h3>
            <ul class="landing-fq__list">
              <li class="landing-fq__li"><span class="landing-fq__check">✓</span>Control real de ingresos y salidas por empleado.</li>
              <li class="landing-fq__li"><span class="landing-fq__check">✓</span>Menos conflictos: información centralizada y verificable.</li>
              <li class="landing-fq__li"><span class="landing-fq__check">✓</span>Base para reportes de horas, llegadas tarde y ausencias.</li>
              <li class="landing-fq__li"><span class="landing-fq__check">✓</span>Mejor organización para gestión de turnos y liquidación.</li>
            </ul>
          </div>

          <div class="landing-fq__card">
            <h3>Para empleados</h3>
            <ul class="landing-fq__list">
              <li class="landing-fq__li"><span class="landing-fq__check">✓</span>Transparencia: cada fichada queda registrada.</li>
              <li class="landing-fq__li"><span class="landing-fq__check">✓</span>Historial personal: “Mis fichadas” (según rol).</li>
              <li class="landing-fq__li"><span class="landing-fq__check">✓</span>Menos discusiones por horarios: hay un registro claro.</li>
              <li class="landing-fq__li"><span class="landing-fq__check">✓</span>Proceso simple: escanear y listo.</li>
            </ul>
          </div>
        </div>
      </section>

      <!-- CTA FINAL -->
      <section class="landing-fq__cta">
        <div>
          <h2>Listo para usar en tu negocio</h2>
          <p>Panel de administración + terminal QR + registros claros para control de asistencia.</p>
        </div>

        <div class="landing-fq__actions" style="margin-top:0;">
          <a class="landing-fq__btn landing-fq__btn--primary" href="<?=$BASE?>/login/login">Empezar</a>
          <a class="landing-fq__btn" href="<?=$BASE?>/terminal/index">Abrir Terminal</a>
        </div>
      </section>

    </div>
  </main>

  <?=$footer?>
</body>
</html>