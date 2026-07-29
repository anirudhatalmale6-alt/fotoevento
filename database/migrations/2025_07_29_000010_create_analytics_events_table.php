<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('photo_id')->nullable()->index();
            $table->string('type', 30)->index();      // gallery_view | photo_preview
            $table->string('visitor', 40)->index();    // id anónimo por dispositivo (cookie)
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['event_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
