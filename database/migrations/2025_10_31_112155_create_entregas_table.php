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

            $table->decimal('precio', 10, 2)->default(0.00);

            $table->string('observaciones', 1000)->nullable();

            // ENUMS en mayúsculas
            $table->enum('horario', ['MAÑANA', 'TARDE', 'INDIFERENTE'])->default('INDIFERENTE');
            $table->string('mensaje', 500)->nullable();
            $table->enum('estado', ['ARCHIVADO', 'PENDIENTE', 'ACTIVO'])->default('PENDIENTE');

            $table->softDeletes();
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
