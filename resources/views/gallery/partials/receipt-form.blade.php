<form method="post"
      action="{{ route('gallery.order.receipt', ['slug'=>$event->slug,'code'=>$order->code]).'?t='.$order->token }}"
      enctype="multipart/form-data" id="receiptForm">
  @csrf
  <div class="field">
    <label>Captura del Yape</label>
    <label class="filebox" id="fileLabel" for="receiptFile">📎 Toca para subir la captura de tu Yapeo (JPG o PNG)</label>
    <input type="file" id="receiptFile" name="receipt" accept="image/jpeg,image/png" style="display:none">
  </div>
  <div class="field">
    <label>Código de operación (opcional)</label>
    <input type="text" name="op_code" maxlength="40" placeholder="Ej: 01234567">
  </div>
  <button type="submit" class="btn gold" id="receiptSubmit">Enviar comprobante</button>
</form>
<script>
(function(){
  var f=document.getElementById('receiptFile'), l=document.getElementById('fileLabel');
  if(f){ f.addEventListener('change',function(){
    if(f.files && f.files[0]){ l.textContent='✓ '+f.files[0].name; l.classList.add('has'); }
  }); }
  var form=document.getElementById('receiptForm');
  if(form){ form.addEventListener('submit',function(){
    var b=document.getElementById('receiptSubmit'); b.disabled=true; b.textContent='Enviando...';
  }); }
})();
</script>
