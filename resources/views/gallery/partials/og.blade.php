{{-- Vista previa al compartir el enlace (WhatsApp, Facebook, etc.) --}}
@php
  $ogTitle = $event->name.' · Joel Garate Fotografía';
  $ogDesc  = 'Mira y elige tus fotos del evento '.$event->name.'. Galería privada — descarga en alta tras el pago.';
  $ogImg   = optional($event->photos()->first())->previewUrl();
@endphp
<meta property="og:type" content="website">
<meta property="og:site_name" content="Joel Garate Fotografía">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDesc }}">
<meta property="og:url" content="{{ $event->galleryUrl() }}">
@if($ogImg)
<meta property="og:image" content="{{ $ogImg }}">
<meta property="og:image:alt" content="Foto del evento {{ $event->name }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="{{ $ogImg }}">
@else
<meta name="twitter:card" content="summary">
@endif
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDesc }}">
