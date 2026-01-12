<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entrega>
 */
class EntregaFactory extends Factory
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
            'fuente' => $this->faker->company(),
            'producto' => $this->faker->word(),
            'direccion' => $this->faker->streetAddress(),
            'codigo_postal' => $this->faker->postcode(),
            'destinatario' => $this->faker->name(),
            'telf_destinatario' => $this->faker->phoneNumber(),
            'cliente' => $this->faker->name(),
            'telf_cliente' => $this->faker->phoneNumber(),
            'fecha_entrega' => $this->faker->dateTimeBetween('now', '+1 month'),
            'precio' => $this->faker->randomFloat(2, 10, 500), // Precio entre 10 y 500 €
            'observaciones' => $this->faker->optional()->sentence(),
            'horario' => $this->faker->randomElement($horarios),
            'mensaje' => $this->faker->optional()->sentence(),
            'estado' => $this->faker->randomElement($estados),
        ];
    }
}
