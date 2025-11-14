<?php

namespace Database\Seeders;

use App\Models\Entrega;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin    ',
            'email' => 'admin@admin.com',
            'password' => Hash::make('1234')
        ]);

        Entrega::factory()->count(3)->create();
        Reserva::factory()->count(3)->create();
    }
}
