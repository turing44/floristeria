<?php

namespace Database\Factories;

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
        $horarios = ['MAÑANA', 'TARDE', 'INDIFERENTE'];
        $estados = ['ARCHIVADO', 'PENDIENTE', 'ACTIVO'];

        return [
            'cliente' => $this->faker->name(),
            'telf_cliente' => $this->faker->phoneNumber(),
            'precio' => $this->faker->randomFloat(2, 10, 300),
            'dinero_a_cuenta' => $this->faker->randomFloat(2, 0, 150),
            'fecha_entrega' => $this->faker->dateTimeBetween('now', '+1 month'),
            'observaciones' => $this->faker->optional()->sentence(),
            'horario' => $this->faker->randomElement($horarios),
            'destinatario' => $this->faker->optional()->name(),
            'mensaje' => $this->faker->optional()->sentence(),
            'estado' => $this->faker->randomElement($estados),
        ];
    }
}
