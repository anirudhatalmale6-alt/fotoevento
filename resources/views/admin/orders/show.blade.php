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
  @php
    $on = $order->status==='aprobado';
    $cls = $order->status==='aprobado' ? 'on' : ($order->status==='rechazado' ? 'bad' : '');
  @endphp
  <span class="badge {{ $on?'on':'' }}" style="font-size:14px;padding:8px 14px">{{ $order->statusLabel() }}</span>
</div>

<div class="row">
  <div class="col card" style="min-width:260px">
    <h2>Cliente</h2>
    <div class="kv"><b>Nombre:</b> {{ $order->customer_name }}</div>
    <div class="kv"><b>WhatsApp / Celular:</b> {{ $order->customer_contact }}</div>
    @if($order->customer_email)<div class="kv"><b>Correo:</b> {{ $order->customer_email }}</div>@endif
    <a href="https://wa.me/{{ $order->whatsappNumber() }}" target="_blank" class="btn sm" style="margin-top:12px">Escribir por WhatsApp</a>
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

{{-- ====== Enlace para el cliente ====== --}}
@php
  $msg = $order->isApproved()
    ? "¡Hola {$order->customer_name}! Tu pago del pedido {$order->code} fue confirmado. Aquí puedes descargar tus fotos en alta resolución, sin marca de agua: {$order->clientUrl()}"
    : "¡Hola {$order->customer_name}! Aquí puedes ver tu pedido {$order->code} y completar tu pago con Yape: {$order->clientUrl()}";
  $wa = 'https://wa.me/'.$order->whatsappNumber().'?text='.rawurlencode($msg);
@endphp
<div class="card" style="margin-top:16px">
  <h2>Enlace del cliente {{ $order->isApproved() ? '(para descargar)' : '' }}</h2>
  <p class="muted" style="font-size:13px;margin-top:0">
    @if($order->isApproved())
      El pago está aprobado. Envíale este enlace al cliente por WhatsApp para que descargue sus fotos en alta, sin marca de agua. Es su enlace personal y privado.
    @else
      Este es el enlace personal del cliente para ver su pedido, pagar y hacer seguimiento. Puedes enviárselo por WhatsApp.
    @endif
  </p>
  <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
    <input id="clientlink" readonly value="{{ $order->clientUrl() }}" style="flex:1;min-width:240px">
    <button type="button" class="btn ghost sm" onclick="navigator.clipboard.writeText(document.getElementById('clientlink').value); this.textContent='¡Copiado!'; setTimeout(()=>this.textContent='Copiar',1500)">Copiar</button>
    <a href="{{ $wa }}" target="_blank" class="btn sm" style="background:#25d366">Enviar por WhatsApp</a>
  </div>
</div>

{{-- ====== Pago con Yape ====== --}}
<div class="card" style="margin-top:16px">
  <h2>Pago con Yape</h2>

  @if($order->status==='aprobado')
    <div class="flash" style="margin:0 0 12px">Pago aprobado el {{ $order->approved_at?->format('d/m/Y H:i') }}. El cliente ya puede descargar sus fotos en alta.</div>
  @elseif($order->status==='pendiente')
    <p class="muted" style="font-size:14px;margin:0">El cliente aún no ha enviado su comprobante. Cuando pague con Yape y suba la captura, aparecerá aquí para que la revises y apruebes.</p>
  @endif

  @if($order->hasReceipt() || $order->op_code)
    <div class="row" style="align-items:flex-start">
      @if($order->hasReceipt())
        <div class="col" style="min-width:200px;max-width:260px">
          <label style="margin-bottom:8px">Comprobante enviado</label>
          <a href="{{ route('admin.orders.receipt',$order) }}" target="_blank">
            <img src="{{ route('admin.orders.receipt',$order) }}" alt="Comprobante" style="width:100%;border:1px solid var(--line);border-radius:10px;display:block">
          </a>
          <a href="{{ route('admin.orders.receipt',$order) }}" target="_blank" class="muted" style="font-size:12px;display:block;margin-top:6px">Abrir en tamaño completo ↗</a>
        </div>
      @endif
      <div class="col" style="min-width:200px">
        @if($order->op_code)<div class="kv"><b>Código de operación:</b> {{ $order->op_code }}</div>@endif
        @if($order->paid_at)<div class="kv"><b>Comprobante recibido:</b> {{ $order->paid_at->format('d/m/Y H:i') }}</div>@endif

        @if($order->status!=='aprobado')
          <form method="post" action="{{ route('admin.orders.approve',$order) }}" style="margin-top:14px">@csrf
            <button class="btn" style="background:#0b8f6f">✓ Aprobar y liberar descarga</button>
          </form>
          <form method="post" action="{{ route('admin.orders.reject',$order) }}" style="margin-top:8px"
                onsubmit="return confirm('¿Marcar este comprobante como rechazado? El cliente podrá enviar otro.')">@csrf
            <button class="btn danger sm">Rechazar comprobante</button>
          </form>
        @else
          <form method="post" action="{{ route('admin.orders.reject',$order) }}" style="margin-top:14px"
                onsubmit="return confirm('¿Revertir la aprobación de este pedido?')">@csrf
            <button class="btn ghost sm">Revertir aprobación</button>
          </form>
        @endif
      </div>
    </div>
  @endif
</div>

<div class="card" style="margin-top:16px;border-color:#f6c9cb">
  <h2>Eliminar pedido</h2>
  <p class="muted" style="font-size:13px;margin-top:0">Quita este pedido de tu panel (por ejemplo, si fue una prueba). No borra las fotos ni el evento, solo el pedido y su comprobante.</p>
  <form method="post" action="{{ route('admin.orders.destroy',$order) }}"
        onsubmit="return confirm('¿Eliminar el pedido {{ $order->code }}? Esta acción no se puede deshacer.')">
    @csrf @method('DELETE')
    <button class="btn danger sm">🗑️ Eliminar este pedido</button>
  </form>
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
@endsection
