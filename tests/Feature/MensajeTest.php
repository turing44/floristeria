<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MensajeTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_crear_mensaje(): void
    {
        $datos = [
            'texto_mensaje' => 'Hola Marta te amo mucho y quiero volver contigo, solo fue un beso y estuve pensando en ti te lo juro',
            'nombre_mensaje' => 'Jose Pedro el amor de tu vida'
        ];
        
        $response = $this->postJson('/api/mensaje/pdf', $datos);
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
