<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string("producto");
            $table->string("direccion");
            $table->string("destinatario");
            $table->string("destinatario_telf");
            $table->string("cliente");
            $table->string("cliente_telf");
            $table->date("fecha_entrega");
            $table->string("observaciones")->nullable();
            $table->string("horario")->nulleable();
            $table->string("mensaje")->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
