<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('token', 48)->nullable()->after('code');      // acceso del cliente a su pedido (link)
            $table->string('receipt_path')->nullable()->after('note');   // comprobante de Yape (disco privado)
            $table->string('op_code', 40)->nullable()->after('receipt_path'); // código de operación Yape
            $table->timestamp('paid_at')->nullable()->after('op_code');  // cuándo subió el comprobante
            $table->timestamp('approved_at')->nullable()->after('paid_at'); // cuándo el fotógrafo aprobó
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['token', 'receipt_path', 'op_code', 'paid_at', 'approved_at']);
        });
    }
};
