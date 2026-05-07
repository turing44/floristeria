<?php

namespace Tests\Unit;

use App\Pedidos\Contratos\ServicioContratoPedidos;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ServicioContratoPedidosTest extends TestCase
{
    private function servicio(): ServicioContratoPedidos
    {
        return $this->app->make(ServicioContratoPedidos::class);
    }

    public function test_obtiene_reglas_de_creacion_de_entrega(): void
    {
        $reglas = $this->servicio()->obtenerReglas('entrega', 'crear');

        $this->assertArrayHasKey('nombre_cliente', $reglas);
        $this->assertArrayHasKey('direccion', $reglas);
        $this->assertArrayHasKey('codigo_postal', $reglas);
        $this->assertStringContainsString('required', $reglas['nombre_cliente']);
    }

    public function test_obtiene_reglas_de_actualizacion_son_nullable(): void
    {
        $reglas = $this->servicio()->obtenerReglas('entrega', 'actualizar');

        $this->assertStringContainsString('nullable', $reglas['nombre_cliente']);
        $this->assertStringContainsString('nullable', $reglas['direccion']);
    }

    public function test_separa_datos_entre_pedido_y_entrega(): void
    {
        $datos = [
            'nombre_cliente' => 'Juan',
            'telefono_cliente' => '600000000',
            'producto' => 'Ramo',
            'precio' => 25,
            'fecha' => '2026-01-01',
            'direccion' => 'Calle 1',
            'codigo_postal' => '28001',
            'telefono_destinatario' => '600000001',
        ];

        $separados = $this->servicio()->separarDatos('entrega', $datos);

        $this->assertSame('Juan', $separados['pedido']['nombre_cliente']);
        $this->assertSame('Ramo', $separados['pedido']['producto']);
        $this->assertSame('Calle 1', $separados['entrega']['direccion']);
        $this->assertSame('28001', $separados['entrega']['codigo_postal']);
        $this->assertArrayNotHasKey('direccion', $separados['pedido']);
    }

    public function test_normaliza_horario_sin_acento(): void
    {
        $datos = $this->servicio()->normalizarEntrada('entrega', ['horario' => 'MANANA']);

        $this->assertSame('MAÑANA', $datos['horario']);
    }

    public function test_validacion_adicional_dinero_pendiente_no_excede_precio(): void
    {
        $datos = ['precio' => 50, 'dinero_pendiente' => 70];
        $validator = Validator::make($datos, []);

        $this->servicio()->aplicarValidacionesAdicionales('reserva', $validator, $datos);
        $validator->passes();

        $this->assertTrue($validator->errors()->has('dinero_pendiente'));
    }

    public function test_validacion_adicional_pasa_cuando_dinero_pendiente_es_menor(): void
    {
        $datos = ['precio' => 50, 'dinero_pendiente' => 20];
        $validator = Validator::make($datos, []);

        $this->servicio()->aplicarValidacionesAdicionales('reserva', $validator, $datos);
        $validator->passes();

        $this->assertFalse($validator->errors()->has('dinero_pendiente'));
    }

    public function test_destino_de_envio_para_entrega(): void
    {
        $contratoCrear = $this->servicio()->obtenerContratoFormulario('entrega', 'crear');
        $contratoEditar = $this->servicio()->obtenerContratoFormulario('entrega', 'actualizar');

        $this->assertSame('POST', $contratoCrear['envio']['metodo']);
        $this->assertSame('/api/entregas', $contratoCrear['envio']['ruta']);
        $this->assertSame('PUT', $contratoEditar['envio']['metodo']);
        $this->assertSame('/api/entregas/{id}', $contratoEditar['envio']['ruta']);
    }
}
