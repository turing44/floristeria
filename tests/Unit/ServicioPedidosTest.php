<?php

namespace Tests\Unit;

use App\Models\Entrega;
use App\Pedidos\Servicios\ServicioPedidos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicioPedidosTest extends TestCase
{
    use RefreshDatabase;

    private function servicio(): ServicioPedidos
    {
        return $this->app->make(ServicioPedidos::class);
    }

    public function test_crea_pedido_y_entrega_en_una_transaccion(): void
    {
        $entrega = $this->servicio()->crear('entrega', 'DOMICILIO', 'entrega', [
            'nombre_cliente' => 'Juan',
            'telefono_cliente' => '600000000',
            'producto' => 'Ramo',
            'precio' => 30,
            'fecha' => '2026-02-01',
            'direccion' => 'Calle Mayor 1',
            'codigo_postal' => '28001',
            'telefono_destinatario' => '600000001',
        ]);

        $this->assertInstanceOf(Entrega::class, $entrega);
        $this->assertSame('DOMICILIO', $entrega->pedido->tipo_pedido);
        $this->assertSame('Juan', $entrega->pedido->nombre_cliente);
        $this->assertSame('Calle Mayor 1', $entrega->direccion);
    }

    public function test_actualiza_solo_los_campos_recibidos(): void
    {
        $entrega = Entrega::factory()->create();
        $direccionOriginal = $entrega->direccion;

        $this->servicio()->actualizar('entrega', $entrega, [
            'nombre_cliente' => 'Otro Cliente',
        ]);

        $entrega->refresh();
        $this->assertSame('Otro Cliente', $entrega->pedido->nombre_cliente);
        $this->assertSame($direccionOriginal, $entrega->direccion);
    }

    public function test_archivar_marca_entrega_y_pedido_como_softdelete(): void
    {
        $entrega = Entrega::factory()->create();

        $this->servicio()->archivar($entrega);

        $this->assertSoftDeleted('entregas', ['id' => $entrega->id]);
        $this->assertSoftDeleted('pedidos', ['id' => $entrega->pedido_id]);
    }

    public function test_restaurar_recupera_entrega_y_pedido(): void
    {
        $entrega = Entrega::factory()->create();
        $this->servicio()->archivar($entrega);

        $entrega = Entrega::withTrashed()->with(['pedido' => fn ($q) => $q->withTrashed()])
            ->find($entrega->id);

        $this->servicio()->restaurar($entrega);

        $this->assertDatabaseHas('entregas', ['id' => $entrega->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('pedidos', ['id' => $entrega->pedido_id, 'deleted_at' => null]);
    }
}
