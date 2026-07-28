<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $event->name }}</title>
<style>
:root{--bg:#0e1015;--panel:#161a22;--line:#2a3140;--txt:#eef1f6;--muted:#9aa4b5;--brand:#7c5cff;--brand2:#00d1b2;--gold:#e8c17a}
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
.gallery{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;padding:22px 0 80px}
@media(max-width:900px){.gallery{grid-template-columns:repeat(3,1fr)}}
@media(max-width:640px){.gallery{grid-template-columns:repeat(2,1fr)}}
.cell{position:relative;border-radius:12px;overflow:hidden;background:#1d222c;aspect-ratio:3/2;cursor:pointer}
.cell img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s;
  -webkit-user-select:none;user-select:none;-webkit-touch-callout:none;pointer-events:none;-webkit-user-drag:none}
.lb img{-webkit-user-select:none;user-select:none;-webkit-touch-callout:none;-webkit-user-drag:none}
.cell:hover img{transform:scale(1.05)}
.cell .code{position:absolute;left:8px;bottom:8px;font-size:11px;background:rgba(0,0,0,.55);padding:3px 7px;border-radius:6px}
.empty{color:var(--muted);text-align:center;padding:60px 0}
.lb{position:fixed;inset:0;z-index:70;background:rgba(6,7,10,.94);display:none;align-items:center;justify-content:center;padding:16px}
.lb.open{display:flex}.lb img{max-width:100%;max-height:80vh;border-radius:12px}
.lb .close{position:absolute;top:14px;right:16px;background:none;border:none;color:#fff;font-size:30px;cursor:pointer}
.lb .nav{position:absolute;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.1);border:none;color:#fff;font-size:26px;width:48px;height:48px;border-radius:50%;cursor:pointer}
.lb .prev{left:14px}.lb .next{right:14px}
footer{border-top:1px solid var(--line);color:var(--muted);font-size:12px;text-align:center;padding:22px 0}
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
  <div class="note">Toca una foto para verla en grande. La selección de fotos y el pago con Yape se activan en la siguiente etapa.</div>
</div>

<div class="wrap">
  @if($event->photos->isEmpty())
    <div class="empty">Aún no hay fotos en esta galería.</div>
  @else
    <div class="gallery" id="gallery">
      @foreach($event->photos as $p)
        <div class="cell" data-full="{{ $p->previewUrl() }}" data-code="{{ $p->code }}">
          <img src="{{ $p->thumbUrl() }}" loading="lazy" alt="{{ $p->code }}">
          <span class="code">{{ $p->code }}</span>
        </div>
      @endforeach
    </div>
  @endif
</div>

<div class="lb" id="lb">
  <button class="close" id="lbClose">×</button>
  <button class="nav prev" id="lbPrev">‹</button>
  <button class="nav next" id="lbNext">›</button>
  <img id="lbImg" src="" alt="">
</div>

<footer>Galería protegida · Las fotos se muestran con marca de agua. La descarga en alta se habilita luego del pago.</footer>

<script>
// Disuasores anti-copia: sin clic derecho / sin arrastrar / sin guardar imagen.
// (La protección real es la marca de agua; esto bloquea las formas fáciles de copiar.)
document.addEventListener('contextmenu', e=>{ if(e.target.tagName==='IMG') e.preventDefault(); });
document.addEventListener('dragstart', e=>{ if(e.target.tagName==='IMG') e.preventDefault(); });
const cells=[...document.querySelectorAll('.cell')]; let idx=0;
function open(i){ idx=(i+cells.length)%cells.length; document.getElementById('lbImg').src=cells[idx].dataset.full; document.getElementById('lb').classList.add('open'); }
cells.forEach((c,i)=>c.addEventListener('click',()=>open(i)));
document.getElementById('lbClose').onclick=()=>document.getElementById('lb').classList.remove('open');
document.getElementById('lbPrev').onclick=()=>open(idx-1);
document.getElementById('lbNext').onclick=()=>open(idx+1);
document.addEventListener('keydown',e=>{ if(!document.getElementById('lb').classList.contains('open'))return;
  if(e.key==='Escape')document.getElementById('lb').classList.remove('open'); if(e.key==='ArrowLeft')open(idx-1); if(e.key==='ArrowRight')open(idx+1); });
</script>
</body>
</html>
