<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MensajeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_genera_pdf_de_mensaje(): void
    {
        $respuesta = $this->post('/api/mensaje/pdf', [
            'nombre_destinatario' => 'Maria',
            'mensaje_tarjeta' => 'Felicidades en tu cumpleanos.',
        ]);

        $respuesta->assertOk();
        $this->assertSame('application/pdf', $respuesta->headers->get('Content-Type'));
    }
}
