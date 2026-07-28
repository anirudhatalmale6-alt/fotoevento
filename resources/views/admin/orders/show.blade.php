@extends('admin.layout')
@section('title','Pedido '.$order->code)
@section('content')
<div class="pagehead">
  <div>
    <a href="{{ route('admin.orders.index') }}" class="btn ghost sm">← Pedidos</a>
    <h1 style="margin-top:10px">Pedido {{ $order->code }}</h1>
    <div class="muted">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->event?->name }}</div>
  </div>
  <div class="sp"></div>
  @php $on = $order->status==='pagado'||$order->status==='entregado'; @endphp
  <span class="badge {{ $on?'on':'' }}" style="font-size:14px;padding:8px 14px">{{ $order->statusLabel() }}</span>
</div>

<div class="row">
  <div class="col card" style="min-width:260px">
    <h2>Cliente</h2>
    <div class="kv"><b>Nombre:</b> {{ $order->customer_name }}</div>
    <div class="kv"><b>WhatsApp / Celular:</b> {{ $order->customer_contact }}</div>
    @if($order->customer_email)<div class="kv"><b>Correo:</b> {{ $order->customer_email }}</div>@endif
    <a href="https://wa.me/{{ preg_replace('/\D/','',$order->customer_contact) }}" target="_blank" class="btn sm" style="margin-top:12px">Escribir por WhatsApp</a>
  </div>

  <div class="col card" style="min-width:260px">
    <h2>Resumen</h2>
    <div class="kv"><b>Fotos:</b> {{ $order->photo_count }}</div>
    <div class="kv"><b>Subtotal:</b> {{ $order->event?->currency }} {{ number_format($order->subtotal,2) }}</div>
    @if($order->subtotal - $order->total > 0.001)
      <div class="kv" style="color:#0b8f6f"><b>{{ $order->applied_label ?: 'Descuento' }}:</b> - {{ $order->event?->currency }} {{ number_format($order->subtotal - $order->total,2) }}</div>
    @endif
    <div class="kv" style="font-size:18px;font-weight:800;margin-top:8px"><b>Total:</b> {{ $order->event?->currency }} {{ number_format($order->total,2) }}</div>
  </div>
</div>

<div class="card" style="margin-top:16px">
  <h2>Fotos del pedido ({{ $order->items->count() }})</h2>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:10px;margin-top:6px">
    @foreach($order->items as $it)
      <div style="border:1px solid var(--line);border-radius:10px;overflow:hidden">
        @if($it->photo)
          <img src="{{ $it->photo->thumbUrl() }}" style="width:100%;aspect-ratio:3/2;object-fit:cover;display:block" alt="{{ $it->code }}">
        @endif
        <div style="padding:6px 8px;font-size:12px;color:var(--muted)">{{ $it->code }}</div>
      </div>
    @endforeach
  </div>
</div>

<div class="card" style="margin-top:16px;background:#fbfbfe">
  <h2>Pago y entrega</h2>
  <p class="muted" style="font-size:14px;line-height:1.6;margin:0">
    En el <b>Hito 3</b> se activará aquí el pago con Yape (tu QR + número), el cliente subirá su comprobante y tú podrás
    <b>aprobar el pago</b> y <b>liberar la descarga</b> en alta resolución con un botón. Por ahora el pedido queda registrado
    en estado “Pendiente” para que veas que la selección del cliente llega correctamente.
  </p>
</div>
@endsection
