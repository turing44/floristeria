<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pedido;
use App\Models\GuestToken;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition(): array
    {
        return [
            'guest_token_id' => null,
            'tipo_pedido' => $this->faker->randomElement(['DOMICILIO', 'TIENDA']),
            'fuente' => $this->faker->randomElement(['local', 'telefono', 'instagram']),
            'nombre_cliente' => $this->faker->name(),
            'telefono_cliente' => $this->faker->numerify('6########'),
            'producto' => ucfirst($this->faker->words(3, true)),
            'precio' => $this->faker->randomFloat(2, 20, 180),
            'fecha' => $this->faker->dateTimeBetween('-1 week', '+3 weeks')->format('Y-m-d'),
            'observaciones' => $this->faker->optional()->sentence(),
            'horario'           => $this->faker->randomElement(['MAÑANA', 'TARDE', 'INDIFERENTE']),
            'nombre_destinatario' => $this->faker->optional()->firstName(),
            'mensaje_tarjeta' => $this->faker->optional()->sentence(),
        ];
    }

    public function conTokenInvitado(?GuestToken $token = null): self
    {
        return $this->state(fn () => [
            'guest_token_id' => $token?->id ?? GuestToken::factory(),
        ]);
    }

    public function paraEntrega(): self
    {
        return $this->state(fn () => [
            'tipo_pedido' => 'DOMICILIO',
            'horario' => $this->faker->randomElement(['MAÑANA', 'TARDE', 'INDIFERENTE']),
        ]);
    }

    public function paraReserva(): self
    {
        return $this->state(fn () => [
            'tipo_pedido' => 'TIENDA',
            'horario' => 'INDIFERENTE',
            'nombre_destinatario' => null,
            'mensaje_tarjeta' => null,
        ]);
    }
}
