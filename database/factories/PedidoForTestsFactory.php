<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pedido;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PedidoForTestsFactory extends Factory
{


    protected $model = Pedido::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {
        return [
            'producto'         => "ramo",
            'direccion'         => "sevilla",
            'destinatario'     => "rober",
            'destinatario_telf'=> "666 666 666",
            'cliente'          => "rober",
            'cliente_telf'     => "666 666 666",
            'fecha_entrega'    => $this->faker->date(),
            'observaciones'    => $this->faker->sentence(),
            'horario'          => 'Tarde',
            'mensaje'          => $this->faker->sentence(6),
        ];
    }
}
