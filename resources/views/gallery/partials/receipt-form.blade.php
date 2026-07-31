<form method="post"
      action="{{ route('gallery.order.receipt', ['slug'=>$event->slug,'code'=>$order->code]).'?t='.$order->token }}"
      enctype="multipart/form-data" id="receiptForm">
  @csrf
  <div class="field">
    <label>Envía tu comprobante de Yape</label>
    <label class="filebox" id="fileLabel" for="receiptFile">📎 Toca aquí para subir la captura de tu Yapeo (JPG o PNG)</label>
    <input type="file" id="receiptFile" name="receipt" accept="image/jpeg,image/png" style="display:none">
    <div class="fhint">Con la captura es suficiente 👍 Joel la revisa y te enviamos tus fotos a tu WhatsApp{{ !empty($order->customer_contact) ? ' ('.$order->customer_contact.')' : '' }}. No necesitas escribir nada más.</div>
  </div>
  <details class="opwrap">
    <summary>¿Prefieres escribir el código de operación? (opcional)</summary>
    <input type="text" name="op_code" maxlength="40" placeholder="Ej: 01234567" style="margin-top:8px">
  </details>
  <button type="submit" class="btn gold" id="receiptSubmit">Enviar comprobante</button>
</form>
<script>
(function(){
  var f=document.getElementById('receiptFile'), l=document.getElementById('fileLabel');
  if(f){ f.addEventListener('change',function(){
    if(f.files && f.files[0]){ l.textContent='✓ '+f.files[0].name; l.classList.add('has'); }
  }); }
  var form=document.getElementById('receiptForm');
  if(form){ form.addEventListener('submit',function(e){
    var hasFile=f && f.files && f.files.length;
    var op=form.querySelector('[name=op_code]');
    var hasOp=op && op.value.trim().length;
    if(!hasFile && !hasOp){ e.preventDefault(); alert('Sube la captura de tu Yape para enviar tu comprobante.'); return; }
    var b=document.getElementById('receiptSubmit'); b.disabled=true; b.textContent='Enviando...';
  }); }
})();
</script>
