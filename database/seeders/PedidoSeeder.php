<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Factories\PedidoForTestsFactory;
use Database\Factories\PedidoRandomFactory;

class PedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PedidoForTestsFactory::new()->count(1)->create();
        PedidoRandomFactory::new()->count(10)->create();
    }
}
