@extends('admin.layout')
@section('title','Eventos')
@section('content')
<div class="pagehead">
  <div>
    <h1>Eventos</h1>
    <div class="muted">Crea eventos ilimitados y sube las fotos de cada uno.</div>
  </div>
  <div class="sp"></div>
  <a href="{{ route('admin.events.create') }}" class="btn">+ Nuevo evento</a>
</div>

@if($events->isEmpty())
  <div class="card" style="text-align:center;padding:50px">
    <div style="font-size:40px">📷</div>
    <h2 style="margin-top:10px">Aún no tienes eventos</h2>
    <p class="muted">Empieza creando tu primer evento (promoción, graduación, etc.).</p>
    <a href="{{ route('admin.events.create') }}" class="btn" style="margin-top:8px">Crear mi primer evento</a>
  </div>
@else
  <div class="card" style="padding:6px 12px">
    <table>
      <thead><tr><th>Evento</th><th>Estado</th><th>Fecha</th><th>Fotos</th><th>Precio</th><th>PIN</th><th></th></tr></thead>
      <tbody>
      @foreach($events as $e)
        <tr>
          <td><a href="{{ route('admin.events.show',$e) }}" style="font-weight:700">{{ $e->name }}</a>
            <div class="muted" style="font-size:12px">/g/{{ $e->slug }}</div></td>
          <td><span class="badge {{ $e->published ? 'on' : 'wait' }}">{{ $e->published ? '● Activo' : '⏸ Pausado' }}</span></td>
          <td>{{ $e->event_date? $e->event_date->format('d/m/Y') : '—' }}</td>
          <td>{{ $e->photos_count }}</td>
          <td>{{ $e->currency }} {{ number_format($e->price_unit,2) }}</td>
          <td>{!! $e->pin ? '<span class="badge on">'.$e->pin.'</span>' : '<span class="badge">sin PIN</span>' !!}</td>
          <td style="text-align:right"><a href="{{ route('admin.events.show',$e) }}" class="btn ghost sm">Administrar</a></td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
@endif
@endsection
