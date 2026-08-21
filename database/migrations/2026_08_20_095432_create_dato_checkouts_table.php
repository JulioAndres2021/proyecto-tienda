<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_checkout', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrito_id')->unique()->constrained('carritos')->cascadeOnDelete();
            $table->string('nombre_cliente');
            $table->string('email');
            $table->string('direccion_envio');
            $table->string('ciudad');
            $table->string('codigo_postal');
            $table->string('metodo_pago')->default("efectivo");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datos_checkout');
    }
};