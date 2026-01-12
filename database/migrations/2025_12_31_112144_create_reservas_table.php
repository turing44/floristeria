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
            
            // RELACIÓN 
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            

            $table->dateTime('fecha_recogida');
            $table->decimal('dinero_a_cuenta', 10, 2)->default(0.00); 

            $table->enum('horario', ['MAÑANA', 'TARDE', 'INDIFERENTE'])->default('INDIFERENTE');

            $table->string('nombre_mensaje', 150)->nullable(); 
            $table->string('texto_mensaje', 500)->nullable();  
            
            $table->timestamps();
            $table->softDeletes();
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