<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Pedido {{ $order->code }} · {{ $event->name }}</title>
<style>
:root{--bg:#0e1015;--panel:#161a22;--panel2:#1d222c;--line:#2a3140;--txt:#eef1f6;--muted:#9aa4b5;--brand:#7c5cff;--brand2:#00d1b2;--gold:#e8c17a;--danger:#e5484d;--yape:#742284}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--txt);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif}
.wrap{max-width:660px;margin:0 auto;padding:0 16px}
header.top{border-bottom:1px solid var(--line)}
.top .wrap{display:flex;align-items:center;gap:12px;height:60px}
.brand{display:flex;align-items:center;gap:10px;font-weight:700}
.logo{width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,var(--brand),var(--brand2));display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800}
.card{background:var(--panel);border:1px solid var(--line);border-radius:16px;padding:22px;margin-top:20px}
.ring{width:54px;height:54px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 10px}
.ring.ok{background:rgba(0,209,178,.15);border:2px solid var(--brand2);color:var(--brand2)}
.ring.wait{background:rgba(232,193,122,.15);border:2px solid var(--gold);color:var(--gold)}
.ring.bad{background:rgba(229,72,77,.14);border:2px solid var(--danger);color:var(--danger)}
h2{text-align:center;margin:0 0 4px;font-size:22px}
.sub{text-align:center;color:var(--muted);font-size:14px;margin-bottom:16px}
.badge{display:inline-block;font-size:12px;border-radius:999px;padding:4px 12px}
.badge.pend{background:rgba(232,193,122,.14);color:var(--gold);border:1px solid rgba(232,193,122,.3)}
.badge.rev{background:rgba(124,92,255,.16);color:#b7a6ff;border:1px solid rgba(124,92,255,.35)}
.badge.ok{background:rgba(0,209,178,.16);color:var(--brand2);border:1px solid rgba(0,209,178,.35)}
.badge.bad{background:rgba(229,72,77,.14);color:#ff8a8d;border:1px solid rgba(229,72,77,.3)}
.kv{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--line);font-size:14px}
.kv b{color:var(--gold)}
.tot{font-size:20px;font-weight:800;padding-top:14px}.tot b{color:var(--gold)}
.thumbs{display:grid;grid-template-columns:repeat(5,1fr);gap:6px;margin:14px 0 4px}
.thumbs img{width:100%;aspect-ratio:1;object-fit:cover;border-radius:7px}
.flash{background:rgba(0,209,178,.12);border:1px solid rgba(0,209,178,.35);color:var(--brand2);border-radius:10px;padding:11px 14px;font-size:14px;margin-top:14px}
.err{background:rgba(229,72,77,.12);border:1px solid rgba(229,72,77,.35);color:#ff8a8d;border-radius:10px;padding:11px 14px;font-size:14px;margin-top:14px}
/* Yape box */
.yapebox{background:var(--panel2);border:1px solid var(--line);border-radius:14px;padding:18px;margin-top:16px;text-align:center}
.yapehead{display:inline-flex;align-items:center;gap:8px;background:var(--yape);color:#fff;font-weight:800;border-radius:10px;padding:7px 14px;font-size:14px}
.qr{width:210px;max-width:70%;border-radius:12px;margin:12px auto 6px;display:block;background:#fff;padding:8px}
.paytip{background:rgba(124,92,255,.12);border:1px solid rgba(124,92,255,.3);color:#c9bcff;border-radius:10px;padding:11px 13px;font-size:13px;line-height:1.5;margin:14px 0 2px;text-align:left}
.numrow{display:flex;align-items:center;justify-content:center;gap:10px;margin-top:14px;flex-wrap:wrap}
.copybtn{background:var(--yape);color:#fff;border:none;border-radius:11px;padding:12px 15px;font-weight:800;font-size:14px;cursor:pointer;flex:none;white-space:nowrap}
.copybtn:active{transform:scale(.98)}
.qrwrap{margin-top:14px;text-align:center}
.qrwrap>summary{color:#b7a6ff;font-size:13px;list-style:none}
.qrwrap>summary::-webkit-details-marker{display:none}
.payacc{color:var(--muted);font-size:13px}
.payamt{margin-top:10px;font-size:15px}.payamt b{color:var(--gold);font-size:20px}
/* --- Pago en 1 pantalla: número y botón en la MISMA fila, sin textos de relleno --- */
.payttl{text-align:center;font-size:22px;margin:0 0 2px}.payttl b{color:var(--gold)}
.paybox{display:flex;align-items:center;gap:12px;text-align:left;
  background:var(--panel2);border:1px solid rgba(124,92,255,.45);border-radius:16px;padding:12px 14px;margin-top:12px}
.payl{flex:1;min-width:0;overflow:hidden}
.paylbl{color:var(--muted);font-size:11px;letter-spacing:.06em;text-transform:uppercase}
/* nowrap: el número nunca se parte en dos líneas. Si el fotógrafo guarda el número con
   espacios o con prefijo (+51 ...) no cabe al lado del botón, así que ajustaFuente() lo
   achica hasta que entre. El clamp es sólo el punto de partida y el respaldo sin JS. */
.paynum{font-size:clamp(19px,6vw,26px);font-weight:800;letter-spacing:.02em;margin:1px 0;line-height:1.15;white-space:nowrap}
.payacc{color:var(--muted);font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
/* En pantallas angostas el botón cede ancho para que el número siga grande */
@media(max-width:375px){.copybtn{padding:11px 12px;font-size:13px}}
/* Teléfonos muy angostos: al lado del botón ya no queda hueco para el número,
   así que se vuelve a apilar. La fila de una línea es para donde SÍ entra. */
@media(max-width:340px){
  .paybox{flex-direction:column;align-items:stretch;text-align:center}
  .copybtn{width:100%}
}
.qrlink{display:block;width:100%;background:none;border:none;color:#b7a6ff;font-size:13px;cursor:pointer;margin-top:10px;padding:4px}
/* --- Pantalla de espera: UN solo mensaje, con las fotos dentro --- */
.revttl{text-align:center;font-size:24px;margin:0 0 6px}
.revbox{background:var(--panel2);border:1px solid var(--line);border-radius:16px;padding:20px 16px;margin-top:18px;text-align:center}
.revttl2{font-size:18px;font-weight:800;margin-bottom:6px}
.revtxt{color:var(--muted);font-size:15px;line-height:1.6;margin:0}.revtxt b{color:var(--txt)}
.picks{display:flex;flex-wrap:wrap;justify-content:center;gap:12px;margin-top:16px}
.pick{width:104px}
.picklbl{display:flex;align-items:center;justify-content:center;gap:6px;font-size:12px;color:var(--muted);margin-bottom:5px}
.picknum{background:var(--line);color:var(--txt);border-radius:6px;min-width:18px;padding:1px 5px;font-weight:700;font-size:11px}
.pick img{width:100%;aspect-ratio:1;object-fit:cover;border-radius:12px;display:block}
.pickmas{color:var(--muted);font-size:12px;margin-top:12px;text-align:center}
/* Variante compacta de la pantalla de pago: el número va ENCIMA de la miniatura */
.picks.mini{gap:8px;margin-top:12px}
.picks.mini .pick{width:62px;position:relative}
.picks.mini .picklbl{font-size:10px;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block}
.picks.mini img{border-radius:9px}
.picks.mini .picknum{position:absolute;left:4px;bottom:4px;background:rgba(0,0,0,.62);font-size:10px;padding:0 5px;border-radius:5px}
.qrblock{margin-top:14px;background:#fff;border-radius:14px;padding:14px 12px}
.qrblock .qrttl{color:#111;font-weight:700;font-size:14px;margin:0 0 4px}
.qrblock .qr{width:240px;max-width:78%;margin:8px auto 4px;padding:6px}
.qrbtns{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-top:10px}
.qrbtns .btn{max-width:220px}
.qrhint{color:#555;font-size:12px;line-height:1.5;margin-top:10px}
/* QR a pantalla completa (captura limpia para escanear) */
.qrfull{display:none;position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.72);align-items:center;justify-content:center;padding:16px}
.qrfull.open{display:flex}
.qrfull-card{background:#fff;color:#111;border-radius:20px;padding:24px 20px;max-width:420px;width:100%;text-align:center}
.qrfull-title{font-weight:800;font-size:18px;color:#111;margin:0 0 14px}
.qrfull-img{width:min(80vw,340px);height:auto;display:block;margin:0 auto;background:#fff;padding:6px;border-radius:10px}
.qrfull-amt{font-size:24px;font-weight:800;color:#111;margin-top:14px}
.qrfull-num{font-size:14px;color:#333;margin-top:2px}
.qrfull-hint{font-size:12px;color:#666;line-height:1.5;margin:14px 0 16px}
.qrfull-card .btn{width:100%;max-width:none}
.steps{margin:16px 0 0;padding-left:18px;color:var(--muted);font-size:13px;line-height:1.6;text-align:left}
.steps b{color:var(--txt)}
/* form */
.field{margin:12px 0}.field label{display:block;font-size:13px;color:var(--muted);margin-bottom:6px;font-weight:600}
.field input[type=text]{width:100%;padding:11px 12px;background:var(--panel2);border:1px solid var(--line);border-radius:10px;color:var(--txt);font-size:14px}
/* .field label ya declara display:block, así que este selector tiene que ser más específico */
.field label.filebox,.filebox{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;min-height:96px;
  border:2px dashed rgba(124,92,255,.55);border-radius:16px;padding:12px 14px;text-align:center;color:var(--txt);
  font-size:15px;font-weight:600;cursor:pointer;background:var(--panel2);line-height:1.35}
.filebox .fbico{font-size:24px;line-height:1}
.filebox .fbsub{font-size:11px;color:var(--muted);font-weight:500}
.filebox.has{color:var(--brand2);border-color:var(--brand2);border-style:solid}
.fhint{font-size:12px;color:var(--muted);margin-top:8px;line-height:1.5}
.opwrap{margin:10px 0 4px}
.opwrap>summary{color:var(--muted);font-size:12px;cursor:pointer;list-style:none}
.opwrap>summary::-webkit-details-marker{display:none}
.opwrap input[type=text]{width:100%;padding:11px 12px;background:var(--panel2);border:1px solid var(--line);border-radius:10px;color:var(--txt);font-size:14px}
.btn{display:block;width:100%;text-align:center;background:var(--brand);color:#fff;border:none;border-radius:10px;padding:14px;font-weight:800;margin-top:14px;text-decoration:none;cursor:pointer;font-size:15px}
.btn.gold{background:var(--gold);color:#221a08}
.btn.ghost{background:var(--panel2);color:var(--txt);border:1px solid var(--line)}
.btn.wa{background:#25d366;color:#0a2e18}
.eta{margin-top:12px;background:rgba(232,193,122,.12);border:1px solid rgba(232,193,122,.35);color:var(--gold);border-radius:10px;padding:9px 12px;font-size:13px;font-weight:600}
.dlgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px;margin-top:14px}
.dl{border:1px solid var(--line);border-radius:12px;overflow:hidden;background:var(--panel2)}
.dl img{width:100%;aspect-ratio:3/2;object-fit:cover;display:block}
.dl a{display:block;text-align:center;padding:9px;font-size:13px;font-weight:700;color:var(--brand2);text-decoration:none}
.note{margin-top:16px;background:var(--panel2);border:1px solid var(--line);border-radius:12px;padding:13px;font-size:13px;color:var(--muted);line-height:1.6}.note b{color:var(--txt)}
.dltip{margin-top:12px;background:rgba(0,209,178,.12);border:1px solid rgba(0,209,178,.35);color:var(--brand2);border-radius:10px;padding:11px 13px;font-size:13px;line-height:1.5;text-align:center;font-weight:600}
details{margin-top:12px}summary{cursor:pointer;color:var(--muted);font-size:13px}
footer{color:var(--muted);font-size:12px;text-align:center;padding:26px 0}
/* Pantalla de pago en celulares chicos: cada píxel cuenta para que entre sin deslizar */
@media(max-height:760px){
  .card{padding:16px;margin-top:12px}
  .payttl{font-size:20px}
  .sub{margin-bottom:8px}
  .btn{padding:12px;margin-top:10px}
  .field{margin:10px 0}
  .opwrap{margin:8px 0 0}
  footer{display:none}   /* decorativo: en una pantalla corta vale más no tener que deslizar */
}
</style>
</head>
<body>
<header class="top"><div class="wrap">
  <div class="brand"><div class="logo">JG</div> Joel Garate Fotografía</div>
</div></header>

<div class="wrap">
  <div class="card">
    @php
      $st         = $order->status;
      $porPagar   = in_array($st, ['pendiente','rechazado'], true);
      $enRevision = $st === 'comprobante';
      $nFotos     = (int) $order->photo_count;
      $lasFotos   = $nFotos === 1 ? '1 foto' : $nFotos.' fotos';
    @endphp

    @if($porPagar)
      {{-- Pantalla de PAGO: una sola cosa que hacer, sin adornos --}}
      <h2 class="payttl">Pagar pedido <b>{{ $event->currency }} {{ number_format($order->total,2) }}</b></h2>
      <div class="sub">{{ $lasFotos }} · Referencia {{ $order->code }}</div>
      @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
    @elseif($enRevision)
      {{-- Pantalla de ESPERA: el mensaje va UNA sola vez, no en dos cajas --}}
      <h2 class="revttl">⏳ ¡Pago en revisión!</h2>
      <div class="sub">Referencia <b style="color:var(--txt)">{{ $order->code }}</b> ·
        {{ $lasFotos }} · <b style="color:var(--gold)">{{ $event->currency }} {{ number_format($order->total,2) }}</b></div>
      @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
    @else
      <div class="ring {{ $st==='aprobado'?'ok':($st==='comprobante'?'wait':'ok') }}">
        {{ $st==='aprobado'?'✓':($st==='comprobante'?'⏳':'✓') }}
      </div>
      <h2>
        @if($st==='aprobado') ¡Pago aprobado!
        @else Comprobante recibido
        @endif
      </h2>
      <div class="sub">Referencia <b style="color:var(--txt)">{{ $order->code }}</b> ·
        <span class="badge {{ $st==='aprobado'?'ok':'rev' }}">{{ $order->statusLabel() }}</span>
      </div>

      @if(session('flash')==='comprobante')<div class="flash">¡Gracias! Recibimos tu comprobante. En cuanto confirmemos tu Yapeo te enviaremos tus fotos a tu WhatsApp. 💬</div>@endif
      @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif

      {{-- Resumen del pedido --}}
      <div class="kv"><span>Evento</span><span>{{ $event->name }}</span></div>
      <div class="kv"><span>Nombre</span><span>{{ $order->customer_name }}</span></div>
      <div class="kv"><span>Fotos</span><span>{{ $order->photo_count }}</span></div>
      @if($order->subtotal - $order->total > 0.001)
        <div class="kv" style="color:var(--brand2)"><span>{{ $order->applied_label ?: 'Descuento' }}</span><span>- {{ $event->currency }} {{ number_format($order->subtotal - $order->total,2) }}</span></div>
      @endif
      <div class="kv tot"><span>Total {{ $st==='aprobado'?'pagado':'a pagar' }}</span><b>{{ $event->currency }} {{ number_format($order->total,2) }}</b></div>
    @endif

    {{-- ====== ESTADO: APROBADO -> descargas ====== --}}
    @if($st==='aprobado')
      <p style="text-align:center;color:var(--muted);font-size:14px;margin:16px 0 0">Tus fotos en alta resolución, sin marca de agua, ya están listas para descargar. 🎉</p>
      <div class="dltip">📲 Una vez que termine la descarga, la foto se guarda directo en la galería de tu teléfono.</div>
      <div class="dlgrid">
        @foreach($order->items as $it)
          <div class="dl">
            @if($it->photo)<img src="{{ $it->photo->thumbUrl() }}" alt="{{ $it->code }}">@endif
            <a href="{{ route('gallery.download', ['slug'=>$event->slug,'code'=>$order->code,'item'=>$it->id,'t'=>$order->token]) }}">⬇ Descargar</a>
          </div>
        @endforeach
      </div>
      <div class="note">Descarga cada foto con el botón correspondiente. El archivo es el original en alta, sin marca de agua. Guarda este enlace para volver a descargar cuando quieras.</div>

    {{-- ====== ESTADO: COMPROBANTE (en revisión) ====== --}}
    @elseif($enRevision)
      {{-- Un solo mensaje, con las fotos dentro. Antes se repetía en la caja verde y aquí. --}}
      <div class="revbox">
        <div class="revttl2">¡Recibimos tu comprobante!</div>
        <p class="revtxt">Apenas validemos tu Yape, te enviaremos tus {{ $lasFotos }} sin marca de agua
          a tu WhatsApp <b>{{ $order->customer_contact }}</b> (5-15 min).</p>

        @php $verFotos = $order->items->take(6); @endphp
        <div class="picks">
          @foreach($verFotos as $i => $it)
            <div class="pick">
              <div class="picklbl"><span class="picknum">{{ $i+1 }}</span> {{ $it->code }}</div>
              @if($it->photo)<img src="{{ $it->photo->thumbUrl() }}" alt="{{ $it->code }}">@endif
            </div>
          @endforeach
        </div>
        @if($order->items->count() > $verFotos->count())
          <div class="pickmas">y {{ $order->items->count() - $verFotos->count() }} más</div>
        @endif

        @if($order->op_code)<div class="pickmas">Código de operación: <b style="color:var(--txt)">{{ $order->op_code }}</b></div>@endif
      </div>

      <details>
        <summary>¿Te equivocaste de comprobante? Enviar otro</summary>
        @include('gallery.partials.receipt-form')
      </details>

      @php
        $waNum = preg_replace('/\D/', '', $yape['number'] ?? '');
        if (strlen($waNum) === 9) { $waNum = '51'.$waNum; } // Perú
        $waMsg = 'Hola, ya subí mi comprobante '.$order->code.' para las fotos de "'.$event->name.'". Mi nombre: '.$order->customer_name.'.';
      @endphp
      @if($waNum)
        <a class="btn wa" href="https://wa.me/{{ $waNum }}?text={{ rawurlencode($waMsg) }}" target="_blank" rel="noopener">
          <span style="font-size:18px">💬</span> WhatsApp de Soporte
        </a>
      @endif

    {{-- ====== ESTADO: PENDIENTE / RECHAZADO -> pagar ====== --}}
    @else
      @if($st==='rechazado')
        <div class="err">Tu comprobante anterior no pudo validarse. Por favor verifica el Yapeo y vuelve a enviarlo.</div>
      @endif

      @php $qrurl = !empty($yape['qr_path']) ? \Illuminate\Support\Facades\Storage::disk(config('storage.public_disk'))->url($yape['qr_path']) : null; @endphp

      {{-- 1) El número y el botón de copiar, en UNA sola línea --}}
      @if(!empty($yape['number']))
        <div class="paybox">
          <div class="payl">
            <div class="paylbl">Yape al número</div>
            <div class="paynum" id="yapenum">{{ $yape['number'] }}</div>
            <div class="payacc">{{ $yape['account'] ?: 'Joel Garate Fotografía' }}</div>
          </div>
          <button type="button" class="copybtn" onclick="copyNum(this)">📋 Copiar número</button>
        </div>
      @endif

      {{-- 2) Subir la captura + la alternativa por WhatsApp: las dos formas de mandar lo mismo --}}
      @php
        $waNum = preg_replace('/\D/', '', $yape['number'] ?? '');
        if (strlen($waNum) === 9) { $waNum = '51'.$waNum; } // Perú
        $waMsg = 'Hola Joel, te envío la captura de mi Yape del pedido '.$order->code
               . ' ("'.$event->name.'"). Son '.$lasFotos.' por '
               . $event->currency.' '.number_format($order->total,2).'. Mi nombre: '.$order->customer_name.'.';
      @endphp
      @include('gallery.partials.receipt-form', $waNum ? [
        'waHref'   => 'https://wa.me/'.$waNum.'?text='.rawurlencode($waMsg),
        'waAvisar' => route('gallery.order.whatsapp', ['slug'=>$event->slug,'code'=>$order->code]).'?t='.$order->token,
      ] : [])

      {{-- 3) El QR queda a un toque, para quien paga desde otro celular --}}
      @if($qrurl)
        <button type="button" class="qrlink" onclick="openQR()">¿Pagas desde otro celular? Ver el QR</button>
      @endif
    @endif

    {{-- Miniaturas sólo en la pantalla de pago: en revisión ya van DENTRO del mensaje --}}
    @if($porPagar)
      @php $verFotos = $order->items->take(8); @endphp
      <div class="picks mini">
        @foreach($verFotos as $i => $it)
          <div class="pick">
            <div class="picklbl">{{ $it->code }}</div>
            @if($it->photo)<img src="{{ $it->photo->thumbUrl() }}" alt="{{ $it->code }}"><span class="picknum">{{ $i+1 }}</span>@endif
          </div>
        @endforeach
      </div>
      @if($order->items->count() > $verFotos->count())
        <div class="pickmas">y {{ $order->items->count() - $verFotos->count() }} más</div>
      @endif
    @endif

    <a href="{{ route('gallery.show', $event->slug) }}" class="btn ghost">{{ $enRevision ? '◀ Volver a la galería' : 'Seguir viendo la galería' }}</a>
  </div>
</div>
@php $qrurlFull = !empty($yape['qr_path']) ? \Illuminate\Support\Facades\Storage::disk(config('storage.public_disk'))->url($yape['qr_path']) : null; @endphp
@if($qrurlFull && $st!=='aprobado')
<div class="qrfull" id="qrFull" onclick="if(event.target===this)closeQR()">
  <div class="qrfull-card">
    <div class="qrfull-title">Escanea este código con Yape</div>
    <img class="qrfull-img" src="{{ $qrurlFull }}" alt="QR Yape">
    <div class="qrfull-amt">{{ $event->currency }} {{ number_format($order->total,2) }}</div>
    @if(!empty($yape['number']))<div class="qrfull-num">{{ $yape['number'] }} · {{ $yape['account'] ?: 'Joel Garate Fotografía' }}</div>@endif
    <div class="qrfull-hint">Si vas a subir el QR desde la galería de Yape, toma la captura de esta pantalla: se ve grande y limpio, sin nada alrededor.</div>
    <button type="button" class="btn ghost" onclick="closeQR()">Cerrar</button>
  </div>
</div>
@endif
<footer>FotoEvento · Joel Garate Fotografía</footer>
<script>
/* Botón verde: abre el chat de WhatsApp Y avisa al fotógrafo, porque el comprobante
   va a llegar a su celular y el panel no se enteraría solo. El aviso no puede frenar
   la apertura del chat: se manda con keepalive y el enlace sigue su curso. */
(function(){
  var a=document.getElementById('waDirect'); if(!a) return;
  var url=a.getAttribute('data-avisar'), meta=document.querySelector('meta[name=csrf-token]');
  var avisado=false;
  a.addEventListener('click',function(){
    if(avisado||!url||!meta) return;
    avisado=true;
    try{
      fetch(url,{method:'POST',keepalive:true,headers:{
        'X-CSRF-TOKEN':meta.content,'Accept':'application/json'
      }}).catch(function(){});
    }catch(e){}
  });
})();
/* El número tiene que caber al lado del botón sin desbordarse. Cuánto mide depende de
   cómo lo haya guardado el fotógrafo (con espacios, con +51) y del ancho del teléfono,
   así que se mide de verdad en vez de confiar en un tamaño fijo. */
(function(){
  var e=document.getElementById('yapenum'); if(!e) return;
  var MAX=26;   // el tamaño "gigante" que pidió el cliente; sólo se baja si no entra
  function ajustaFuente(){
    var px=MAX; e.style.fontSize=px+'px';
    while(e.scrollWidth>e.clientWidth && px>13){ px-=1; e.style.fontSize=px+'px'; }
  }
  ajustaFuente();
  window.addEventListener('resize', ajustaFuente);
  if(document.fonts && document.fonts.ready) document.fonts.ready.then(ajustaFuente);
})();
function openQR(){var m=document.getElementById('qrFull');if(m)m.classList.add('open');}
function closeQR(){var m=document.getElementById('qrFull');if(m)m.classList.remove('open');}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeQR();});
function copyNum(btn){
  var el=document.getElementById('yapenum');
  if(!el) return;
  var n=el.textContent.trim().replace(/\s+/g,'');
  var done=function(){var t=btn.textContent;btn.textContent='¡Copiado!';setTimeout(function(){btn.textContent=t},1600);};
  if(navigator.clipboard&&navigator.clipboard.writeText){
    navigator.clipboard.writeText(n).then(done).catch(function(){fallback(n,done);});
  }else{fallback(n,done);}
  function fallback(txt,cb){var i=document.createElement('textarea');i.value=txt;i.style.position='fixed';i.style.opacity='0';document.body.appendChild(i);i.focus();i.select();try{document.execCommand('copy');cb();}catch(e){}document.body.removeChild(i);}
}
</script>
</body>
</html>
