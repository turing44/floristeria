<?php

namespace Tests\Unit;

use App\Models\Entrega;
use App\Pedidos\Servicios\ServicioListadoPedidos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicioListadoPedidosTest extends TestCase
{
    use RefreshDatabase;

    private function servicio(): ServicioListadoPedidos
    {
        return $this->app->make(ServicioListadoPedidos::class);
    }

    public function test_lista_vacia_devuelve_meta_consistente(): void
    {
        $resultado = $this->servicio()->listarEntregas([]);

        $this->assertSame(0, $resultado['meta']['total']);
        $this->assertSame(0, $resultado['meta']['archivadas']);
        $this->assertSame(0, $resultado['meta']['resumen']['activas']);
    }

    public function test_busqueda_por_codigo_postal_filtra(): void
    {
        $entrega = Entrega::factory()->create(['codigo_postal' => '99988']);
        Entrega::factory()->count(2)->create(['codigo_postal' => '11122']);

        $resultado = $this->servicio()->listarEntregas(['buscar' => '99988']);

        $this->assertSame(1, $resultado['meta']['total']);
        $this->assertSame($entrega->id, $resultado['registros']->first()->id);
    }

    public function test_orden_por_codigo_postal(): void
    {
        Entrega::factory()->create(['codigo_postal' => '28010']);
        Entrega::factory()->create(['codigo_postal' => '08001']);
        Entrega::factory()->create(['codigo_postal' => '46011']);

        $resultado = $this->servicio()->listarEntregas(['ordenar' => 'cp']);

        $cps = $resultado['registros']->pluck('codigo_postal')->all();
        $this->assertSame(['08001', '28010', '46011'], $cps);
    }

    public function test_resumen_cuenta_archivadas(): void
    {
        $entrega = Entrega::factory()->create();
        $entrega->delete();

        $resultado = $this->servicio()->listarEntregas([]);

        $this->assertSame(1, $resultado['meta']['resumen']['archivadas']);
        $this->assertSame(0, $resultado['meta']['resumen']['activas']);
    }
}
