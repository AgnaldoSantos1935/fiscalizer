<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/favicon.svg') }}" type="image/svg+xml">
    <style>
        html { scroll-behavior: smooth; }
        body {
            background: linear-gradient(180deg, #f8fbff 0%, #eef7ff 100%);
            font-family: 'Inter', Arial, sans-serif;
            padding-bottom: 64px;
        }
        .gov-header { background: linear-gradient(90deg, #0f172a 0%, #1e293b 45%, #0ea5e9 100%); color: #fff; position: sticky; top: 0; z-index: 1020; box-shadow: 0 10px 26px rgba(14,165,233,.25); }
        .gov-header .brand { display: flex; align-items: center; gap: .75rem; }
        .btn-acesso { background: linear-gradient(135deg, #0ea5e9, #6366f1); color: #fff; border: none; box-shadow: 0 8px 20px rgba(14,165,233,.35); }
        .btn-acesso:hover { filter: brightness(1.06); transform: translateY(-1px); }
        .hero { background: radial-gradient(1200px 500px at 10% 10%, rgba(255,255,255,.18), transparent), linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0ea5e9 100%); color: #fff; }
        .hero { background-image: radial-gradient(1200px 500px at 10% 10%, rgba(255,255,255,.18), transparent), linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0ea5e9 100%); background-size: cover; background-repeat: no-repeat; background-position: center; }
        .hero .lead { opacity: .9; }
        .hero-benefits { color: #ffffff; }
        .hero-benefits h5 { font-weight: 700; color: #ffffff; margin-bottom: .7rem; font-size: 1.35rem; letter-spacing: .2px; }
        .hero-benefits ul { list-style: none; padding-left: 0; margin: .5rem 0 0; }
        .hero-benefits li { display: flex; align-items: flex-start; gap: .6rem; margin-bottom: .5rem; color: #e2e8f0; }
        .hero-benefits li span { line-height: 1.5; text-shadow: 0 1px 2px rgba(0,0,0,.35); }
        .hero-benefits h5 { text-shadow: 0 1px 2px rgba(0,0,0,.35); }
        .hero-benefits .icon { color: #0ea5e9; }
        .card-feature i { font-size: 1.6rem; }
        .metric-card { border: 1px solid #dbeafe; background: rgba(255,255,255,.95); box-shadow: 0 10px 26px rgba(99,102,241,.14); }
        .metric-card .value { font-size: 1.6rem; font-weight: 800; color: #0ea5e9; }
        .module-card { position: relative; transition: transform .24s ease, box-shadow .24s ease; border: 1px solid transparent; border-radius: 16px; box-shadow: 0 10px 26px rgba(2,132,199,.12); background: linear-gradient(#ffffff, #ffffff) padding-box, linear-gradient(135deg, #0ea5e9, #6366f1) border-box; }
        .module-card:hover { transform: translateY(-4px); box-shadow: 0 18px 38px rgba(2,132,199,.22); }
        .module-card i { background: linear-gradient(135deg, #0ea5e9, #6366f1); -webkit-background-clip: text; background-clip: text; color: transparent !important; text-shadow: 0 2px 12px rgba(14,165,233,.25); }
        .card-outline { border-radius: 16px; border: 1px solid #dbeafe; box-shadow: 0 8px 20px rgba(99,102,241,.10); }
        .section-title { font-weight: 700; }
        .section-title::after { content: ""; display: block; width: 64px; height: 4px; background: linear-gradient(90deg, #0ea5e9, #6366f1); border-radius: 4px; margin-top: .35rem; }

        .card-media { display: block; height: 160px; width: auto; max-width: 100%; object-fit: contain; margin: 0 auto 12px; }
        @media (max-width: 767px) { .card-media { height: 120px; } }

        @keyframes borderGlow { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .module-card::after { content: none; }

        .btn, .btn-acesso { position: relative; overflow: hidden; }
        .ripple-effect { position: absolute; border-radius: 50%; transform: scale(0); animation: ripple 600ms linear; background: rgba(255,255,255,.4); pointer-events: none; }
        @keyframes ripple { to { transform: scale(15); opacity: 0; } }

        .fixed-footer { position: fixed; left: 0; right: 0; bottom: 0; background: #0f172a; color: #fff; border-top: 1px solid #0ea5e9; z-index: 1040; }
        .fixed-footer .container { min-height: 56px; }
        .fixed-footer a { color: #e2e8f0; }
        .fixed-footer a:hover { color: #ffffff; }
    </style>
</head>
<body>

@yield('content')

<footer class="fixed-footer py-2">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="small">© {{ date('Y') }} Governo do Pará • SEDUC • Fiscalizer</div>
    <div class="d-none d-md-flex align-items-center gap-3">
      <a href="#" class="text-decoration-none small">Privacidade</a>
      <a href="#" class="text-decoration-none small">Termos</a>
      <a href="#contato" class="text-decoration-none small">Contato</a>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="#" class="text-decoration-none"><i class="fa-brands fa-instagram"></i></a>
      <a href="#" class="text-decoration-none"><i class="fa-brands fa-facebook"></i></a>
      <a href="#" class="text-decoration-none"><i class="fa-brands fa-linkedin"></i></a>
      <a href="#" class="text-decoration-none"><i class="fa-brands fa-twitter"></i></a>
    </div>
  </div>
  </footer>

</body>
<div id="chatWidget" style="position:fixed; right:18px; bottom:78px; z-index:1100;">
  <button id="chatToggle" class="btn btn-acesso rounded-circle" style="width:56px; height:56px; display:flex; align-items:center; justify-content:center;">
    <i class="fa-solid fa-robot"></i>
  </button>
  <div id="chatPanel" class="card module-card" style="width:320px; height:430px; position:absolute; right:0; bottom:68px; display:none;">
    <div class="card-body d-flex flex-column" style="height:100%;">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <strong>Assistente Fiscalizer</strong>
        <button id="chatClose" class="btn btn-sm btn-outline-secondary">✕</button>
      </div>
      <div id="chatLog" class="flex-grow-1 border rounded p-2 mb-2" style="overflow:auto; background:#ffffffcc;"></div>
      <div class="input-group">
        <input id="chatText" type="text" class="form-control" placeholder="Escreva sua pergunta">
        <button id="chatSendBtn" class="btn btn-acesso">Enviar</button>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('click', function(e) {
  const t = e.target.closest('.btn, .btn-acesso');
  if (!t) return;
  const r = t.getBoundingClientRect();
  const s = Math.max(r.width, r.height);
  const x = e.clientX - r.left - s / 2;
  const y = e.clientY - r.top - s / 2;
  const d = document.createElement('span');
  d.className = 'ripple-effect';
  d.style.width = s + 'px';
  d.style.height = s + 'px';
  d.style.left = x + 'px';
  d.style.top = y + 'px';
  t.appendChild(d);
  d.addEventListener('animationend', function() { d.remove(); });
});
document.addEventListener('DOMContentLoaded', function () {
  const w = document.getElementById('chatWidget');
  const p = document.getElementById('chatPanel');
  const t = document.getElementById('chatToggle');
  const c = document.getElementById('chatClose');
  const log = document.getElementById('chatLog');
  const txt = document.getElementById('chatText');
  const send = document.getElementById('chatSendBtn');

  function append(text, who) {
    const d = document.createElement('div');
    d.className = who === 'user' ? 'text-end mb-1' : 'text-start mb-1';
    d.textContent = (who === 'user' ? 'Você: ' : 'Assistente: ') + text;
    log.appendChild(d);
    log.scrollTop = log.scrollHeight;
  }
  function saveHist() {
    localStorage.setItem('fx_chat_hist', log.innerHTML);
  }
  function loadHist() {
    localStorage.removeItem('fx_chat_hist');
    log.innerHTML = '';
  }
  loadHist();
  try {
    fetch('{{ route('site.chatbot.ask') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ reset: true })
    }).catch(function(){});
  } catch (e) {}

  t.addEventListener('click', function () { p.style.display = p.style.display === 'none' || !p.style.display ? 'block' : 'none'; });
  c.addEventListener('click', function () { p.style.display = 'none'; });

  async function sendMsg() {
    const q = txt.value.trim();
    if (!q) return;
    append(q, 'user');
    saveHist();
    txt.value = '';
    const typing = document.createElement('div');
    typing.className = 'text-start mb-1';
    typing.textContent = 'Assistente está digitando…';
    log.appendChild(typing);
    log.scrollTop = log.scrollHeight;
    try {
      const resp = await fetch('{{ route('site.chatbot.ask') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ pergunta: q })
      });
      const data = await resp.json();
      typing.remove();
      append(data.resposta || 'Sem resposta disponível.', 'bot');
      if (Array.isArray(data.sugestoes) && data.sugestoes.length) {
        const wrap = document.createElement('div');
        wrap.className = 'text-start mb-2';
        const label = document.createElement('div');
        label.textContent = 'Sugestões:';
        wrap.appendChild(label);
        data.sugestoes.slice(0,3).forEach(function(s){
          const b = document.createElement('button');
          b.className = 'btn btn-sm btn-outline-primary me-1 mt-1';
          b.textContent = s;
          b.addEventListener('click', function(){ txt.value = s; sendMsg(); });
          wrap.appendChild(b);
        });
        log.appendChild(wrap);
        log.scrollTop = log.scrollHeight;
      }
      if (Array.isArray(data.hist_preview) && data.hist_preview.length) {
        const h = document.createElement('div');
        h.className = 'text-start mb-2';
        const lbl = document.createElement('div');
        lbl.textContent = 'Últimas:';
        h.appendChild(lbl);
        data.hist_preview.forEach(function(item){
          const line = document.createElement('div');
          line.className = 'small';
          line.textContent = (item.t || '') + ' • ' + (item.q || '');
          h.appendChild(line);
        });
        log.appendChild(h);
        log.scrollTop = log.scrollHeight;
      }
      saveHist();
    } catch (e) {
      typing.remove();
      append('Erro ao enviar a mensagem.', 'bot');
    }
  }

  send.addEventListener('click', sendMsg);
  txt.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); sendMsg(); } });
});
</script>
</html>
