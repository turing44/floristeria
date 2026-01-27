<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Reserva;

class ReservaTest extends TestCase
{
    use RefreshDatabase;
    public function test_reserva_BD()
    {
        $reserva = Reserva::factory()->create();
        $response = $this->get("/api/reservas/pdf/{$reserva->id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
    public function test_reserva_no_existente_BD(){
        $response = $this->get("/api/reservas/pdf/10000000000000");
        $response->assertStatus(404);
    }


}
