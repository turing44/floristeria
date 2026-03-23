<?php

namespace Database\Factories;

use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reserva>
 */
class ReservaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pedido_id' => Pedido::factory()->paraReserva(),
            'dinero_pendiente' => $this->faker->randomFloat(2, 0, 70),
            'hora_recogida' => $this->faker->optional()->numberBetween(9, 20),
        ];
    }
}
