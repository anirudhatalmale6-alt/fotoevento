@extends('admin.layout')
@section('title','Analítica · '.$event->name)
@section('content')

<style>
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px}
@media(max-width:820px){.stats{grid-template-columns:repeat(2,1fr)}}
.stat{background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px}
.stat .n{font-size:28px;font-weight:800;line-height:1}
.stat .l{color:var(--muted);font-size:13px;margin-top:6px}
/* Barras por día */
.chart{display:flex;align-items:flex-end;gap:6px;height:150px;padding:10px 0;overflow-x:auto}
.day{display:flex;flex-direction:column;align-items:center;gap:4px;min-width:34px}
.bars{display:flex;align-items:flex-end;gap:3px;height:120px}
.bar{width:9px;border-radius:4px 4px 0 0;background:var(--brand)}
.bar.p{background:var(--brand2)}
.dl{font-size:10px;color:var(--muted)}
.legend{display:flex;gap:16px;font-size:12px;color:var(--muted);margin-top:4px}
.dot{display:inline-block;width:10px;height:10px;border-radius:3px;vertical-align:middle;margin-right:5px}
/* Top fotos */
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-top:6px}
.pcard{border:1px solid var(--line);border-radius:12px;overflow:hidden;background:#fff}
.pcard img{width:100%;aspect-ratio:3/2;object-fit:cover;display:block}
.pcard .pb{padding:8px 10px}
.pcard .code{font-size:12px;color:var(--muted)}
.pcard .metric{font-weight:800;font-size:15px}
.track{height:6px;background:#eef1f7;border-radius:99px;margin-top:6px;overflow:hidden}
.track > i{display:block;height:100%;background:linear-gradient(90deg,var(--brand),var(--brand2));border-radius:99px}
.empty{text-align:center;padding:40px;color:var(--muted)}
</style>

<div class="pagehead">
  <div>
    <a href="{{ route('admin.analytics.index') }}" class="btn ghost sm">← Analítica</a>
    <h1 style="margin-top:10px">{{ $event->name }}</h1>
    <div class="muted">{{ $event->event_date? $event->event_date->format('d/m/Y').' · ' : '' }}Actividad de la galería</div>
  </div>
  <div class="sp"></div>
  <a href="{{ $event->galleryUrl() }}" target="_blank" class="btn ghost sm">Abrir galería ↗</a>
</div>

<div class="stats">
  <div class="stat"><div class="n">{{ number_format($visitors) }}</div><div class="l">Visitantes únicos</div></div>
  <div class="stat"><div class="n">{{ number_format($visits) }}</div><div class="l">Visitas totales</div></div>
  <div class="stat"><div class="n">{{ number_format($previews) }}</div><div class="l">Fotos previsualizadas</div></div>
  <div class="stat"><div class="n">{{ number_format($orders) }}</div><div class="l">Pedidos</div></div>
</div>

<div class="card" style="margin-bottom:18px">
  <h2>Actividad de los últimos 14 días</h2>
  <div class="chart">
    @foreach($days as $d)
      <div class="day">
        <div class="bars">
          <div class="bar"   style="height:{{ $maxDay>0 ? round($d['views']/$maxDay*118) : 0 }}px" title="{{ $d['views'] }} visitas"></div>
          <div class="bar p" style="height:{{ $maxDay>0 ? round($d['previews']/$maxDay*118) : 0 }}px" title="{{ $d['previews'] }} previsualizaciones"></div>
        </div>
        <div class="dl">{{ \Illuminate\Support\Carbon::parse($d['date'])->format('d/m') }}</div>
      </div>
    @endforeach
  </div>
  <div class="legend">
    <span><i class="dot" style="background:var(--brand)"></i>Visitas a la galería</span>
    <span><i class="dot" style="background:var(--brand2)"></i>Previsualizaciones de fotos</span>
  </div>
</div>

<div class="card">
  <h2>Fotos con más interés</h2>
  <p class="muted" style="font-size:13px;margin-top:-6px">Las que tus clientes abrieron más veces para verlas en grande. Buenas candidatas para destacar o imprimir.</p>
  @if($topPhotos->isEmpty())
    <div class="empty">Todavía no hay previsualizaciones registradas para este evento.<br>En cuanto tus clientes abran fotos en grande, aparecerán aquí ordenadas por interés.</div>
  @else
    <div class="grid">
      @foreach($topPhotos as $t)
        <div class="pcard">
          <img src="{{ $t['photo']->thumbUrl() }}" alt="{{ $t['photo']->code }}" loading="lazy">
          <div class="pb">
            <div class="code">{{ $t['photo']->code }}</div>
            <div class="metric">{{ number_format($t['views']) }} vistas</div>
            <div class="code">{{ number_format($t['people']) }} personas</div>
            <div class="track"><i style="width:{{ round($t['views']/$maxPhotoViews*100) }}%"></i></div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

@endsection
