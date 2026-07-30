@extends('admin.layout')
@section('title',$event->name)
@section('content')
<div class="pagehead">
  <a href="{{ route('admin.events.index') }}" class="btn ghost sm">← Eventos</a>
  <div><h1 style="display:inline-flex;align-items:center;gap:10px">{{ $event->name }}
    <span class="badge {{ $event->published ? 'on' : 'wait' }}" style="font-size:12px">{{ $event->published ? '● Activo' : '⏸ Pausado' }}</span></h1>
    <div class="muted">{{ $event->photos_count }} fotos · {{ $event->currency }} {{ number_format($event->price_unit,2) }} por foto</div></div>
  <div class="sp"></div>
  <form method="post" action="{{ route('admin.events.toggle',$event) }}" style="display:inline"
        onsubmit="return confirm('{{ $event->published ? '¿Pausar este evento? Los clientes ya no podrán ver ni comprar en esta galería hasta que lo reactives.' : '¿Reactivar este evento? La galería volverá a estar disponible para tus clientes.' }}')">
    @csrf
    <button class="btn sm {{ $event->published ? 'ghost' : '' }}">{{ $event->published ? '⏸ Pausar galería' : '▶ Activar galería' }}</button>
  </form>
  <a href="{{ $event->galleryUrl() }}" target="_blank" class="btn ghost sm">Ver galería ↗</a>
</div>

<!-- Enlace de la galería -->
<div class="card" style="margin-bottom:16px;display:flex;gap:14px;align-items:center;flex-wrap:wrap">
  <div style="flex:1;min-width:240px">
    <label>Enlace privado para tus clientes</label>
    <input id="galleryLink" readonly value="{{ $event->galleryUrl() }}">
  </div>
  <div>
    <label>PIN</label>
    <div style="font-weight:800;font-size:18px">{{ $event->pin ?: 'sin PIN' }}</div>
  </div>
  <button class="btn sm" onclick="copyLink()">Copiar enlace</button>
</div>

<!-- Subida masiva -->
<div class="card" style="margin-bottom:16px">
  <h2>Subir fotos</h2>
  <div class="muted" style="font-size:13px;margin-bottom:12px">
    Arrastra aquí tus fotos o selecciónalas (puedes elegir cientos a la vez). Se suben por lotes con barra de progreso.
    El sistema genera automáticamente la marca de agua y la miniatura; el original se guarda protegido.
  </div>
  <div id="drop" style="border:2px dashed var(--line);border-radius:12px;padding:34px;text-align:center;cursor:pointer;background:#fafbfe">
    <div style="font-size:34px">⬆️</div>
    <div style="font-weight:700;margin-top:6px">Arrastra tus fotos o haz clic para elegir</div>
    <div class="muted" style="font-size:13px">JPG o PNG · hasta 25 MB por foto</div>
    <input id="file" type="file" accept="image/jpeg,image/png" multiple hidden>
  </div>
  <div id="progwrap" style="display:none;margin-top:14px">
    <div style="height:10px;background:#eef1f7;border-radius:999px;overflow:hidden">
      <div id="bar" style="height:100%;width:0;background:linear-gradient(90deg,var(--brand),var(--brand2));transition:width .2s"></div>
    </div>
    <div class="muted" id="progtxt" style="font-size:13px;margin-top:8px"></div>
  </div>
</div>

<!-- Grilla de fotos -->
<div class="card">
  <style>
    .ph.is-cover{outline:3px solid var(--brand);outline-offset:-3px}
    .coverbtn{position:absolute;top:6px;left:6px;border:none;background:rgba(0,0,0,.55);color:#fff;border-radius:8px;padding:3px 8px;font-size:12px;font-weight:600;cursor:pointer}
    .ph.is-cover .coverbtn{background:var(--brand)}
  </style>
  <div style="display:flex;align-items:center;margin-bottom:6px">
    <h2 style="margin:0">Fotos del evento (<span id="count">{{ $event->photos_count }}</span>)</h2>
    <div class="sp" style="flex:1"></div>
  </div>
  <p class="muted" style="margin:0 0 12px;font-size:13px">La foto marcada como ★ Portada es la que aparece al compartir el enlace (WhatsApp, etc.). Si no eliges ninguna, se usa la primera. Pasa el cursor sobre una foto y toca “Portada”.</p>
  <div id="grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px">
    @foreach($event->photos as $p)
      @php $isCover = $event->cover_photo_id == $p->id; @endphp
      <div class="ph {{ $isCover?'is-cover':'' }}" data-id="{{ $p->id }}" style="position:relative;border-radius:10px;overflow:hidden;aspect-ratio:3/2;background:#eef1f7">
        <img src="{{ $p->thumbUrl() }}" style="width:100%;height:100%;object-fit:cover">
        <button onclick="setCover({{ $p->id }})" title="Usar como portada" class="coverbtn">{{ $isCover?'★ Portada':'☆ Portada' }}</button>
        <button onclick="delPhoto({{ $p->id }})" title="Eliminar"
          style="position:absolute;top:6px;right:6px;border:none;background:rgba(0,0,0,.55);color:#fff;border-radius:8px;width:26px;height:26px;cursor:pointer">✕</button>
      </div>
    @endforeach
  </div>
  <div id="empty" class="muted" style="text-align:center;padding:30px;@if($event->photos_count) display:none @endif">Todavía no hay fotos. Súbelas arriba.</div>
</div>

<!-- Ajustes / precios -->
<div class="card" style="margin-top:16px">
  <h2>Ajustes y precios</h2>
  <form method="post" action="{{ route('admin.events.update',$event) }}">
    @csrf @method('PUT')
    <div class="row">
      <div class="col" style="min-width:240px"><label>Nombre</label><input name="name" value="{{ $event->name }}" required></div>
      <div class="col"><label>Fecha</label><input type="date" name="event_date" value="{{ optional($event->event_date)->format('Y-m-d') }}"></div>
    </div>
    <div class="row" style="margin-top:12px">
      <div class="col"><label>Moneda</label><input name="currency" value="{{ $event->currency }}"></div>
      <div class="col"><label>Precio por foto</label><input type="number" step="0.01" min="0" name="price_unit" value="{{ $event->price_unit }}" required></div>
      <div class="col"><label>PIN</label><input name="pin" value="{{ $event->pin }}" maxlength="12"></div>
      <div class="col"><label>Marca de agua</label><input name="watermark_text" value="{{ $event->watermark_text }}" maxlength="60"></div>
    </div>
    <h2 style="margin:18px 0 10px;font-size:15px">Paquetes</h2>
    <div id="pkgs">
      @foreach($event->packages as $pk)
      <div class="row" style="margin-bottom:10px;align-items:flex-end">
        <div class="col"><label>Cantidad</label><input type="number" min="1" name="pkg_qty[]" value="{{ $pk->qty }}"></div>
        <div class="col"><label>Precio</label><input type="number" step="0.01" min="0" name="pkg_price[]" value="{{ $pk->price }}"></div>
        <div class="col" style="min-width:180px"><label>Etiqueta</label><input name="pkg_label[]" value="{{ $pk->label }}"></div>
        <button type="button" class="btn ghost sm" onclick="this.parentElement.remove()">Quitar</button>
      </div>
      @endforeach
    </div>
    <button type="button" class="btn ghost sm" onclick="addPkg()">+ Agregar paquete</button>
    <div style="margin-top:16px;display:flex;gap:10px">
      <button class="btn dark">Guardar cambios</button>
    </div>
  </form>
  <form method="post" action="{{ route('admin.events.destroy',$event) }}" style="margin-top:14px"
        onsubmit="return confirm('¿Eliminar este evento y todas sus fotos?')">
    @csrf @method('DELETE')
    <button class="btn danger sm">Eliminar evento</button>
  </form>
</div>

<template id="pkgtpl">
  <div class="row" style="margin-bottom:10px;align-items:flex-end">
    <div class="col"><label>Cantidad</label><input type="number" min="1" name="pkg_qty[]"></div>
    <div class="col"><label>Precio</label><input type="number" step="0.01" min="0" name="pkg_price[]"></div>
    <div class="col" style="min-width:180px"><label>Etiqueta</label><input name="pkg_label[]"></div>
    <button type="button" class="btn ghost sm" onclick="this.parentElement.remove()">Quitar</button>
  </div>
</template>
@endsection

@section('scripts')
<script>
const EV = {{ $event->id }};
const uploadUrl = "{{ route('admin.events.photos.upload',$event) }}";
function addPkg(){ document.getElementById('pkgs').appendChild(document.getElementById('pkgtpl').content.cloneNode(true)); }
function copyLink(){ const i=document.getElementById('galleryLink'); i.select(); document.execCommand('copy'); }

const drop=document.getElementById('drop'), file=document.getElementById('file');
drop.addEventListener('click',()=>file.click());
['dragover','dragenter'].forEach(e=>drop.addEventListener(e,ev=>{ev.preventDefault();drop.style.borderColor='#7c5cff'}));
['dragleave','drop'].forEach(e=>drop.addEventListener(e,ev=>{ev.preventDefault();drop.style.borderColor=''}));
drop.addEventListener('drop',ev=>{ handle(ev.dataTransfer.files); });
file.addEventListener('change',()=>handle(file.files));

async function handle(list){
  const files=[...list].filter(f=>/image\/(jpeg|png)/.test(f.type));
  if(!files.length) return;
  const BATCH=8; let done=0;
  document.getElementById('progwrap').style.display='block';
  for(let i=0;i<files.length;i+=BATCH){
    const chunk=files.slice(i,i+BATCH);
    const fd=new FormData();
    chunk.forEach(f=>fd.append('photos[]',f));
    try{
      const r=await fetch(uploadUrl,{method:'POST',headers:{'X-CSRF-TOKEN':window.CSRF,'Accept':'application/json'},body:fd});
      const j=await r.json();
      if(j.ok){ j.saved.forEach(addTile); document.getElementById('count').textContent=j.total; document.getElementById('empty').style.display='none'; }
    }catch(e){ console.error(e); }
    done+=chunk.length;
    const pct=Math.round(done/files.length*100);
    document.getElementById('bar').style.width=pct+'%';
    document.getElementById('progtxt').textContent=`Subiendo ${done} de ${files.length} fotos… ${pct}%`;
  }
  document.getElementById('progtxt').textContent=`✓ ${done} fotos subidas y protegidas con marca de agua.`;
  setTimeout(()=>{document.getElementById('progwrap').style.display='none';document.getElementById('bar').style.width='0'},2500);
}
function addTile(p){
  const g=document.getElementById('grid');
  const d=document.createElement('div');
  d.className='ph'; d.dataset.id=p.id;
  d.style.cssText='position:relative;border-radius:10px;overflow:hidden;aspect-ratio:3/2;background:#eef1f7';
  d.innerHTML=`<img src="${p.thumb}" style="width:100%;height:100%;object-fit:cover">
    <button title="Usar como portada" class="coverbtn">☆ Portada</button>
    <button title="Eliminar" style="position:absolute;top:6px;right:6px;border:none;background:rgba(0,0,0,.55);color:#fff;border-radius:8px;width:26px;height:26px;cursor:pointer">✕</button>`;
  d.querySelector('.coverbtn').onclick=()=>setCover(p.id);
  d.querySelectorAll('button')[1].onclick=()=>delPhoto(p.id);
  g.prepend(d);
}
async function setCover(id){
  const r=await fetch(`/admin/eventos/${EV}/portada/${id}`,{method:'POST',headers:{'X-CSRF-TOKEN':window.CSRF,'Accept':'application/json'}});
  const j=await r.json();
  if(j.ok){
    document.querySelectorAll('.ph').forEach(ph=>{
      ph.classList.remove('is-cover');
      const b=ph.querySelector('.coverbtn'); if(b) b.textContent='☆ Portada';
    });
    const t=document.querySelector(`.ph[data-id="${id}"]`);
    if(t){ t.classList.add('is-cover'); const b=t.querySelector('.coverbtn'); if(b) b.textContent='★ Portada'; }
  }
}
async function delPhoto(id){
  if(!confirm('¿Eliminar esta foto?')) return;
  const fd=new FormData(); fd.append('_method','DELETE');
  const r=await fetch(`/admin/eventos/${EV}/fotos/${id}`,{method:'POST',headers:{'X-CSRF-TOKEN':window.CSRF,'Accept':'application/json'},body:fd});
  const j=await r.json();
  if(j.ok){ document.querySelector(`.ph[data-id="${id}"]`)?.remove(); document.getElementById('count').textContent=j.total; }
}
</script>
@endsection
