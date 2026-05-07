<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContratosControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_devuelve_contrato_de_entrega(): void
    {
        $respuesta = $this->getJson('/api/contratos/entregas');

        $respuesta->assertOk()
            ->assertJsonStructure([
                'entidad',
                'version',
                'operacion',
                'secciones' => [
                    ['id', 'titulo', 'campos'],
                ],
                'campos_ocultos',
                'envio' => ['metodo', 'ruta'],
            ]);

        $this->assertSame('entrega', $respuesta->json('entidad'));
        $this->assertSame('crear', $respuesta->json('operacion'));
        $this->assertSame('POST', $respuesta->json('envio.metodo'));
        $this->assertSame('/api/entregas', $respuesta->json('envio.ruta'));

        $claves = collect($respuesta->json('secciones'))
            ->flatMap(fn ($s) => collect($s['campos'])->pluck('clave'))
            ->all();

        // Campos clave que el frontend espera para pintar el formulario.
        foreach (['nombre_cliente', 'telefono_cliente', 'producto', 'precio', 'fecha', 'direccion', 'codigo_postal', 'telefono_destinatario'] as $clave) {
            $this->assertContains($clave, $claves, "Falta el campo {$clave} en el contrato de entrega");
        }
    }

    public function test_devuelve_contrato_de_reserva(): void
    {
        $respuesta = $this->getJson('/api/contratos/reservas');

        $respuesta->assertOk();
        $this->assertSame('reserva', $respuesta->json('entidad'));
        $this->assertSame('/api/reservas', $respuesta->json('envio.ruta'));

        $claves = collect($respuesta->json('secciones'))
            ->flatMap(fn ($s) => collect($s['campos'])->pluck('clave'))
            ->all();

        foreach (['nombre_cliente', 'telefono_cliente', 'producto', 'precio', 'fecha', 'hora_recogida', 'dinero_pendiente'] as $clave) {
            $this->assertContains($clave, $claves, "Falta el campo {$clave} en el contrato de reserva");
        }
    }
}
