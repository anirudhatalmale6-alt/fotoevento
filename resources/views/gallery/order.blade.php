<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pedido {{ $order->code }} · {{ $event->name }}</title>
<style>
:root{--bg:#0e1015;--panel:#161a22;--panel2:#1d222c;--line:#2a3140;--txt:#eef1f6;--muted:#9aa4b5;--brand:#7c5cff;--brand2:#00d1b2;--gold:#e8c17a}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--txt);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif}
.wrap{max-width:640px;margin:0 auto;padding:0 16px}
header.top{border-bottom:1px solid var(--line)}
.top .wrap{display:flex;align-items:center;gap:12px;height:60px}
.brand{display:flex;align-items:center;gap:10px;font-weight:700}
.logo{width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,var(--brand),var(--brand2));display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800}
.card{background:var(--panel);border:1px solid var(--line);border-radius:16px;padding:22px;margin-top:24px}
.ring{width:56px;height:56px;border-radius:50%;background:rgba(0,209,178,.15);border:2px solid var(--brand2);color:var(--brand2);display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 12px}
h2{text-align:center;margin:0 0 4px;font-size:22px}
.sub{text-align:center;color:var(--muted);font-size:14px;margin-bottom:18px}
.kv{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--line);font-size:14px}
.kv b{color:var(--gold)}
.tot{font-size:20px;font-weight:800;padding-top:14px}.tot b{color:var(--gold)}
.badge{display:inline-block;font-size:12px;background:rgba(232,193,122,.14);color:var(--gold);border:1px solid rgba(232,193,122,.3);border-radius:999px;padding:4px 12px}
.thumbs{display:grid;grid-template-columns:repeat(5,1fr);gap:6px;margin:14px 0 4px}
.thumbs img{width:100%;aspect-ratio:1;object-fit:cover;border-radius:7px}
.next{margin-top:18px;background:var(--panel2);border:1px solid var(--line);border-radius:12px;padding:14px;font-size:13px;color:var(--muted);line-height:1.6}
.next b{color:var(--txt)}
.btn{display:block;text-align:center;background:var(--brand);color:#fff;border:none;border-radius:10px;padding:13px;font-weight:800;margin-top:16px;text-decoration:none}
</style>
</head>
<body>
<header class="top"><div class="wrap">
  <div class="brand"><div class="logo">JG</div> Joel Garate Fotografía</div>
</div></header>

<div class="wrap">
  <div class="card">
    <div class="ring">✓</div>
    <h2>¡Pedido registrado!</h2>
    <div class="sub">Referencia <b style="color:var(--txt)">{{ $order->code }}</b> · <span class="badge">Pendiente de pago</span></div>

    <div class="kv"><span>Evento</span><span>{{ $event->name }}</span></div>
    <div class="kv"><span>Nombre</span><span>{{ $order->customer_name }}</span></div>
    <div class="kv"><span>WhatsApp / Celular</span><span>{{ $order->customer_contact }}</span></div>
    <div class="kv"><span>Fotos seleccionadas</span><span>{{ $order->photo_count }}</span></div>
    <div class="kv"><span>Subtotal</span><span>{{ $event->currency }} {{ number_format($order->subtotal,2) }}</span></div>
    @if($order->subtotal - $order->total > 0.001)
      <div class="kv" style="color:var(--brand2)"><span>{{ $order->applied_label ?: 'Descuento' }}</span><span>- {{ $event->currency }} {{ number_format($order->subtotal - $order->total,2) }}</span></div>
    @endif
    <div class="kv tot"><span>Total a pagar</span><b>{{ $event->currency }} {{ number_format($order->total,2) }}</b></div>

    <div class="thumbs">
      @foreach($order->items->take(10) as $it)
        @if($it->photo)<img src="{{ $it->photo->thumbUrl() }}" alt="{{ $it->code }}">@endif
      @endforeach
    </div>

    <div class="next">
      <b>Siguiente paso:</b> el pago con Yape se habilita muy pronto. Cuando confirmes el Yapeo, Joel aprobará tu pedido desde su panel y se activará la descarga de tus fotos en alta resolución, <b>sin marca de agua</b>.
      Guarda tu referencia <b>{{ $order->code }}</b> para cualquier consulta.
    </div>

    <a href="{{ route('gallery.show', $event->slug) }}" class="btn">Volver a la galería</a>
  </div>
</div>
</body>
</html>
