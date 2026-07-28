<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ingresar · FotoEvento</title>
<style>
body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;
  background:radial-gradient(1000px 600px at 70% -10%,#2a2350,#0f1220 60%);color:#eef1f6;
  display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}
.card{background:#171b26;border:1px solid #2a3140;border-radius:18px;max-width:400px;width:100%;padding:34px 30px;box-shadow:0 30px 80px rgba(0,0,0,.5)}
.logo{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#7c5cff,#00c4a3);display:flex;align-items:center;justify-content:center;font-weight:800;margin-bottom:16px}
h1{font-size:20px;margin:0 0 4px}
p{color:#9aa4b5;font-size:14px;margin:0 0 22px}
label{display:block;font-size:12px;color:#9aa4b5;margin:14px 0 6px;text-transform:uppercase;letter-spacing:.05em}
input{width:100%;padding:12px 14px;border:1px solid #2a3140;border-radius:10px;background:#1d222c;color:#eef1f6;font-size:15px}
input:focus{outline:none;border-color:#7c5cff}
button{width:100%;margin-top:20px;padding:13px;border:none;border-radius:10px;background:#7c5cff;color:#fff;font-weight:700;font-size:15px;cursor:pointer}
.err{background:#3a1c1f;border:1px solid #6b2b2f;color:#ffb4b7;padding:10px 12px;border-radius:8px;font-size:13px;margin-top:14px}
.hint{color:#6b7688;font-size:12px;margin-top:16px;text-align:center}
</style>
</head>
<body>
<form class="card" method="post" action="{{ route('admin.login.post') }}">
  @csrf
  <div class="logo">JG</div>
  <h1>Panel de administración</h1>
  <p>Joel Garate Fotografía — venta de fotos</p>
  <label>Correo</label>
  <input type="email" name="email" value="{{ old('email') }}" autofocus required>
  <label>Contraseña</label>
  <input type="password" name="password" required>
  <button>Ingresar</button>
  @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
  <div class="hint">Acceso solo para el fotógrafo.</div>
</form>
</body>
</html>
