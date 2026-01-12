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
            $table->timestamps();
            $table->softDeletes();

            $table->string('producto', 100);
            $table->string('direccion');
            $table->string('destinatario', 100);
            $table->string('destinatario_telf', 30); 
            $table->string('cliente', 100);
            $table->string('cliente_telf', 100);
            $table->date('fecha_entrega');
            $table->string('observaciones')->nullable();
            $table->enum('horario', ['MAÑANA', 'TARDE']);
            $table->string('mensaje', 400)->nullable();

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
