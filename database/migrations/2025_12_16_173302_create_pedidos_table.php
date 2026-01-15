<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(){
        Schema::create('pedidos', function (Blueprint $table) {
        $table->id(); // id
        
        // RELACIONES (Claves foráneas)
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('guest_token_id')->nullable()->constrained('guest_tokens')->nullOnDelete();


        $table->enum('tipo_pedido', ['DOMICILIO', 'TIENDA'])->default('TIENDA');
        // DATOS DEL PEDIDO
        $table->string('producto'); 
        $table->decimal('precio', 10, 2); 
        $table->string('estado')->default('pendiente'); 
        $table->enum('horario', ['MAÑANA', 'TARDE','INDIFERENTE'])->default('INDIFERENTE');
        
        $table->text('observaciones')->nullable(); 
        
        // DATOS DEL CLIENTE
        $table->string('cliente_nombre'); 
        $table->string('cliente_telf'); 
        
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
