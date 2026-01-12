<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pedido;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition()
    {
        $horarios = ['MAÑANA', 'TARDE', 'INDIFERENTE'];
        $estados = ['ARCHIVADO', 'PENDIENTE', 'ACTIVO'];

        return [
            'user_id' => null,
            'guest_token_id' => null,
            'descripcion' => $this->faker->sentence(),
            'precio' => $this->faker->randomFloat(2, 10, 500),
            'estado' => $this->faker->randomElement($estados),  // selecciona uno al azar
            'horario' => $this->faker->randomElement($horarios), // selecciona uno al azar
            'observaciones' => $this->faker->paragraph(),
            'cliente_nombre' => $this->faker->name(),
            'cliente_telf' => $this->faker->phoneNumber(),
        ];
    }
}
