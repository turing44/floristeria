<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            
            $table->decimal('dinero_pendiente', 5, 2)->default(0.00); 
            $table->enum('estado_pago', ['PAGADO', 'PENDIENTE'])->default('PENDIENTE');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};