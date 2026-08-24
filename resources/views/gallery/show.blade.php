<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $event->name }} · Joel Garate Fotografía</title>
@include('gallery.partials.og', ['event' => $event])
<style>
:root{--bg:#0e1015;--panel:#161a22;--panel2:#1d222c;--line:#2a3140;--txt:#eef1f6;--muted:#9aa4b5;--brand:#7c5cff;--brand2:#00d1b2;--gold:#e8c17a;--yape:#742284}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--txt);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif}
.wrap{max-width:1180px;margin:0 auto;padding:0 16px}
header.top{position:sticky;top:0;z-index:40;background:rgba(14,16,21,.86);backdrop-filter:blur(10px);border-bottom:1px solid var(--line)}
.top .wrap{display:flex;align-items:center;gap:12px;height:60px}
.brand{display:flex;align-items:center;gap:10px;font-weight:700}
.logo{width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,var(--brand),var(--brand2));display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800}
.badge{display:inline-block;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);border:1px solid var(--line);padding:5px 10px;border-radius:999px}
.hero{padding:26px 0 6px}.hero h2{font-size:24px;margin:12px 0 6px}.hero .meta{color:var(--muted);font-size:14px}
.pricepills{display:flex;flex-wrap:wrap;gap:8px;margin-top:16px}
.pill{background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:10px 14px;font-size:13px}.pill b{color:var(--gold)}
.note{margin-top:14px;background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:12px 14px;color:var(--muted);font-size:13px}
.gallery{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;padding:22px 0 10px}
@media(max-width:900px){.gallery{grid-template-columns:repeat(3,1fr)}}
@media(max-width:640px){.gallery{grid-template-columns:repeat(2,1fr)}}
.cell{position:relative;border-radius:12px;overflow:hidden;background:#1d222c;aspect-ratio:3/2;cursor:pointer;border:2px solid transparent}
.cell.sel{border-color:var(--brand2)}
.cell img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s;
  -webkit-user-select:none;user-select:none;-webkit-touch-callout:none;pointer-events:none;-webkit-user-drag:none}
.lb img{-webkit-user-select:none;user-select:none;-webkit-touch-callout:none;-webkit-user-drag:none}
.cell:hover img{transform:scale(1.05)}
.cell .code{position:absolute;left:8px;bottom:8px;font-size:11px;background:rgba(0,0,0,.55);padding:3px 7px;border-radius:6px}
.cell .pick{position:absolute;top:8px;right:8px;width:28px;height:28px;border-radius:50%;background:rgba(0,0,0,.5);border:2px solid #fff;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff;z-index:2}
.cell.sel .pick{background:var(--brand2);border-color:var(--brand2);color:#06231d}
.empty{color:var(--muted);text-align:center;padding:60px 0}
/* Lightbox */
.lb{position:fixed;inset:0;z-index:70;background:rgba(6,7,10,.94);display:none;align-items:center;justify-content:center;padding:16px;flex-direction:column;gap:14px}
.lb.open{display:flex}.lb img{max-width:100%;max-height:72vh;border-radius:12px}
.lb .close{position:absolute;top:14px;right:16px;background:none;border:none;color:#fff;font-size:30px;cursor:pointer}
.lb .nav{position:absolute;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.1);border:none;color:#fff;font-size:26px;width:48px;height:48px;border-radius:50%;cursor:pointer}
.lb .prev{left:14px}.lb .next{right:14px}
.lb .lbbar{display:flex;align-items:center;gap:12px}
.lb .lbcode{color:var(--muted);font-size:13px}
.btn{background:var(--brand);color:#fff;border:none;border-radius:10px;padding:12px 18px;font-weight:800;cursor:pointer;font-size:14px}
.btn.on{background:var(--panel2);color:var(--txt);border:1px solid var(--line)}
.btn.ghost{background:var(--panel2);color:var(--txt);border:1px solid var(--line)}
.btn.block{width:100%}
.btn:disabled{opacity:.6;cursor:default}
/* Barra flotante = pago en 1 paso (nombre + WhatsApp + pagar) */
.cartbar{position:fixed;left:0;right:0;bottom:0;z-index:50;background:rgba(22,26,34,.97);border-top:1px solid var(--line);backdrop-filter:blur(10px);
  box-shadow:0 -10px 30px rgba(0,0,0,.45);transition:transform .18s ease-out}
.cartbar .wrap{padding-top:10px;padding-bottom:calc(12px + env(safe-area-inset-bottom,0px))}
.cbtop{display:flex;align-items:baseline;justify-content:space-between;gap:10px;margin-bottom:8px}
.cbcount{font-size:13px;color:var(--muted)}.cbcount b{color:var(--txt)}
.cbsave{font-size:12px;color:var(--brand2);font-weight:700;text-align:right}
.cbfields{display:grid;grid-template-columns:1fr 1fr;gap:8px}
@media(max-width:340px){.cbfields{grid-template-columns:1fr}}
.cbf{display:flex;align-items:center;gap:8px;background:var(--panel2);border:1px solid var(--line);border-radius:11px;padding:0 11px}
.cbf .ico{font-size:14px;opacity:.7;flex:none}
.cbf input{flex:1;min-width:0;width:100%;background:none;border:none;color:var(--txt);font-size:15px;padding:12px 0}
.cbf input:focus{outline:none}
.cbf:focus-within{border-color:var(--brand)}
.cbf.bad{border-color:#e5484d}
.cberr{display:none;color:#ff8a8d;font-size:12px;margin-top:7px}
.cberr.on{display:block}
.paybtn{width:100%;margin-top:10px;background:var(--yape);color:#fff;border:none;border-radius:12px;padding:15px;font-weight:800;font-size:16px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:8px}
.paybtn:active{transform:scale(.99)}
.paybtn:disabled{opacity:.7;cursor:default}
footer{border-top:1px solid var(--line);color:var(--muted);font-size:12px;text-align:center;padding:22px 0 26px}
</style>
</head>
<body>
<header class="top"><div class="wrap">
  <div class="brand"><div class="logo">JG</div> Joel Garate Fotografía</div>
</div></header>

<div class="wrap hero">
  <span class="badge">Galería del evento</span>
  <h2>{{ $event->name }}</h2>
  <div class="meta">
    {{ $event->event_date? $event->event_date->format('d/m/Y').' · ' : '' }}{{ $event->photos->count() }} fotos
  </div>
  <div class="pricepills">
    <div class="pill">Foto individual <b>{{ $event->currency }} {{ number_format($event->price_unit,2) }}</b></div>
    @foreach($event->packages as $pk)
      <div class="pill">{{ $pk->label ?: 'Paquete '.$pk->qty.' fotos' }} <b>{{ $event->currency }} {{ number_format($pk->price,2) }}</b></div>
    @endforeach
  </div>
  <div class="note">Toca el círculo de cada foto para elegirla. El precio se calcula solo, siempre con el mejor paquete. Cuando termines, escribe tu nombre y tu WhatsApp abajo y pasa a pagar con Yape.</div>
</div>

<div class="wrap">
  @if($event->photos->isEmpty())
    <div class="empty">Aún no hay fotos en esta galería.</div>
  @else
    <div class="gallery" id="gallery"></div>
  @endif
</div>

<footer>Galería protegida · Las fotos se muestran con marca de agua. La descarga en alta se habilita luego del pago.</footer>

<!-- Lightbox -->
<div class="lb" id="lb">
  <button class="close" id="lbClose">×</button>
  <button class="nav prev" id="lbPrev">‹</button>
  <button class="nav next" id="lbNext">›</button>
  <img id="lbImg" src="" alt="">
  <div class="lbbar">
    <span class="lbcode" id="lbCode"></span>
    <button class="btn" id="lbBuy">¡Quiero esta!</button>
  </div>
</div>

<!-- Barra flotante: seleccionar + datos + pagar, todo en la misma pantalla -->
<form class="cartbar" id="cartbar" style="display:none"
      method="post" action="{{ route('gallery.order.store', $event->slug) }}">
  @csrf
  <div class="wrap">
    <div class="cbtop">
      <div class="cbcount"><b id="count">0</b> foto(s) seleccionada(s)</div>
      <div class="cbsave" id="barSub"></div>
    </div>
    <div class="cbfields">
      <div class="cbf" id="wName"><span class="ico">👤</span>
        <input name="customer_name" id="fName" maxlength="120" placeholder="Tu nombre" autocomplete="name" enterkeyhint="next"></div>
      <div class="cbf" id="wPhone"><span class="ico">📱</span>
        <input name="customer_contact" id="fPhone" maxlength="60" placeholder="Tu WhatsApp" inputmode="tel" autocomplete="tel" enterkeyhint="done"></div>
    </div>
    <div class="cberr" id="cbErr"></div>
    <button class="paybtn" id="payBtn" type="submit"><span id="barTotal">Pagar con Yape</span></button>
    <div id="hiddenIds"></div>
  </div>
</form>

<script id="eventData" type="application/json">
{!! json_encode([
  'currency' => $event->currency,
  'unit'     => (float) $event->price_unit,
  'packages' => $event->packages->map(fn($p)=>['qty'=>(int)$p->qty,'price'=>(float)$p->price,'label'=>$p->label])->values(),
  'photos'   => $event->photos->map(fn($p)=>['id'=>$p->id,'code'=>$p->code,'thumb'=>$p->thumbUrl(),'full'=>$p->previewUrl()])->values(),
], JSON_UNESCAPED_UNICODE) !!}
</script>

<script>
const EVENT = JSON.parse(document.getElementById('eventData').textContent);
const $ = s => document.querySelector(s);
const money = n => EVENT.currency + " " + Number(n).toFixed(2);
const selected = new Set();
let lbIndex = 0;

/* ---- Anti-copia (la protección real es la marca de agua) ---- */
document.addEventListener('contextmenu', e=>{ if(e.target.tagName==='IMG') e.preventDefault(); });
document.addEventListener('dragstart', e=>{ if(e.target.tagName==='IMG') e.preventDefault(); });

/* ---- Mejor precio: MISMA lógica que el servidor (PricingService) ---- */
function quote(count){
  count = Math.max(0, count|0);
  const unit = EVENT.unit;
  const sub = +(count*unit).toFixed(2);
  if(count===0) return {sub:0,total:0,discount:0,label:null};
  const pks = EVENT.packages.filter(p=>p.qty>0 && p.price>0);
  const cost = new Array(count+1).fill(Infinity); cost[0]=0;
  for(let i=1;i<=count;i++){
    cost[i]=cost[i-1]+unit;
    pks.forEach(pk=>{ if(i>=pk.qty) cost[i]=Math.min(cost[i], cost[i-pk.qty]+pk.price); });
  }
  let total=cost[count], overshoot=null;
  pks.forEach(pk=>{ if(pk.qty>=count && pk.price<total){ total=pk.price; overshoot=pk.label || ('Paquete '+pk.qty+' fotos'); } });
  total=+total.toFixed(2);
  const discount=+(sub-total).toFixed(2);
  let label=null;
  if(overshoot!==null) label=overshoot;
  else if(discount>0.001){ const ex=pks.find(p=>p.qty===count); label= ex ? (ex.label || ('Paquete '+ex.qty+' fotos')) : 'Precio con paquetes'; }
  return {sub,total,discount:Math.max(0,discount),label};
}

/* ---- Galería ---- */
function renderGallery(){
  const g=$('#gallery'); if(!g) return;
  g.innerHTML = EVENT.photos.map((p,i)=>`
    <div class="cell" data-i="${i}" data-id="${p.id}">
      <img src="${p.thumb}" loading="lazy" alt="${p.code}">
      <span class="code">${p.code}</span>
      <span class="pick"></span>
    </div>`).join('');
  document.querySelectorAll('.cell').forEach(c=>{
    const i=+c.dataset.i;
    c.querySelector('.pick').addEventListener('click',e=>{ e.stopPropagation(); toggle(EVENT.photos[i].id); });
    c.addEventListener('click',()=>openLb(i));
  });
  syncCells();
}
function syncCells(){
  document.querySelectorAll('.cell').forEach(c=>{
    const on=selected.has(+c.dataset.id);
    c.classList.toggle('sel',on);
    c.querySelector('.pick').textContent = on ? '✓' : '';
  });
}
function toggle(id){
  id=+id;
  if(selected.has(id)) selected.delete(id); else selected.add(id);
  syncCells(); renderCart(); updateLbBtn();
}

/* ---- Barra de pago (reemplaza al carrito y a la ventana de datos) ---- */
const bar = $('#cartbar');

function renderCart(){
  const n = selected.size;
  $('#count').textContent = n;
  bar.style.display = n>0 ? 'block' : 'none';

  const q = quote(n);
  $('#barTotal').textContent = 'Pagar ' + money(q.total) + ' con Yape';
  $('#barSub').textContent = q.discount>0.001
    ? ('Ahorras '+money(q.discount)+(q.label? ' · '+q.label : ''))
    : '';
  fitBar();
}

/* El alto de la barra cambia (1 o 2 filas de campos, con o sin error):
   el hueco al final de la galería se recalcula, nunca se fija a ojo. */
function fitBar(){
  const h = bar.style.display==='none' ? 0 : bar.offsetHeight;
  document.body.style.paddingBottom = h ? (h+8)+'px' : '';
}
window.addEventListener('resize', fitBar);

/* Teclado de Android/iOS: el teclado NO empuja a los elementos fijos, los tapa.
   visualViewport dice cuánto ocupa, y subimos la barra justo eso. */
const vv = window.visualViewport;
function liftBar(){
  if(!vv) return;
  const tapado = Math.max(0, window.innerHeight - vv.height - vv.offsetTop);
  bar.style.transform = tapado > 60 ? 'translateY(-'+tapado+'px)' : '';
}
if(vv){ vv.addEventListener('resize', liftBar); vv.addEventListener('scroll', liftBar); }

/* ---- Recordar los datos para la próxima compra ---- */
const KEY_N='fe_nombre', KEY_T='fe_whatsapp';
try{
  const n=localStorage.getItem(KEY_N), t=localStorage.getItem(KEY_T);
  if(n) $('#fName').value=n;
  if(t) $('#fPhone').value=t;
}catch(e){}

/* ---- Validación en el sitio, sin sacar al cliente de la galería ---- */
function soloDigitos(s){ return (s||'').replace(/\D/g,''); }
function showErr(msg, campo){
  const e=$('#cbErr'); e.textContent=msg; e.classList.add('on');
  $('#wName').classList.toggle('bad', campo==='nombre');
  $('#wPhone').classList.toggle('bad', campo==='whatsapp');
  fitBar();
  if(campo==='nombre') $('#fName').focus(); else $('#fPhone').focus();
}
function clearErr(){
  $('#cbErr').classList.remove('on');
  $('#wName').classList.remove('bad'); $('#wPhone').classList.remove('bad');
  fitBar();
}
$('#fName').addEventListener('input', clearErr);
$('#fPhone').addEventListener('input', clearErr);
$('#fName').addEventListener('keydown', e=>{ if(e.key==='Enter'){ e.preventDefault(); $('#fPhone').focus(); } });

bar.addEventListener('submit', e=>{
  const nombre = $('#fName').value.trim();
  const tel    = soloDigitos($('#fPhone').value);

  if(selected.size===0){ e.preventDefault(); return; }
  if(nombre.length < 2){ e.preventDefault(); showErr('Escribe tu nombre para poder enviarte las fotos.','nombre'); return; }
  if(tel.length < 9){ e.preventDefault(); showErr('Tu WhatsApp debe tener 9 dígitos. Ahí te enviamos las fotos.','whatsapp'); return; }

  try{ localStorage.setItem(KEY_N, nombre); localStorage.setItem(KEY_T, $('#fPhone').value.trim()); }catch(err){}

  const box=$('#hiddenIds'); box.innerHTML='';
  [...selected].forEach(id=>{
    const inp=document.createElement('input');
    inp.type='hidden'; inp.name='photo_ids[]'; inp.value=id; box.appendChild(inp);
  });
  $('#payBtn').disabled=true; $('#barTotal').textContent='Un momento...';
});

/* ---- Analítica: registra la previsualización de una foto (sin datos personales) ---- */
const TRACK_URL = "{{ route('gallery.track', $event->slug) }}";
const CSRF = document.querySelector('meta[name=csrf-token]').content;
const tracked = new Set();
function trackPreview(photoId){
  if(tracked.has(photoId)) return;   // una sola vez por sesión y foto
  tracked.add(photoId);
  try{
    fetch(TRACK_URL, {
      method:'POST', keepalive:true,
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
      body: JSON.stringify({type:'photo_preview', photo_id:photoId})
    }).catch(()=>{});
  }catch(e){}
}

/* ---- Lightbox ---- */
function openLb(i){
  lbIndex=i; const p=EVENT.photos[i];
  $('#lbImg').src=p.full; $('#lbCode').textContent=p.code;
  updateLbBtn(); $('#lb').classList.add('open');
  trackPreview(p.id);
}
function updateLbBtn(){
  const p=EVENT.photos[lbIndex]; if(!p) return;
  const on=selected.has(p.id); const b=$('#lbBuy');
  b.textContent = on ? 'Quitar esta foto' : '¡Quiero esta!';
  b.classList.toggle('on', on);
}
$('#lbBuy').addEventListener('click', ()=> toggle(EVENT.photos[lbIndex].id));
$('#lbClose').addEventListener('click', ()=> $('#lb').classList.remove('open'));
$('#lbPrev').addEventListener('click', ()=> openLb((lbIndex-1+EVENT.photos.length)%EVENT.photos.length));
$('#lbNext').addEventListener('click', ()=> openLb((lbIndex+1)%EVENT.photos.length));
document.addEventListener('keydown', e=>{
  if(!$('#lb').classList.contains('open')) return;
  if(e.key==='Escape') $('#lb').classList.remove('open');
  if(e.key==='ArrowLeft') $('#lbPrev').click();
  if(e.key==='ArrowRight') $('#lbNext').click();
});

/* ---- init ---- */
renderGallery(); renderCart();
</script>
</body>
</html>
