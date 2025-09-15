<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pedido;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PedidoRandomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Pedido::class;

    public function definition(): array
    {
        return [
            'producto'         => fake()->word(),
            'direccion'         => $this->faker->sentence(),
            'destinatario'     => $this->faker->name(),
            'destinatario_telf'=> $this->faker->phoneNumber(),
            'cliente'          => $this->faker->name(),
            'cliente_telf'     => $this->faker->phoneNumber(),
            'fecha_entrega'    => $this->faker->date(),
            'observaciones'    => $this->faker->sentence(),
            'horario'          => $this->faker->randomElement(['Mañana', 'Tarde']),
            'mensaje'          => $this->faker->sentence(6),
        ];
    }
}
