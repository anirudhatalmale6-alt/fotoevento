<?php

/*
 | Discos lógicos que usa la app para las fotos.
 |
 | Por defecto se usan los discos locales ('public' y 'local'). Al configurar
 | Cloudflare R2 (variables R2_* en .env) se cambia a 'r2public' y 'r2' sin
 | tocar el código: sólo se ajustan estas dos variables.
 */

return [
    'public_disk'  => env('PHOTOS_PUBLIC_DISK', 'public'),   // previews, miniaturas, QR
    'private_disk' => env('PHOTOS_PRIVATE_DISK', 'local'),   // originales, comprobantes
];
