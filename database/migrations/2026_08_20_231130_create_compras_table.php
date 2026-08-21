<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();

            $table->foreignId('carrito_id')->nullable()->constrained('carritos')->nullOnDelete();
            $table->string('nombre_cliente');
            $table->string('email');
            $table->string('direccion_envio');
            $table->string('ciudad');
            $table->string('codigo_postal');
            $table->string('metodo_pago');
            
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuestos', 10, 2);
            $table->decimal('costo_envio', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('estado')->default('confirmada');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};