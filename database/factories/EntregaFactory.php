<?php

namespace Database\Factories;

use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entrega>
 */
class EntregaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pedido_id' => Pedido::factory()->paraEntrega(),
            'direccion' => $this->faker->streetAddress(),
            'codigo_postal' => $this->faker->postcode(),
            'telefono_destinatario' => $this->faker->numerify('6########'),
        ];
    }
}
