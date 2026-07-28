<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->date('event_date')->nullable();
            $table->string('pin', 12)->nullable();          // PIN opcional por galería
            $table->string('currency', 8)->default('S/');
            $table->decimal('price_unit', 8, 2)->default(0); // precio por foto (definible por evento)
            $table->string('watermark_text')->nullable();    // marca de agua (texto o nombre)
            $table->string('cover_thumb')->nullable();
            $table->unsignedInteger('photos_count')->default(0);
            $table->boolean('published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
