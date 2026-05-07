<?php

namespace Tests\Feature;

use App\Models\Entrega;
use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntregaApiTest extends TestCase
{
    use RefreshDatabase;

    private function datosEntregaValidos(array $sobreescribir = []): array
    {
        return array_merge([
            'nombre_cliente' => 'Cliente Demo',
            'telefono_cliente' => '600111222',
            'producto' => 'Ramo de rosas',
            'precio' => 35.50,
            'fecha' => now()->addDay()->format('Y-m-d'),
            'horario' => 'MAÑANA',
            'observaciones' => null,
            'nombre_destinatario' => 'Maria',
            'mensaje_tarjeta' => 'Felicidades',
            'direccion' => 'Calle Mayor 1',
            'codigo_postal' => '28013',
            'telefono_destinatario' => '600999888',
        ], $sobreescribir);
    }

    public function test_lista_entregas_vacia_devuelve_meta_y_data(): void
    {
        $respuesta = $this->getJson('/api/entregas');

        $respuesta->assertOk()
            ->assertJsonPath('meta.total', 0)
            ->assertJsonStructure(['data', 'meta' => ['total', 'archivadas', 'resumen', 'filtros']]);
    }

    public function test_crea_entrega_con_datos_validos(): void
    {
        $respuesta = $this->postJson('/api/entregas', $this->datosEntregaValidos());

        $respuesta->assertCreated()
            ->assertJsonPath('data.direccion', 'Calle Mayor 1')
            ->assertJsonPath('data.codigo_postal', '28013');

        $this->assertDatabaseCount('pedidos', 1);
        $this->assertDatabaseCount('entregas', 1);
        $this->assertDatabaseHas('pedidos', [
            'tipo_pedido' => 'DOMICILIO',
            'nombre_cliente' => 'Cliente Demo',
        ]);
    }

    public function test_no_crea_entrega_si_faltan_campos_obligatorios(): void
    {
        $respuesta = $this->postJson('/api/entregas', [
            'producto' => 'Ramo',
        ]);

        $respuesta->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_cliente', 'telefono_cliente', 'precio', 'fecha', 'direccion', 'codigo_postal', 'telefono_destinatario']);
    }

    public function test_actualiza_entrega(): void
    {
        $entrega = Entrega::factory()->create();

        $respuesta = $this->putJson("/api/entregas/{$entrega->id}", [
            'direccion' => 'Nueva direccion 42',
        ]);

        $respuesta->assertOk()
            ->assertJsonPath('data.direccion', 'Nueva direccion 42');

        $this->assertDatabaseHas('entregas', [
            'id' => $entrega->id,
            'direccion' => 'Nueva direccion 42',
        ]);
    }

    public function test_archiva_entrega_y_aparece_en_archivadas(): void
    {
        $entrega = Entrega::factory()->create();

        $this->deleteJson("/api/entregas/{$entrega->id}")->assertNoContent();

        $this->assertSoftDeleted('entregas', ['id' => $entrega->id]);
        $this->assertSoftDeleted('pedidos', ['id' => $entrega->pedido_id]);

        $archivadas = $this->getJson('/api/entregas/archivadas')->assertOk();
        $archivadas->assertJsonPath('meta.total', 1);
    }

    public function test_restaura_entrega_archivada(): void
    {
        $entrega = Entrega::factory()->create();
        $entrega->pedido->delete();
        $entrega->delete();

        $this->postJson("/api/entregas/restaurar/{$entrega->id}")->assertOk();

        $this->assertDatabaseHas('entregas', [
            'id' => $entrega->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('pedidos', [
            'id' => $entrega->pedido_id,
            'deleted_at' => null,
        ]);
    }

    public function test_busqueda_filtra_por_cliente(): void
    {
        $entrega1 = Entrega::factory()->create();
        $entrega1->pedido->update(['nombre_cliente' => 'Pepita Perez']);

        Entrega::factory()->create()->pedido->update(['nombre_cliente' => 'Otro Cliente']);

        $respuesta = $this->getJson('/api/entregas?buscar=Pepita');

        $respuesta->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $entrega1->id);
    }

    public function test_filtro_por_horario(): void
    {
        $manana = Entrega::factory()->create();
        $manana->pedido->update(['horario' => 'MAÑANA']);

        $tarde = Entrega::factory()->create();
        $tarde->pedido->update(['horario' => 'TARDE']);

        $respuesta = $this->getJson('/api/entregas?horario=TARDE');

        $respuesta->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $tarde->id);
    }

    public function test_show_devuelve_entrega(): void
    {
        $entrega = Entrega::factory()->create();

        $this->getJson("/api/entregas/{$entrega->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $entrega->id);
    }

    public function test_show_inexistente_es_404(): void
    {
        $this->getJson('/api/entregas/9999')->assertNotFound();
    }

    public function test_pdf_de_entrega_responde_con_content_type_pdf(): void
    {
        $entrega = Entrega::factory()->create();

        $respuesta = $this->get("/api/entregas/pdf/{$entrega->id}");

        $respuesta->assertOk();
        $this->assertSame('application/pdf', $respuesta->headers->get('Content-Type'));
    }
}
