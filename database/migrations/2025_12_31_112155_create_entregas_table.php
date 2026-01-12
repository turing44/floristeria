<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            
            // RELACIÓN
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            
            $table->string('fuente')->nullable(); 
            $table->string('direccion');
            $table->string('codigo_postal');
            $table->string('destinatario_nombre');
            $table->string('destinatario_telf');
            
            $table->dateTime('fecha_entrega')->nullable();
            $table->enum('horario', ['MAÑANA', 'TARDE', 'INDIFERENTE'])->default('INDIFERENTE');
            $table->string('mensaje_dedicatoria', 500)->nullable(); 
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};