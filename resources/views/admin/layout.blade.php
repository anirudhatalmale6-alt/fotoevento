<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Panel') · FotoEvento</title>
<style>
:root{--bg:#f4f6fb;--card:#fff;--ink:#1c2333;--muted:#6b7688;--line:#e6eaf1;--brand:#7c5cff;--brand2:#00c4a3;--gold:#d9a441;--danger:#e5484d}
*{box-sizing:border-box}
body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;background:var(--bg);color:var(--ink)}
a{color:inherit;text-decoration:none}
.top{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:20}
.top .in{max-width:1100px;margin:0 auto;display:flex;align-items:center;gap:12px;padding:0 18px;height:60px}
.brand{display:flex;align-items:center;gap:10px;font-weight:800}
.logo{width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,var(--brand),var(--brand2));color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800}
.brand small{display:block;font-weight:600;color:var(--muted);font-size:11px}
.top .sp{flex:1}
.wrap{max-width:1100px;margin:0 auto;padding:24px 18px 60px}
.btn{display:inline-flex;align-items:center;gap:7px;background:var(--brand);color:#fff;border:none;border-radius:10px;padding:10px 16px;font-weight:700;cursor:pointer;font-size:14px}
.btn:hover{filter:brightness(1.07)}
.btn.ghost{background:#eef1f7;color:var(--ink)}
.btn.dark{background:var(--ink)}
.btn.danger{background:var(--danger)}
.btn.sm{padding:7px 12px;font-size:13px}
.card{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:20px}
.flash{background:#e7f8f1;border:1px solid #b9ead9;color:#0b6b52;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px}
.err{background:#fdecec;border:1px solid #f6c9cb;color:#a51f24;padding:10px 14px;border-radius:10px;margin-bottom:14px;font-size:14px}
label{display:block;font-size:13px;color:var(--muted);margin:0 0 6px;font-weight:600}
input,select{width:100%;padding:11px 13px;border:1px solid var(--line);border-radius:10px;font-size:14px;background:#fff;color:var(--ink)}
input:focus,select:focus{outline:none;border-color:var(--brand)}
.row{display:flex;gap:14px;flex-wrap:wrap}
.row>.col{flex:1;min-width:150px}
h1{font-size:22px;margin:0 0 4px}
h2{font-size:16px;margin:0 0 12px}
.muted{color:var(--muted)}
.pagehead{display:flex;align-items:center;gap:12px;margin-bottom:18px}
.pagehead .sp{flex:1}
table{width:100%;border-collapse:collapse}
th,td{text-align:left;padding:12px 10px;border-bottom:1px solid var(--line);font-size:14px}
th{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em}
.badge{display:inline-block;font-size:12px;background:#eef1f7;color:var(--muted);border-radius:999px;padding:3px 10px}
.badge.on{background:#e7f8f1;color:#0b6b52}
.kv{padding:6px 0;font-size:14px}.kv b{color:var(--muted);font-weight:600;margin-right:4px}
.navlink{font-weight:700;color:var(--muted);padding:8px 4px}.navlink.active{color:var(--ink)}
.notifbtn{display:inline-flex;align-items:center;gap:6px;background:#eef1f7;color:var(--ink);border:1px solid var(--line);border-radius:10px;padding:7px 12px;font-weight:700;font-size:13px;cursor:pointer}
.notifbtn.on{background:#e7f8f1;border-color:#b9ead9;color:#0b6b52}
.toast{position:fixed;left:50%;bottom:22px;transform:translateX(-50%) translateY(20px);background:#111827;color:#fff;padding:13px 18px;border-radius:12px;font-size:14px;box-shadow:0 12px 34px rgba(0,0,0,.28);opacity:0;pointer-events:none;transition:.25s;z-index:100;max-width:92vw;text-align:center}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0);pointer-events:auto}
</style>
</head>
<body>
<header class="top">
  <div class="in">
    <a href="{{ route('admin.events.index') }}" class="brand">
      <span class="logo">JG</span>
      <span>FotoEvento <small>Joel Garate Fotografía</small></span>
    </a>
    @auth
      <a href="{{ route('admin.events.index') }}" class="navlink {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">Eventos</a>
      <a href="{{ route('admin.orders.index') }}" class="navlink {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">Pedidos</a>
      <a href="{{ route('admin.analytics.index') }}" class="navlink {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">Analítica</a>
      <a href="{{ route('admin.settings.edit') }}" class="navlink {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">Configuración</a>
    @endauth
    <div class="sp"></div>
    @auth
      <button type="button" id="notifBtn" class="notifbtn" title="Activa el sonido y aviso cuando llegue un pedido">🔔 Avisos</button>
      <a href="{{ route('admin.events.create') }}" class="btn sm">+ Nuevo evento</a>
      <form method="post" action="{{ route('admin.logout') }}" style="display:inline">@csrf
        <button class="btn ghost sm">Salir</button>
      </form>
    @endauth
  </div>
</header>
<main class="wrap">
  @if(session('ok'))<div class="flash">{{ session('ok') }}</div>@endif
  @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
  @yield('content')
</main>
<script>
window.CSRF = document.querySelector('meta[name=csrf-token]').content;
</script>
@auth
<div id="notifToast" class="toast"></div>
<script>
(function(){
  const PING="{{ route('admin.orders.ping') }}";
  let lastId=null, enabled=false, audioCtx=null;
  function beep(){
    try{
      audioCtx=audioCtx||new (window.AudioContext||window.webkitAudioContext)();
      if(audioCtx.state==='suspended') audioCtx.resume();
      const o=audioCtx.createOscillator(), g=audioCtx.createGain();
      o.connect(g); g.connect(audioCtx.destination);
      o.type='sine'; o.frequency.setValueAtTime(880,audioCtx.currentTime);
      o.frequency.setValueAtTime(660,audioCtx.currentTime+0.15);
      g.gain.setValueAtTime(0.18,audioCtx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001,audioCtx.currentTime+0.35);
      o.start(); o.stop(audioCtx.currentTime+0.35);
    }catch(e){}
  }
  function toast(html){
    const t=document.getElementById('notifToast'); if(!t) return;
    t.innerHTML=html; t.classList.add('show');
    clearTimeout(t._t); t._t=setTimeout(()=>t.classList.remove('show'),9000);
  }
  async function poll(){
    try{
      const r=await fetch(PING,{headers:{'Accept':'application/json'}});
      if(!r.ok) return;
      const j=await r.json();
      if(lastId===null){ lastId=j.max_id; return; }
      if(j.max_id>lastId){
        lastId=j.max_id; const l=j.latest;
        const msg=l?`Nuevo pedido ${l.code} · ${l.name} · ${l.currency} ${l.total}`:'Nuevo pedido recibido';
        beep();
        toast(`🎉 ${msg} ${l?`<a href="${l.url}" style="color:#7cf0d0;font-weight:700;margin-left:8px">Ver pedido</a>`:''}`);
        if('Notification' in window && Notification.permission==='granted'){
          try{ const n=new Notification('🎉 Nuevo pedido en FotoEvento',{body:msg}); n.onclick=()=>{ if(l&&l.url) window.open(l.url,'_blank'); }; }catch(e){}
        }
      }
    }catch(e){}
  }
  const btn=document.getElementById('notifBtn');
  if(btn){
    btn.addEventListener('click', async ()=>{
      enabled=true; beep();
      if('Notification' in window && Notification.permission!=='granted'){ try{ await Notification.requestPermission(); }catch(e){} }
      btn.textContent='🔔 Avisos activos'; btn.classList.add('on');
      toast('Avisos activados ✓ Con esta pestaña abierta te aviso con sonido cuando entre un pedido.');
    });
  }
  poll(); setInterval(poll, 20000);
})();
</script>
@endauth
@yield('scripts')
</body>
</html>
