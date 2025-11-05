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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->string('producto');
            $table->string('cliente');
            $table->string('telf_cliente');

            // el decimal es seguro para eel dinero
            $table->decimal('precio', 10, 2)->default(0.00);
            $table->decimal('dinero_a_cuenta', 10, 2)->default(0.00);
            $table->date('fecha_entrega');
            $table->string('observaciones', 1000)->nullable();

            // Enums en MAYÚSCULAS
            $table->enum('horario', ['MAÑANA', 'TARDE', 'INDIFERENTE'])->default('INDIFERENTE');
            $table->string('nombre_mensaje', 150)->nullable();
            $table->string('texto_mensaje', 500)->nullable();
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
        Schema::dropIfExists('reservas');
    }
};
