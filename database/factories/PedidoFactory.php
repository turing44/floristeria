<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pedido;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition()
    {
        return [
            'fuente'            => $this->faker->optional()->randomElement(['Interflora', 'Glovo', 'Web', 'Tienda Física']),
            'producto'          => $this->faker->words(3, true),
            'cliente_nombre'    => $this->faker->name,
            'cliente_telf'      => $this->faker->phoneNumber,
            'fecha'             => $this->faker->date(),
            'precio'            => $this->faker->randomFloat(2, 10, 250),
            'observaciones'     => $this->faker->sentence,
            'horario'           => $this->faker->randomElement(['MAÑANA', 'TARDE', 'INDIFERENTE']),
            'nombre_mensaje'    => $this->faker->firstName,
            'texto_mensaje'     => $this->faker->sentence,
        ];
    }
}
