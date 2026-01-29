<?php

namespace Database\Factories;

use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reserva>
 */
class ReservaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'dinero_pendiente' => $this->faker->randomFloat(2,0,70),
            'estado_pago' => $this->faker->randomElement(['PENDIENTE', 'PAGADO']),
            'pedido_id' => Pedido::factory(),
        ];
    }
}
