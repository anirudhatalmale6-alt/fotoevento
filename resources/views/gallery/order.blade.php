<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
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
.copybtn{background:var(--yape);color:#fff;border:none;border-radius:9px;padding:9px 15px;font-weight:700;font-size:13px;cursor:pointer}
.copybtn:active{transform:scale(.97)}
.qrwrap{margin-top:14px;text-align:center}
.qrwrap>summary{color:#b7a6ff;font-size:13px;list-style:none}
.qrwrap>summary::-webkit-details-marker{display:none}
.paynum{font-size:26px;font-weight:800;letter-spacing:.03em;margin:0}
.payacc{color:var(--muted);font-size:13px}
.payamt{margin-top:10px;font-size:15px}.payamt b{color:var(--gold);font-size:20px}
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
.filebox{border:1px dashed var(--line);border-radius:12px;padding:16px;text-align:center;color:var(--muted);font-size:14px;cursor:pointer;background:var(--panel2)}
.filebox.has{color:var(--brand2);border-color:var(--brand2)}
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
</style>
</head>
<body>
<header class="top"><div class="wrap">
  <div class="brand"><div class="logo">JG</div> Joel Garate Fotografía</div>
</div></header>

<div class="wrap">
  <div class="card">
    @php $st = $order->status; @endphp
    <div class="ring {{ $st==='aprobado'?'ok':($st==='rechazado'?'bad':($st==='comprobante'?'wait':'ok')) }}">
      {{ $st==='aprobado'?'✓':($st==='rechazado'?'!':($st==='comprobante'?'⏳':'✓')) }}
    </div>
    <h2>
      @if($st==='aprobado') ¡Pago aprobado!
      @elseif($st==='comprobante') Comprobante recibido
      @elseif($st==='rechazado') Revisa tu comprobante
      @else ¡Pedido registrado!
      @endif
    </h2>
    <div class="sub">Referencia <b style="color:var(--txt)">{{ $order->code }}</b> ·
      <span class="badge {{ $st==='aprobado'?'ok':($st==='rechazado'?'bad':($st==='comprobante'?'rev':'pend')) }}">{{ $order->statusLabel() }}</span>
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
    @elseif($st==='comprobante')
      <div class="yapebox">
        <div class="ring wait" style="margin-top:4px">⏳</div>
        <div style="font-weight:700">Tu pago está en revisión</div>
        <p style="color:var(--muted);font-size:14px;margin:8px 0 0;line-height:1.6">¡Gracias! Recibimos tu comprobante. En cuanto confirmemos tu Yapeo, te enviaremos tus fotos en alta definición directamente a tu WhatsApp. Ya puedes cerrar esta pantalla con tranquilidad.</p>
        <div class="eta">⏳ Tiempo promedio de validación: 5 a 15 minutos.</div>
        @if($order->op_code)<p style="color:var(--muted);font-size:13px;margin:8px 0 0">Código de operación: <b style="color:var(--txt)">{{ $order->op_code }}</b></p>@endif
        @php
          $waNum = preg_replace('/\D/', '', $yape['number'] ?? '');
          if (strlen($waNum) === 9) { $waNum = '51'.$waNum; } // Perú
          $waMsg = 'Hola, ya subí mi comprobante '.$order->code.' para las fotos de "'.$event->name.'". Mi nombre: '.$order->customer_name.'.';
        @endphp
        @if($waNum)
          <a class="btn wa" href="https://wa.me/{{ $waNum }}?text={{ rawurlencode($waMsg) }}" target="_blank">💬 Consultar por WhatsApp</a>
        @endif
      </div>
      <details>
        <summary>¿Te equivocaste de comprobante? Enviar otro</summary>
        @include('gallery.partials.receipt-form')
      </details>

    {{-- ====== ESTADO: PENDIENTE / RECHAZADO -> pagar ====== --}}
    @else
      @if($st==='rechazado')
        <div class="err">Tu comprobante anterior no pudo validarse. Por favor verifica el Yapeo y vuelve a enviarlo.</div>
      @endif

      @php $qrurl = !empty($yape['qr_path']) ? \Illuminate\Support\Facades\Storage::disk(config('storage.public_disk'))->url($yape['qr_path']) : null; @endphp
      <div class="yapebox">
        <div class="yapehead">Yape</div>

        @if(!empty($yape['number']))
          <div class="paytip">📱 ¿Pagas desde este mismo celular? Lo más fácil: <b>yapea directo a este número</b>. (No escanees el QR de esta pantalla: no se puede escanear con el mismo teléfono en el que lo ves.)</div>
          <div class="numrow">
            <div class="paynum" id="yapenum">{{ $yape['number'] }}</div>
            <button type="button" class="copybtn" onclick="copyNum(this)">Copiar número</button>
          </div>
          <div class="payacc" style="margin-top:6px">{{ $yape['account'] ?: 'Joel Garate Fotografía' }}</div>
        @endif

        <div class="payamt">Monto a pagar: <b>{{ $event->currency }} {{ number_format($order->total,2) }}</b></div>

        @if($qrurl)
          <div class="qrblock">
            <div class="qrttl">Pagar escaneando el QR</div>
            <img class="qr" src="{{ $qrurl }}" alt="QR Yape">
            <div class="qrbtns">
              <button type="button" class="btn" onclick="openQR()">🔍 Ver QR grande</button>
              <a class="btn ghost" href="{{ $qrurl }}" download="yape-joelgarate.png">Guardar QR</a>
            </div>
            <div class="qrhint">Para escanear desde OTRO celular, toca “Ver QR grande” y apúntalo. Si quieres subirlo desde la galería de Yape, abre “Ver QR grande” y toma la captura ahí: se ve limpio y grande, sin letras alrededor, para que no salga error al escanear.</div>
          </div>
        @endif

        <ol class="steps">
          <li>Abre tu app <b>Yape</b> y <b>yapea al número de arriba</b> (o escanea el QR si estás en otra pantalla).</li>
          <li>Paga exactamente <b>{{ $event->currency }} {{ number_format($order->total,2) }}</b>.</li>
          <li>Sube la captura de tu Yape aquí abajo y envía. Con la captura es suficiente.</li>
          <li>Joel confirma tu pago y te enviamos tus fotos en alta, <b>sin marca de agua</b>, directo a tu <b>WhatsApp</b> ({{ $order->customer_contact }}). También quedan disponibles para descargar aquí.</li>
        </ol>
        <div class="dltip" style="margin-top:14px">💬 Apenas Joel confirme tu Yapeo, recibirás tus fotos en tu WhatsApp <b>{{ $order->customer_contact }}</b>.</div>
      </div>

      @include('gallery.partials.receipt-form')
    @endif

    {{-- fotos del pedido (miniaturas) para estados no-aprobado --}}
    @if($st!=='aprobado')
      <div class="thumbs">
        @foreach($order->items->take(10) as $it)
          @if($it->photo)<img src="{{ $it->photo->thumbUrl() }}" alt="{{ $it->code }}">@endif
        @endforeach
      </div>
    @endif

    <a href="{{ route('gallery.show', $event->slug) }}" class="btn ghost">Seguir viendo la galería</a>
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
