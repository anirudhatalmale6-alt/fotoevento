<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('code', 24)->unique();          // referencia visible p/ el cliente (ej: FE-2025-0007)
            $table->string('customer_name', 120);
            $table->string('customer_contact', 60);          // WhatsApp / celular
            $table->string('customer_email', 120)->nullable();
            $table->unsignedInteger('photo_count')->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);  // precio sin descuento (unidad * cantidad)
            $table->decimal('total', 10, 2)->default(0);      // precio final (mejor paquete)
            $table->string('applied_label', 80)->nullable();  // nombre del paquete aplicado, si hubo
            $table->string('status', 20)->default('pendiente'); // pendiente | pagado | entregado | cancelado
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
