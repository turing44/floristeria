<?php

namespace Tests\Feature;

use App\Models\Reserva;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaApiTest extends TestCase
{
    use RefreshDatabase;

    private function datosReservaValidos(array $sobreescribir = []): array
    {
        return array_merge([
            'nombre_cliente' => 'Cliente Reserva',
            'telefono_cliente' => '600111222',
            'producto' => 'Centro de mesa',
            'precio' => 60.00,
            'fecha' => now()->addDay()->format('Y-m-d'),
            'observaciones' => null,
            'nombre_destinatario' => null,
            'mensaje_tarjeta' => null,
            'hora_recogida' => 12,
            'dinero_pendiente' => 20.00,
        ], $sobreescribir);
    }

    public function test_crea_reserva_y_persiste_dinero_pendiente(): void
    {
        $respuesta = $this->postJson('/api/reservas', $this->datosReservaValidos());

        $respuesta->assertCreated()
            ->assertJsonPath('data.hora_recogida', 12)
            ->assertJsonPath('data.dinero_pendiente', '20.00');

        $this->assertDatabaseHas('pedidos', ['tipo_pedido' => 'TIENDA']);
    }

    public function test_no_acepta_dinero_pendiente_mayor_que_precio(): void
    {
        $respuesta = $this->postJson('/api/reservas', $this->datosReservaValidos([
            'precio' => 50,
            'dinero_pendiente' => 70,
        ]));

        $respuesta->assertStatus(422)
            ->assertJsonValidationErrors(['dinero_pendiente']);
    }

    public function test_no_acepta_hora_recogida_fuera_de_rango(): void
    {
        $this->postJson('/api/reservas', $this->datosReservaValidos(['hora_recogida' => 25]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['hora_recogida']);
    }

    public function test_filtro_pendientes_pago(): void
    {
        $conPendiente = Reserva::factory()->create(['dinero_pendiente' => 30]);
        $sinPendiente = Reserva::factory()->create(['dinero_pendiente' => 0]);

        $respuesta = $this->getJson('/api/reservas?pendientes_pago=1');

        $respuesta->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $conPendiente->id);
    }

    public function test_archiva_reserva(): void
    {
        $reserva = Reserva::factory()->create();

        $this->deleteJson("/api/reservas/{$reserva->id}")->assertNoContent();

        $this->assertSoftDeleted('reservas', ['id' => $reserva->id]);
        $this->assertSoftDeleted('pedidos', ['id' => $reserva->pedido_id]);
    }
}
