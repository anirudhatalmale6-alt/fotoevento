<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $event->name }} — Galería privada</title>
<style>
body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;
  background:radial-gradient(1200px 700px at 70% -10%,#20263a,#0b0d12 60%);color:#eef1f6;
  display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}
.card{background:#161a22;border:1px solid #2a3140;border-radius:18px;max-width:420px;width:100%;padding:34px 30px;box-shadow:0 30px 80px rgba(0,0,0,.5)}
.badge{display:inline-block;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#9aa4b5;border:1px solid #2a3140;padding:5px 10px;border-radius:999px}
h1{font-size:21px;margin:14px 0 6px}
p{color:#9aa4b5;font-size:14px;margin:0 0 22px;line-height:1.5}
label{display:block;font-size:12px;color:#9aa4b5;margin:0 0 6px;letter-spacing:.05em;text-transform:uppercase}
input{width:100%;background:#1d222c;border:1px solid #2a3140;color:#eef1f6;border-radius:12px;padding:14px 16px;font-size:22px;letter-spacing:.4em;text-align:center;font-weight:700}
input:focus{outline:none;border-color:#7c5cff}
button{width:100%;margin-top:16px;background:#7c5cff;color:#fff;border:none;border-radius:12px;padding:14px;font-weight:700;font-size:15px;cursor:pointer}
.err{color:#ff6b74;font-size:13px;margin-top:10px}
</style>
</head>
<body>
<form class="card" method="post" action="{{ route('gallery.unlock',$event->slug) }}">
  @csrf
  <span class="badge">Galería privada</span>
  <h1>{{ $event->name }}</h1>
  <p>Esta galería es privada. Ingresa el PIN que te compartió el fotógrafo.</p>
  <label>PIN de acceso</label>
  <input name="pin" inputmode="numeric" maxlength="12" placeholder="••••" autofocus autocomplete="off">
  @if($error)<div class="err">{{ $error }}</div>@endif
  <button>Ver mi galería</button>
</form>
</body>
</html>
