<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $event->name }}</title>
<style>
:root{--bg:#0e1015;--panel:#161a22;--panel2:#1d222c;--line:#2a3140;--txt:#eef1f6;--muted:#9aa4b5;--brand:#7c5cff;--brand2:#00d1b2;--gold:#e8c17a}
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
.gallery{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;padding:22px 0 110px}
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
/* Barra flotante del carrito */
.cartbar{position:fixed;left:0;right:0;bottom:0;z-index:50;background:rgba(22,26,34,.96);border-top:1px solid var(--line);backdrop-filter:blur(8px)}
.cartbar .wrap{display:flex;align-items:center;gap:12px;height:64px}
.cartbar .info{flex:1}.cartbar .info b{color:var(--gold)}
.cartbar .sub{font-size:12px;color:var(--brand2)}
/* Drawer */
.ov{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:60;display:none}.ov.open{display:block}
.drawer{position:fixed;top:0;right:0;height:100%;width:420px;max-width:92vw;background:var(--panel);border-left:1px solid var(--line);z-index:65;transform:translateX(100%);transition:transform .28s;display:flex;flex-direction:column}
.drawer.open{transform:translateX(0)}
.drawer h3{margin:0;font-size:17px}
.dhead{display:flex;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid var(--line)}
.dhead .x{background:none;border:none;color:var(--muted);font-size:24px;cursor:pointer}
.step{display:none;flex:1;flex-direction:column;overflow:hidden}.step.on{display:flex}
.dbody{flex:1;overflow:auto;padding:14px 18px}
.dfoot{border-top:1px solid var(--line);padding:14px 18px}
.li{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--line)}
.li img{width:52px;height:38px;object-fit:cover;border-radius:7px}
.li .g{flex:1;font-size:13px}
.li .rm{background:none;border:none;color:#e5484d;font-size:12px;cursor:pointer;font-weight:700}
.sumrow{display:flex;justify-content:space-between;font-size:14px;margin:6px 0}
.sumrow.tot{font-size:18px;font-weight:800;margin-top:10px}.sumrow.tot b{color:var(--gold)}
.sumrow.disc{color:var(--brand2)}
.pkgs{margin:0 0 8px;padding-bottom:8px;border-bottom:1px dashed var(--line)}.pkg{display:flex;justify-content:space-between;font-size:12px;color:var(--muted);padding:3px 0}
.field{margin:10px 0}.field label{display:block;font-size:12px;color:var(--muted);margin-bottom:5px;font-weight:600}
.field input{width:100%;padding:11px 12px;background:var(--panel2);border:1px solid var(--line);border-radius:10px;color:var(--txt);font-size:14px}
.field input:focus{outline:none;border-color:var(--brand)}
.hint{font-size:12px;color:var(--muted);margin-top:8px;line-height:1.5}
footer{border-top:1px solid var(--line);color:var(--muted);font-size:12px;text-align:center;padding:22px 0 90px}
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
  <div class="note">Toca el círculo de cada foto para agregarla a tu selección. El sistema calcula automáticamente el mejor precio según los paquetes. Cuando termines, presiona “Continuar con el pedido”.</div>
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
    <button class="btn" id="lbBuy">Agregar a mi selección</button>
  </div>
</div>

<!-- Barra flotante -->
<div class="cartbar" id="cartbar" style="display:none">
  <div class="wrap">
    <div class="info"><span id="count">0</span> foto(s) · <b id="barTotal">{{ $event->currency }} 0.00</b>
      <div class="sub" id="barSub"></div>
    </div>
    <button class="btn" id="openCart">Ver selección</button>
  </div>
</div>

<!-- Drawer -->
<div class="ov" id="ov"></div>
<aside class="drawer" id="drawer">
  <div class="dhead"><h3 id="drawerTitle">Tu selección</h3><button class="x" id="closeCart">×</button></div>

  <!-- Paso 1: items -->
  <div class="step on" id="stepItems">
    <div class="dbody">
      <div class="pkgs" id="pkgs"></div>
      <div id="items"></div>
    </div>
    <div class="dfoot">
      <div class="sumrow"><span>Subtotal (<span id="qty">0</span> fotos)</span><span id="subtotal">{{ $event->currency }} 0.00</span></div>
      <div class="sumrow disc" id="discRow" style="display:none"><span id="discLabel">Descuento</span><span id="discVal"></span></div>
      <div class="sumrow tot"><span>Total</span><b id="total">{{ $event->currency }} 0.00</b></div>
      <button class="btn block" id="toCheckout" style="margin-top:12px">Continuar con el pedido</button>
    </div>
  </div>

  <!-- Paso 2: datos del cliente -->
  <div class="step" id="stepForm">
    <form method="post" action="{{ route('gallery.order.store', $event->slug) }}" id="orderForm" style="display:flex;flex-direction:column;flex:1;overflow:hidden">
      @csrf
      <div class="dbody">
        <div class="sumrow tot" style="margin-top:0"><span><span id="qty2">0</span> foto(s)</span><b id="total2">{{ $event->currency }} 0.00</b></div>
        <p class="hint" style="margin-top:4px">Completa tus datos para registrar el pedido. En el siguiente paso pagarás con Yape y el fotógrafo habilitará la descarga en alta, sin marca de agua.</p>
        <div class="field"><label>Nombre y apellido *</label><input name="customer_name" maxlength="120" required placeholder="Tu nombre"></div>
        <div class="field"><label>WhatsApp / Celular *</label><input name="customer_contact" maxlength="60" required placeholder="Ej: 999 888 777"></div>
        <div class="field"><label>Correo (opcional)</label><input type="email" name="customer_email" maxlength="120" placeholder="tucorreo@ejemplo.com"></div>
        <div id="hiddenIds"></div>
      </div>
      <div class="dfoot">
        <button type="button" class="btn ghost block" id="backToItems" style="margin-bottom:8px">← Volver a mi selección</button>
        <button type="submit" class="btn block" id="confirmOrder">Confirmar pedido</button>
      </div>
    </form>
  </div>
</aside>

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

/* ---- Carrito ---- */
function renderCart(){
  const ids=[...selected]; const n=ids.length;
  $('#count').textContent=n; $('#qty').textContent=n; $('#qty2').textContent=n;
  $('#cartbar').style.display = n>0 ? 'block' : 'none';

  $('#pkgs').innerHTML = EVENT.packages.map(p=>{
    const lbl=p.label || ('Paquete '+p.qty+' fotos');
    return `<div class="pkg"><span>${lbl} · ${p.qty} fotos</span><span>${money(p.price)}</span></div>`;
  }).join('');

  if(n===0){
    $('#items').innerHTML=`<div class="empty" style="padding:30px 0">Aún no has seleccionado fotos.<br>Toca el círculo de una foto para agregarla.</div>`;
  } else {
    $('#items').innerHTML = ids.map(id=>{
      const p=EVENT.photos.find(x=>x.id===id);
      return `<div class="li"><img src="${p.thumb}"><div class="g">${p.code}<br><small style="color:var(--muted)">${money(EVENT.unit)}</small></div><button class="rm" data-id="${id}">Quitar</button></div>`;
    }).join('');
    document.querySelectorAll('.rm').forEach(b=>b.addEventListener('click',()=>toggle(b.dataset.id)));
  }

  const q=quote(n);
  $('#subtotal').textContent=money(q.sub);
  $('#total').textContent=money(q.total);
  $('#total2').textContent=money(q.total);
  $('#barTotal').textContent=money(q.total);
  $('#barSub').textContent = q.discount>0.001 ? ('Ahorras '+money(q.discount)) : '';
  if(q.discount>0.001){
    $('#discRow').style.display='flex';
    $('#discLabel').textContent = q.label || 'Descuento';
    $('#discVal').textContent = '- '+money(q.discount);
  } else $('#discRow').style.display='none';
}

/* ---- Drawer ---- */
function openCart(){ showStep('items'); $('#drawer').classList.add('open'); $('#ov').classList.add('open'); }
function closeCart(){ $('#drawer').classList.remove('open'); $('#ov').classList.remove('open'); }
function showStep(which){
  $('#stepItems').classList.toggle('on', which==='items');
  $('#stepForm').classList.toggle('on', which==='form');
  $('#drawerTitle').textContent = which==='form' ? 'Tus datos' : 'Tu selección';
}
$('#openCart').addEventListener('click', openCart);
$('#closeCart').addEventListener('click', closeCart);
$('#ov').addEventListener('click', closeCart);
$('#toCheckout').addEventListener('click', ()=>{ if(selected.size===0) return; showStep('form'); });
$('#backToItems').addEventListener('click', ()=> showStep('items'));

/* ---- Enviar pedido: inyecta los IDs seleccionados en el form ---- */
$('#orderForm').addEventListener('submit', e=>{
  if(selected.size===0){ e.preventDefault(); return; }
  const box=$('#hiddenIds'); box.innerHTML='';
  [...selected].forEach(id=>{
    const inp=document.createElement('input');
    inp.type='hidden'; inp.name='photo_ids[]'; inp.value=id; box.appendChild(inp);
  });
  $('#confirmOrder').disabled=true; $('#confirmOrder').textContent='Enviando...';
});

/* ---- Lightbox ---- */
function openLb(i){
  lbIndex=i; const p=EVENT.photos[i];
  $('#lbImg').src=p.full; $('#lbCode').textContent=p.code;
  updateLbBtn(); $('#lb').classList.add('open');
}
function updateLbBtn(){
  const p=EVENT.photos[lbIndex]; if(!p) return;
  const on=selected.has(p.id); const b=$('#lbBuy');
  b.textContent = on ? 'Quitar de mi selección' : 'Agregar a mi selección';
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
