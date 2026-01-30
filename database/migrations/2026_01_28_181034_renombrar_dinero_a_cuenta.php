<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    // ESTA ES UNA FALSA MIGRACION QUE HICE PARA QUE LA BASE DE DATOS DE PRODUCCION FUERA CONSISTENTE CON LA MIA DE DESARROLLO


    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->renameColumn('dinero_pendiente', 'dinero_a_cuenta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
