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
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->string('fuente');
            $table->string('producto');
            $table->string('direccion');
            $table->string('codigo_postal');
            $table->string('destinatario');
            $table->string('telf_destinatario');
            $table->string('cliente');
            $table->string('telf_cliente');
            $table->date('fecha_entrega');
            $table->date('precio');
            $table->string('observaciones')->nullable();
            $table->string('horario'); //Enum: 'Mañana', 'Tarde', 'Indiferente'
            $table->string('mensaje')->nullable();            
            $table->string('estado'); //Enum: 'Archivado', 'Pendiente', 'Activo'           

            

            
            $table->timestamps();
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
