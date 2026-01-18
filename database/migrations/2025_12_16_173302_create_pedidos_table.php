<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(){
        Schema::create('pedidos', function (Blueprint $table) {
        $table->id(); 
        
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('guest_token_id')->nullable()->constrained('guest_tokens')->nullOnDelete();

        $table->enum('tipo_pedido', ['DOMICILIO', 'TIENDA'])->default('TIENDA');

        $table->string('fuente')->nullable()->default('local'); 
        $table->string('producto'); 
        $table->decimal('precio', 10, 2); 
        $table->date('fecha'); 
        
        $table->string('cliente_nombre'); 
        $table->string('cliente_telf'); 
        
        $table->enum('horario', ['MAÑANA', 'TARDE','INDIFERENTE'])->default('INDIFERENTE');
        
        $table->text('observaciones')->nullable(); 

        $table->string('nombre_mensaje')->nullable();
        $table->text('texto_mensaje')->nullable();
        
        $table->timestamps();
        $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};