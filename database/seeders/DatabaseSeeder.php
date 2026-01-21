<?php

namespace Database\Seeders;

use App\Models\Entrega;
use App\Models\Reserva;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Entrega::factory(5)->create();
        Reserva::factory(5)->create();
    }
}