<?php

namespace Database\Factories;

use App\Models\Pedido;
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
        
        return [
            "direccion" => $this->faker->streetAddress(),
            'codigo_postal'     => $this->faker->randomElement([
                '41001','41002','41003','41004','41005','41006','41007','41008','41009','41010','41011','41012','41013', '41014', '41015'
            ]),
            'destinatario_telf' => $this->faker->numerify("6########"),
            'pedido_id' => Pedido::factory(),

        ];
        

        
    }
}
