<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RutasApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_ruta_de_health_responde(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_existen_las_rutas_principales_de_api(): void
    {
        $rutas = collect(app('router')->getRoutes())
            ->map(fn ($r) => implode('|', $r->methods()) . ' ' . $r->uri())
            ->all();

        $esperadas = [
            'GET|HEAD api/contratos/entregas',
            'GET|HEAD api/contratos/reservas',
            'GET|HEAD api/entregas',
            'POST api/entregas',
            'GET|HEAD api/entregas/{entrega}',
            'PUT api/entregas/{entrega}',
            'DELETE api/entregas/{entrega}',
            'GET|HEAD api/entregas/archivadas',
            'GET|HEAD api/entregas/pdf/{id}',
            'POST api/entregas/restaurar/{id}',
            'GET|HEAD api/reservas',
            'POST api/reservas',
            'GET|HEAD api/reservas/{reserva}',
            'PUT api/reservas/{reserva}',
            'DELETE api/reservas/{reserva}',
            'POST api/mensaje/pdf',
        ];

        foreach ($esperadas as $ruta) {
            $this->assertContains($ruta, $rutas, "Falta la ruta {$ruta}");
        }
    }
}
