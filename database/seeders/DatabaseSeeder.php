<?php

namespace Database\Seeders;

use App\Models\Entrega;
use App\Models\GuestToken;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Usuario Local',
            'email' => 'local@example.com',
        ]);

        GuestToken::factory()->count(4)->create();

        Entrega::factory()->count(6)->create();
        Reserva::factory()->count(6)->create();

        Entrega::factory()->count(2)->create()->each(function (Entrega $entrega): void {
            $entrega->pedido->delete();
            $entrega->delete();
        });

        Reserva::factory()->count(2)->create()->each(function (Reserva $reserva): void {
            $reserva->pedido->delete();
            $reserva->delete();
        });
    }
}
