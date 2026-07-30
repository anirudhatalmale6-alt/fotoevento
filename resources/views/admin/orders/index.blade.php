@extends('admin.layout')
@section('title','Pedidos')
@section('content')
<div class="pagehead">
  <div>
    <h1>Pedidos</h1>
    <div class="muted">Selecciones de fotos que hicieron tus clientes desde las galerías.</div>
  </div>
  <div class="sp"></div>
  @unless($orders->isEmpty())
    <form method="post" action="{{ route('admin.orders.destroyAll') }}" style="display:inline"
          onsubmit="return confirm('¿Eliminar TODOS los pedidos y empezar de cero? La numeración volverá a FE-0001. Esto no borra tus fotos ni tus eventos, solo los pedidos. Esta acción no se puede deshacer.')">
      @csrf @method('DELETE')
      <button class="btn danger">🗑️ Vaciar todos los pedidos</button>
    </form>
  @endunless
  <a href="{{ route('admin.events.index') }}" class="btn ghost">Eventos</a>
</div>

@if($orders->isEmpty())
  <div class="card" style="text-align:center;padding:50px">
    <div style="font-size:40px">🧾</div>
    <h2 style="margin-top:10px">Aún no hay pedidos</h2>
    <p class="muted">Cuando un cliente seleccione fotos y confirme su pedido en una galería, aparecerá aquí.</p>
  </div>
@else
  <div class="card" style="padding:6px 12px">
    <table>
      <thead><tr><th>Ref.</th><th>Cliente</th><th>Contacto</th><th>Evento</th><th>Fotos</th><th>Total</th><th>Estado</th><th></th></tr></thead>
      <tbody>
      @foreach($orders as $o)
        <tr>
          <td><a href="{{ route('admin.orders.show',$o) }}" style="font-weight:700">{{ $o->code }}</a>
            <div class="muted" style="font-size:12px">{{ $o->created_at->format('d/m/Y H:i') }}</div></td>
          <td>{{ $o->customer_name }}</td>
          <td>{{ $o->customer_contact }}</td>
          <td class="muted">{{ $o->event?->name }}</td>
          <td>{{ $o->photo_count }}</td>
          <td style="font-weight:700">{{ $o->event?->currency }} {{ number_format($o->total,2) }}</td>
          <td><span class="badge {{ $o->statusBadge() }}">{{ $o->statusLabel() }}</span></td>
          <td style="text-align:right;white-space:nowrap">
            <a href="{{ route('admin.orders.show',$o) }}" class="btn ghost sm">Ver</a>
            <form method="post" action="{{ route('admin.orders.destroy',$o) }}" style="display:inline"
                  onsubmit="return confirm('¿Eliminar el pedido {{ $o->code }}? Esta acción no se puede deshacer.')">
              @csrf @method('DELETE')
              <button class="btn danger sm" title="Eliminar pedido">Eliminar</button>
            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  @if($orders->hasPages())<div style="margin-top:14px">{{ $orders->links() }}</div>@endif
@endif
@endsection
