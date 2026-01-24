<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pedido;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;
private function textoMin(int $min, int $max): string
    {
        do {
            $texto = $this->faker->text($max);
        } while (strlen($texto) < $min);

        return $texto;
    }
    private function nombreMinCaracteres(int $min, int $max){
        do{
            $nombre=$this->faker->firstName($max);
        }while(strlen($nombre) < $min);
        return $nombre;
    }
    private function nombreApellidosMinCaracteres(int $min, int $max){
        do{
            $nombre=$this->faker->name($max);
        }while(strlen($nombre) < $min);
        return $nombre;
    }

    public function definition()
    {
        return [
            'fuente'            => $this->faker->optional()->randomElement(['Interflora', 'Glovo', 'Web', 'Tienda Física']),
            'producto'          => $this->faker->words(20, true),
            'cliente_nombre'    => $this->nombreApellidosMinCaracteres(40,41),
            'cliente_telf'      => $this->faker->phoneNumber,
            'fecha'             => $this->faker->date(),
            'precio'            => $this->faker->randomFloat(2, 10, 250),
            'observaciones'     => $this->textoMin(240,241),
            'horario'           => $this->faker->randomElement(['MAÑANA', 'TARDE', 'INDIFERENTE']),
            'nombre_mensaje'    => $this->nombreMinCaracteres(16,17),
            'texto_mensaje'     => $this->textoMin(280,281),
        ];
        
    }
}
