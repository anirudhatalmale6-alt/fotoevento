@extends('admin.layout')
@section('title','Nuevo evento')
@section('content')
<div class="pagehead">
  <a href="{{ route('admin.events.index') }}" class="btn ghost sm">← Volver</a>
  <div><h1>Nuevo evento</h1><div class="muted">Configura los datos y precios. Luego subirás las fotos.</div></div>
</div>

<form method="post" action="{{ route('admin.events.store') }}">
  @csrf
  <div class="card" style="margin-bottom:16px">
    <h2>Datos del evento</h2>
    <div class="row">
      <div class="col" style="min-width:260px">
        <label>Nombre del evento</label>
        <input name="name" value="{{ old('name') }}" placeholder="Promoción 2025 — Colegio San Martín" required>
      </div>
      <div class="col">
        <label>Fecha (opcional)</label>
        <input type="date" name="event_date" value="{{ old('event_date') }}">
      </div>
    </div>
    <div class="row" style="margin-top:14px">
      <div class="col">
        <label>Moneda</label>
        <input name="currency" value="{{ old('currency','S/') }}">
      </div>
      <div class="col">
        <label>Precio por foto individual</label>
        <input type="number" step="0.01" min="0" name="price_unit" value="{{ old('price_unit','15') }}" required>
      </div>
      <div class="col">
        <label>PIN de acceso (opcional)</label>
        <input name="pin" value="{{ old('pin') }}" maxlength="12" placeholder="ej. 2025">
      </div>
    </div>
    <div class="row" style="margin-top:14px">
      <div class="col" style="min-width:260px">
        <label>Texto de la marca de agua</label>
        <input name="watermark_text" value="{{ old('watermark_text', config('app.default_watermark','MUESTRA NO PAGADO')) }}" maxlength="60">
      </div>
    </div>
  </div>

  <div class="card" style="margin-bottom:16px">
    <h2>Paquetes (opcional)</h2>
    <div class="muted" style="margin-bottom:10px;font-size:13px">Define paquetes con precio especial. El sistema aplicará automáticamente el mejor precio al cliente.</div>
    <div id="pkgs"></div>
    <button type="button" class="btn ghost sm" onclick="addPkg()">+ Agregar paquete</button>
  </div>

  <button class="btn dark">Crear evento</button>
</form>

<template id="pkgtpl">
  <div class="row" style="margin-bottom:10px;align-items:flex-end">
    <div class="col"><label>Cantidad de fotos</label><input type="number" min="1" name="pkg_qty[]" placeholder="5"></div>
    <div class="col"><label>Precio del paquete</label><input type="number" step="0.01" min="0" name="pkg_price[]" placeholder="60"></div>
    <div class="col" style="min-width:180px"><label>Etiqueta (opcional)</label><input name="pkg_label[]" placeholder="Paquete 5 fotos"></div>
    <button type="button" class="btn ghost sm" onclick="this.parentElement.remove()">Quitar</button>
  </div>
</template>
@endsection
@section('scripts')
<script>
function addPkg(){ document.getElementById('pkgs').appendChild(document.getElementById('pkgtpl').content.cloneNode(true)); }
addPkg(); // una fila por defecto
</script>
@endsection
