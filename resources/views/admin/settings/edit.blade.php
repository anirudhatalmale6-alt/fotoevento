@extends('admin.layout')
@section('title','Configuración')
@section('content')
<div class="pagehead">
  <div>
    <h1>Configuración de pago (Yape)</h1>
    <div class="muted">Estos datos aparecen en la pantalla de pago de tus clientes cuando confirman su pedido.</div>
  </div>
</div>

<form method="post" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col card" style="min-width:280px">
      <h2>Datos de tu Yape</h2>
      <div style="margin-bottom:12px">
        <label>Número de Yape</label>
        <input name="yape_number" value="{{ old('yape_number', $yape['number']) }}" placeholder="Ej: 999 888 777">
      </div>
      <div>
        <label>Nombre de la cuenta</label>
        <input name="yape_account" value="{{ old('yape_account', $yape['account']) }}" placeholder="Ej: Joel Garate">
      </div>
    </div>

    <div class="col card" style="min-width:280px">
      <h2>Código QR de Yape</h2>
      <p class="muted" style="font-size:13px;margin-top:0">Sube una captura/foto de tu QR desde la app de Yape. El cliente lo escaneará para pagarte.</p>
      @if(!empty($yape['qr_path']))
        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($yape['qr_path']) }}" alt="QR actual"
             style="width:160px;border:1px solid var(--line);border-radius:12px;background:#fff;padding:8px;display:block;margin-bottom:10px">
        <div class="muted" style="font-size:12px;margin-bottom:10px">QR actual. Sube uno nuevo para reemplazarlo.</div>
      @endif
      <input type="file" name="yape_qr" accept="image/jpeg,image/png">
    </div>
  </div>

  <button class="btn" style="margin-top:16px">Guardar datos de Yape</button>
</form>
@endsection
