<?php

namespace Database\Factories;

use App\Models\GuestToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GuestTokenFactory extends Factory
{
    protected $model = GuestToken::class;

    public function definition(): array
    {
        return [
            'token' => (string) Str::uuid(),
            'tipo' => $this->faker->randomElement(['link_generado', 'importado']),
            'fecha_exp' => now()->addDays($this->faker->numberBetween(7, 120)),
            'is_used' => false,
        ];
    }

    public function usado(): self
    {
        return $this->state(fn () => [
            'is_used' => true,
        ]);
    }
}
