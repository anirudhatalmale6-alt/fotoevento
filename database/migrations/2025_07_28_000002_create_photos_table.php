<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();          // ej. IMG_4201 (nombre original sin extensión)
            $table->string('original_path');             // original SIN marca de agua (nunca se publica)
            $table->string('preview_path');              // vista previa CON marca de agua
            $table->string('thumb_path');                // miniatura CON marca de agua
            $table->unsignedInteger('bytes')->default(0);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
