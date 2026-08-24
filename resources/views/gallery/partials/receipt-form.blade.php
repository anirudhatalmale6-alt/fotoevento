<form method="post"
      action="{{ route('gallery.order.receipt', ['slug'=>$event->slug,'code'=>$order->code]).'?t='.$order->token }}"
      enctype="multipart/form-data" id="receiptForm">
  @csrf
  <div class="field">
    <label class="filebox" id="fileLabel" for="receiptFile">
      <span class="fbico">📸</span>
      <span id="fileMain">Toca aquí para subir tu captura</span>
      <span class="fbsub">La captura del Yape, en JPG o PNG</span>
    </label>
    <input type="file" id="receiptFile" name="receipt" accept="image/jpeg,image/png" style="display:none">
  </div>
  <details class="opwrap">
    <summary>¿Prefieres escribir el código de operación? (opcional)</summary>
    <input type="text" name="op_code" maxlength="40" placeholder="Ej: 01234567" style="margin-top:8px">
  </details>
  <button type="submit" class="btn gold" id="receiptSubmit">Enviar comprobante</button>
</form>
<script>
(function(){
  var f=document.getElementById('receiptFile'), l=document.getElementById('fileLabel'),
      m=document.getElementById('fileMain'), b=document.getElementById('receiptSubmit'),
      form=document.getElementById('receiptForm');
  var op=form && form.querySelector('[name=op_code]');

  /* Se esconde DESDE JS, nunca en el HTML: si el navegador no ejecuta scripts,
     el botón tiene que seguir ahí o el formulario no se puede enviar. */
  function refrescar(){
    var listo=(f && f.files && f.files.length) || (op && op.value.trim().length);
    b.style.display = listo ? '' : 'none';
  }
  if(b) refrescar();

  if(f){ f.addEventListener('change',function(){
    if(f.files && f.files[0]){
      if(m) m.textContent='✓ Captura lista, ya puedes enviarla';  // el nombre del archivo no le dice nada al cliente
      l.classList.add('has');
    }
    refrescar();
  }); }
  if(op) op.addEventListener('input', refrescar);

  if(form){ form.addEventListener('submit',function(e){
    var hasFile=f && f.files && f.files.length;
    var hasOp=op && op.value.trim().length;
    if(!hasFile && !hasOp){ e.preventDefault(); alert('Sube la captura de tu Yape para enviar tu comprobante.'); return; }
    b.disabled=true; b.textContent='Enviando...';
  }); }
})();
</script>
