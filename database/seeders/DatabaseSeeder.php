<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('reservas')->truncate();
        DB::table('entregas')->truncate();
        DB::table('pedidos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

       
        for ($i = 1; $i <= 5; $i++) {
            
            
            $tipo = ($i % 2 == 0) ? 'TIENDA' : 'DOMICILIO';

           -
            $pedidoId = DB::table('pedidos')->insertGetId([
                'cliente_nombre' => 'Cliente ' . $tipo . ' ' . $i,
                'cliente_telf'   => '60012300' . $i,
                
                'tipo_pedido'    => $tipo, 
                
                'precio'         => rand(20, 150),
                'estado'         => 'PENDIENTE',
                'observaciones'  => 'Pedido de prueba número ' . $i,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            if ($tipo === 'DOMICILIO') {
                DB::table('entregas')->insert([
                    'pedido_id'           => $pedidoId,
                    'direccion'           => 'Calle del Destino ' . $i,
                    'codigo_postal'       => '2800' . $i,
                    'destinatario_nombre' => 'Destinatario ' . $i,
                    'destinatario_telf'   => '70099900' . $i,
                    'fecha_entrega'       => now()->addDays($i),
                    'horario'             => 'MAÑANA',
                    'mensaje_dedicatoria' => 'Disfruta de tus flores.',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            } else {
                DB::table('reservas')->insert([
                    'pedido_id'       => $pedidoId,
                    'fecha_recogida'  => now()->addDays($i),
                    'dinero_a_cuenta' => 10.00,
                    'horario'         => 'TARDE',
                    'nombre_mensaje'  => 'Feliz día',
                    'texto_mensaje'   => 'Te deseamos lo mejor.',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
    }
}