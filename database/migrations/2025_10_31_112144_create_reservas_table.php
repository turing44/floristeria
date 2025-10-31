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
            $table->string('cliente');
            $table->string('telf_cliente');
            $table->string('precio');
            $table->string('dinero_a_cuenta');
            $table->date('fecha_entrega');
            $table->string('observaciones')->nullable();
            $table->string('horario'); //Enum: 'Mañana', 'Tarde', 'Indiferente'
            $table->string('destinatario')->nullable();
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
        Schema::dropIfExists('reservas');
    }
};
