# FotoEvento — Plataforma de venta de fotos de eventos

Plataforma tipo Wfolio para vender fotografías de eventos (promociones, graduaciones).
Desarrollada en **Laravel 13 + PHP 8.3**. El código es propiedad del cliente.

## Hito 1 (esta entrega)

- **Panel de administración** con acceso protegido (login del fotógrafo).
- **Eventos ilimitados**: crear/editar/eliminar, con enlace privado único por evento.
- **Galerías privadas por enlace + PIN** opcional por evento.
- **Subida masiva** de fotos (por lotes, con barra de progreso — pensada para cientos/miles).
- **Marca de agua automática** (mosaico diagonal con el nombre/marca del fotógrafo) + **miniaturas**, generadas con GD en cada subida.
- **Originales protegidos**: se guardan en un disco privado, nunca accesibles por la web. Sólo se entregan tras el pago (Hito 4).
- **Precios por evento**: precio por foto individual + paquetes (base para la tienda del Hito 2).
- **Galería pública** responsive (PC, tablet, celular) con vista previa con marca de agua y visor (lightbox).

Próximos hitos: **M2** tienda/selección/carrito · **M3** pago Yape (con QR) y aprobación manual · **M4** descargas seguras con enlaces firmados + puesta en línea.

## Requisitos

- PHP 8.2+ con extensiones `gd`, `pdo`, `mbstring`, `fileinfo`
- Composer 2
- Base de datos: SQLite (por defecto, para pruebas) o **MySQL** (recomendado en producción)

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Acceso al panel: `/admin/login`
Usuario de prueba (creado por el seeder): **joel@fotoevento.com** / **fotoevento2025**
(cámbialo tras el primer ingreso).

## Base de datos MySQL (producción)

En `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fotoevento
DB_USERNAME=usuario
DB_PASSWORD=clave
```

Luego `php artisan migrate --seed`.

## Almacenamiento en la nube (Cloudflare R2 / S3) — recomendado

El sistema usa el sistema de discos de Laravel. Para mover las fotos a Cloudflare R2
(sin cambiar código) se configura un disco S3 apuntando al endpoint de R2 y se ajustan
los discos `public`/`local` a ese driver. Se deja listo en el Hito 4 (puesta en línea).

## Cómo funciona la marca de agua

`app/Services/WatermarkService.php` genera, por cada foto subida:
- **preview** (máx. 1000 px) con marca de agua diagonal en mosaico,
- **thumb** (máx. 500 px) con marca más ligera.

El texto de la marca se define por evento (por defecto, el nombre de marca del fotógrafo).
Más adelante se puede añadir marca con logo (PNG) sin cambiar el flujo.

## Estructura principal

```
app/Models/             Event, Photo, EventPackage
app/Http/Controllers/   Admin\AuthController, Admin\EventController, GalleryController
app/Services/           WatermarkService (GD)
resources/views/admin   panel (login, eventos, subida)
resources/views/gallery galería pública (PIN + vista con marca de agua)
```
