<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Entrega;

class EntregaTest extends TestCase
{
    use RefreshDatabase;
    public function test_entrega_BD()
    {
        $entrega = Entrega::factory()->create();
        $response = $this->get("/api/entregas/pdf/{$entrega->id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
    public function test_entrega_no_existente_BD(){
        $response = $this->get("/api/entregas/pdf/10000000000000");
        $response->assertStatus(404);
    }
}
