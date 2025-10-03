<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pedido>
 */
class PedidoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'producto' => fake()->word(),
            'direccion' => fake()->streetAddress(),
            'destinatario' => fake('es_ES')->name(),
            'destinatario_telf' => fake()->phoneNumber(),
            'cliente' => fake('es_ES')->name(),
            'cliente_telf' => fake('es_ES')->phoneNumber(),
            'fecha_entrega' => fake()->date(),
            'observaciones' => fake('es_ES')->text(200), // aprox 200 caracteres
            'horario' => fake()->randomElement(['MAÑANA', 'TARDE']),
            'mensaje' => fake('es_ES')->text(250),
        ];
    }

}
