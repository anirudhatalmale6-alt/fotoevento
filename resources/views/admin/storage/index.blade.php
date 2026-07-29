@extends('admin.layout')
@section('title','Almacenamiento')
@section('content')

@php
  $fmt = function($b){
    $b=(float)$b;
    if($b>=1073741824) return number_format($b/1073741824,2).' GB';
    if($b>=1048576) return number_format($b/1048576,1).' MB';
    if($b>=1024) return number_format($b/1024,0).' KB';
    return number_format($b).' B';
  };
@endphp

<style>
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin:16px 0}
@media(max-width:700px){.stats{grid-template-columns:1fr}}
.stat{background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px}
.stat .n{font-size:26px;font-weight:800;line-height:1}
.stat .l{color:var(--muted);font-size:13px;margin-top:6px}
.bar{height:16px;background:#eef1f7;border-radius:99px;overflow:hidden;margin:10px 0 6px}
.bar>i{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,var(--brand),var(--brand2))}
.bar.warn>i{background:linear-gradient(90deg,#f0a02a,#e5484d)}
.brk{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--line);font-size:14px}
.brk .dot{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:8px;vertical-align:middle}
.note{background:#f7f8fc;border:1px solid var(--line);border-radius:12px;padding:14px;color:var(--muted);font-size:13px;line-height:1.6;margin-top:16px}
</style>

<div class="pagehead">
  <div>
    <h1>Almacenamiento</h1>
    <div class="muted">Espacio usado por tus fotos en la nube (Cloudflare R2).</div>
  </div>
  <div class="sp"></div>
  <a href="{{ route('admin.storage.index', ['fresh'=>1]) }}" class="btn ghost sm">↻ Actualizar</a>
</div>

@if($error ?? false)
  <div class="err">No pude leer el almacenamiento en este momento. Intenta con "Actualizar" en unos segundos.</div>
@else

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:baseline;flex-wrap:wrap;gap:8px">
    <div style="font-size:20px;font-weight:800">{{ $fmt($used) }} <span class="muted" style="font-size:15px;font-weight:600">de {{ $fmt($free_bytes) }} gratis</span></div>
    <div class="muted">{{ $pct }}% usado</div>
  </div>
  <div class="bar {{ $pct>=80?'warn':'' }}"><i style="width:{{ max($pct,1.5) }}%"></i></div>
  <div class="muted" style="font-size:13px">Te queda libre {{ $fmt($remaining) }}@if(!is_null($photos_left)) · espacio para ~{{ number_format($photos_left) }} fotos más aprox.@endif</div>
</div>

<div class="stats">
  <div class="stat"><div class="n">{{ $fmt($used) }}</div><div class="l">Usado ahora</div></div>
  <div class="stat"><div class="n">{{ $fmt($remaining) }}</div><div class="l">Disponible (gratis)</div></div>
  <div class="stat"><div class="n">{{ number_format($orig_count) }}</div><div class="l">Fotos guardadas</div></div>
</div>

<div class="card">
  <h2>En qué se usa</h2>
  @php
    $rows = [
      ['Originales en alta (privados)', $cats['orig'] ?? 0, 'var(--brand)'],
      ['Vistas previas (con marca de agua)', $cats['preview'] ?? 0, 'var(--brand2)'],
      ['Miniaturas', $cats['thumb'] ?? 0, 'var(--gold)'],
      ['Comprobantes de pago', $cats['receipts'] ?? 0, '#8a94a6'],
      ['Otros (QR, etc.)', $cats['other'] ?? 0, '#c9d0dc'],
    ];
  @endphp
  @foreach($rows as $r)
    <div class="brk"><span><span class="dot" style="background:{{ $r[2] }}"></span>{{ $r[0] }}</span><b>{{ $fmt($r[1]) }}</b></div>
  @endforeach
</div>

@if(!empty($events))
<div class="card" style="margin-top:16px">
  <h2>Por evento</h2>
  <div style="overflow-x:auto">
  <table>
    <thead><tr><th>Evento</th><th style="text-align:right">Espacio</th></tr></thead>
    <tbody>
      @foreach($events as $e)
        <tr><td>{{ $e['name'] }}</td><td style="text-align:right;font-weight:700">{{ $fmt($e['bytes']) }}</td></tr>
      @endforeach
    </tbody>
  </table>
  </div>
</div>
@endif

<div class="note">
  Tienes 10 GB gratis en Cloudflare R2. Con eventos de 200–300 fotos, te alcanza para muchos eventos.
  Y tranquilo: si algún día superas los 10 GB, <b>no se detiene ni se borra nada</b> — Cloudflare solo cobra unos centavos por GB extra (aprox. US$0.015 por GB al mes). Puedes liberar espacio borrando fotos o eventos que ya no necesites.
  <br><br>Los datos se actualizan cada pocos minutos; usa "↻ Actualizar" para verlos al instante.
</div>

@endif
@endsection
