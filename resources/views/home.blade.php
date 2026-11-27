<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sistemas UMP</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #f8f9fe;
    --card: #ffffff;
    --border: #e9ecef;
    --title: #32325d;
    --text: #525f7f;
    --muted: #67748e;
    --primary: #5167d9;
    --grad-primary: linear-gradient(87deg, #5167d9, #7a55d9);
    --shadow: 0 2px 8px rgba(50, 50, 93, .06);
    --shadow-hover: 0 8px 20px rgba(50, 50, 93, .12);
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: var(--bg);
    color: var(--text);
    font-family: "Open Sans", system-ui, -apple-system, "Segoe UI", sans-serif;
  }
  .topbar { height: 6px; background: var(--grad-primary); }
  main {
    flex: 1;
    width: 100%;
    max-width: 1040px;
    margin: 0 auto;
    padding: 56px 32px 40px;
    display: flex;
    flex-direction: column;
    gap: 40px;
  }
  header { display: flex; flex-direction: column; align-items: center; gap: 14px; text-align: center; }
  .logo {
    width: 56px; height: 56px; border-radius: 14px;
    background: var(--grad-primary);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: 17px;
    box-shadow: 0 6px 14px rgba(81, 103, 217, .3);
  }
  h1 { margin: 0; font-size: 32px; font-weight: 700; color: var(--title); }
  header p { margin: 0; font-size: 16px; color: var(--muted); }

  .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }

  .card {
    display: flex; align-items: center; gap: 18px;
    padding: 24px;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 10px;
    box-shadow: var(--shadow);
    color: var(--text);
    text-decoration: none;
    transition: transform .15s, box-shadow .15s, border-color .15s;
  }
  .card:hover, .card:focus-visible {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
    border-color: var(--primary);
  }
  .card:focus-visible { outline: 3px solid rgba(81, 103, 217, .35); outline-offset: 2px; }

  .icon {
    flex: none; width: 48px; height: 48px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
  }
  .icon svg { width: 22px; height: 22px; fill: none; stroke: #fff; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .info { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
  .name { font-size: 17px; font-weight: 700; color: var(--title); }
  .desc { font-size: 14px; line-height: 1.5; }
  .go {
    width: 18px; height: 18px; flex: none;
    fill: none; stroke: var(--primary); stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    opacity: 0; transform: translateX(-4px);
    transition: opacity .15s, transform .15s;
  }
  .card:hover .go, .card:focus-visible .go { opacity: 1; transform: none; }

  .card.featured { padding: 28px 32px; gap: 24px; }
  .card.featured .icon { width: 64px; height: 64px; }
  .card.featured .icon svg { width: 28px; height: 28px; }
  .card.featured .name { font-size: 22px; }
  .card.featured .desc { font-size: 15px; }
  .card.featured .go { width: 22px; height: 22px; }

  .c-primary { background: var(--grad-primary); }
  .c-green   { background: linear-gradient(87deg, #2dce89, #2dcaa8); }
  .c-blue    { background: linear-gradient(87deg, #11cdef, #1171ef); }
  .c-orange  { background: linear-gradient(87deg, #fb6340, #fbb140); }
  .c-teal    { background: linear-gradient(87deg, #0ea5a0, #11cdef); }
  .c-red     { background: linear-gradient(87deg, #f5365c, #f56036); }
  .c-purple  { background: linear-gradient(87deg, #8965e0, #bc65e0); }
  .c-indigo  { background: linear-gradient(87deg, #3b5bdb, #11cdef); }

  footer { padding: 24px 32px 32px; text-align: center; font-size: 13px; color: var(--muted); }

  @media (max-width: 560px) {
    main { padding: 40px 20px 24px; gap: 32px; }
    h1 { font-size: 26px; }
    .card.featured { padding: 22px; gap: 16px; }
    .go { opacity: 1; transform: none; }
  }
  @media (prefers-reduced-motion: reduce) {
    .card, .go { transition: none; }
    .card:hover, .card:focus-visible { transform: none; }
  }
</style>
</head>
<body>
<div class="topbar"></div>

<main>
  <header>
    <div class="logo" aria-hidden="true">UMP</div>
    <h1>Sistemas UMP</h1>
    <p>Selecione o sistema que deseja acessar.</p>
  </header>

  <a class="card featured" href="{{ route('dashboard.home') }}">
    <span class="icon c-primary"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
    <span class="info">
      <span class="name">Plataforma UMP</span>
      <span class="desc">Cadastro, Estatística, Tesouraria...</span>
    </span>
    <svg class="go" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
  </a>

  <div class="grid">
    <a class="card" href="{{ route('coletor-dados.login') }}">
      <span class="icon c-green"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="5" width="14" height="16" rx="2"/><path d="M9 12l2 2 4-4"/></svg></span>
      <span class="info"><span class="name">Coletor de Dados</span><span class="desc">Envio padronizado de informações.</span></span>
      <svg class="go" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </a>

    <a class="card" href="{{ route('estatistica') }}">
      <span class="icon c-blue"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7"/></svg></span>
      <span class="info"><span class="name">Painel de Estatística</span><span class="desc">Números e gráficos consolidados.</span></span>
      <svg class="go" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </a>

    <a class="card" href="{{ route('planejamento-estrategico') }}">
      <span class="icon c-orange"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/></svg></span>
      <span class="info"><span class="name">Metas do Quinquênio</span><span class="desc">Metas e indicadores do período.</span></span>
      <svg class="go" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </a>

    <a class="card" href="{{ route('game-ump.acompanhamento') }}">
      <span class="icon c-teal"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l2.2 4.5L19 8.3l-3.5 3.4.8 4.8L12 14.3 7.7 16.5l.8-4.8L5 8.3l4.8-.8L12 3z"/></svg></span>
      <span class="info"><span class="name">Game UMP</span><span class="desc">Acompanhamento e gamificação.</span></span>
      <svg class="go" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </a>

    <a class="card" href="{{ route('digesto') }}">
      <span class="icon c-red"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5a2 2 0 0 1 2-2h12v18H6a2 2 0 0 1-2-2z"/><path d="M8 3v18"/></svg></span>
      <span class="info"><span class="name">Digesto</span><span class="desc">Documentos, resoluções e normas.</span></span>
      <svg class="go" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </a>

    <a class="card" href="https://sisvoto.ump.app.br">
      <span class="icon c-purple"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12v6a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-6"/><path d="M9 11l2 2 4-4"/></svg></span>
      <span class="info"><span class="name">SISVOTO</span><span class="desc">Votações e eleições online.</span></span>
      <svg class="go" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </a>

    <a class="card" href="https://sicom.ump.app.br">
      <span class="icon c-indigo"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L3 21l1.9-6.4A8 8 0 1 1 21 12z"/></svg></span>
      <span class="info"><span class="name">SICOM</span><span class="desc">Sistema de Congressos para Sinodais e Federações.</span></span>
      <svg class="go" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </a>
  </div>
</main>

<footer>© {{ date('Y') }} UMP · União de Mocidade Presbiteriana</footer>
</body>
</html>
