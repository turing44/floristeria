<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('guest_token_id')->nullable()->constrained('guest_tokens')->nullOnDelete();
            $table->enum('tipo_pedido', ['DOMICILIO', 'TIENDA']);
            $table->string('fuente')->default('local');
            $table->string('nombre_cliente', 80);
            $table->string('telefono_cliente', 20);
            $table->string('producto', 150);
            $table->decimal('precio', 10, 2);
            $table->date('fecha');
            $table->text('observaciones')->nullable();
            $table->enum('horario', ['MAÑANA', 'TARDE', 'INDIFERENTE'])->default('INDIFERENTE');
            $table->string('nombre_destinatario', 64)->nullable();
            $table->text('mensaje_tarjeta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
