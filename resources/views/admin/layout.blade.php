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
    @endauth
    <div class="sp"></div>
    @auth
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
@yield('scripts')
</body>
</html>
