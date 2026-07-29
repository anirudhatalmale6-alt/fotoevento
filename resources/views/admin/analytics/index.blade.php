@extends('admin.layout')
@section('title','Analítica')
@section('content')

<style>
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px}
@media(max-width:820px){.stats{grid-template-columns:repeat(2,1fr)}}
.stat{background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px}
.stat .n{font-size:28px;font-weight:800;line-height:1}
.stat .l{color:var(--muted);font-size:13px;margin-top:6px}
.stat .s{font-size:12px;color:var(--muted);margin-top:2px}
.funnel{display:flex;gap:10px;flex-wrap:wrap;align-items:stretch;margin-top:6px}
.fstep{flex:1;min-width:150px;background:#f7f8fc;border:1px solid var(--line);border-radius:12px;padding:14px}
.fstep .fn{font-size:22px;font-weight:800}
.fstep .fl{color:var(--muted);font-size:13px}
.fstep .fp{font-size:12px;color:var(--brand);font-weight:700;margin-top:4px}
.trow a{color:var(--brand);font-weight:700}
.mini{font-size:12px;color:var(--muted)}
.empty{text-align:center;padding:40px;color:var(--muted)}
</style>

<div class="pagehead">
  <div>
    <h1>Analítica</h1>
    <div class="muted">Mide cuánta gente entra a tus galerías y qué fotos generan más interés.</div>
  </div>
</div>

<div class="stats">
  <div class="stat"><div class="n">{{ number_format($totals['visitors']) }}</div><div class="l">Visitantes únicos</div><div class="s">{{ number_format($totals['visits']) }} visitas en total</div></div>
  <div class="stat"><div class="n">{{ number_format($totals['previews']) }}</div><div class="l">Fotos previsualizadas</div><div class="s">clics para ver en grande</div></div>
  <div class="stat"><div class="n">{{ number_format($totals['orders']) }}</div><div class="l">Pedidos</div><div class="s">selecciones confirmadas</div></div>
  <div class="stat"><div class="n">{{ $totals['conv']!==null ? $totals['conv'].'%' : '—' }}</div><div class="l">Conversión</div><div class="s">visitantes que pidieron</div></div>
</div>

<div class="card" style="margin-bottom:18px">
  <h2>Embudo general</h2>
  <p class="mini" style="margin-top:-6px">De cada visitante, ¿cuántos llegan a previsualizar y cuántos terminan pidiendo? Así ves dónde se están yendo.</p>
  @php
    $v = $totals['visitors']; $p = $totals['previews']; $o = $totals['orders'];
    $pctP = $v>0 ? round(min(100,$p/$v*100)) : 0;
    $pctO = $v>0 ? round($o/$v*100,1) : 0;
  @endphp
  <div class="funnel">
    <div class="fstep"><div class="fn">{{ number_format($v) }}</div><div class="fl">Entraron a la galería</div><div class="fp">100%</div></div>
    <div class="fstep"><div class="fn">{{ number_format($p) }}</div><div class="fl">Previsualizaciones de fotos</div><div class="fp">{{ $pctP }}% de las visitas</div></div>
    <div class="fstep"><div class="fn">{{ number_format($o) }}</div><div class="fl">Hicieron un pedido</div><div class="fp">{{ $pctO }}% de los visitantes</div></div>
  </div>
</div>

<div class="card">
  <h2>Por evento</h2>
  @if($rows->isEmpty())
    <div class="empty">Aún no tienes eventos. Crea uno y comparte su galería para empezar a medir.</div>
  @else
    <div style="overflow-x:auto">
    <table>
      <thead><tr>
        <th>Evento</th><th>Visitantes</th><th>Visitas</th><th>Previsualiz.</th><th>Pedidos</th><th>Conversión</th><th></th>
      </tr></thead>
      <tbody>
      @foreach($rows as $r)
        <tr class="trow">
          <td>
            <a href="{{ route('admin.analytics.show', $r['event']) }}">{{ $r['event']->name }}</a>
            <div class="mini">{{ $r['event']->event_date? $r['event']->event_date->format('d/m/Y') : '' }}</div>
          </td>
          <td><b>{{ number_format($r['visitors']) }}</b></td>
          <td>{{ number_format($r['visits']) }}</td>
          <td>{{ number_format($r['previews']) }}<div class="mini">{{ number_format($r['prev_visitors']) }} personas</div></td>
          <td>{{ number_format($r['orders']) }}</td>
          <td>{{ $r['conv']!==null ? $r['conv'].'%' : '—' }}</td>
          <td style="text-align:right"><a href="{{ route('admin.analytics.show', $r['event']) }}" class="btn ghost sm">Ver detalle</a></td>
        </tr>
      @endforeach
      </tbody>
    </table>
    </div>
  @endif
</div>

<p class="mini" style="margin-top:14px">Los datos son 100% tuyos y anónimos: se cuenta cada dispositivo con un identificador aleatorio, sin nombres, correos ni cookies de terceros.</p>

@endsection
