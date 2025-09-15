<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PedidoApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_encuentra_pedido_por_id(): void
    {
        $response = $this->getJson('/api/pedidos/1');

        $response->assertStatus(200);
    }
}
