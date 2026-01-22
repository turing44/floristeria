<?php

namespace Tests\Feature\View\Mensaje;

use Tests\TestCase;

class BladeTest extends TestCase
{
    /**
     * A basic view test example.
     */
    public function test_it_can_render(): void
    {
        $contents = $this->view('mensaje.blade', [
            //
        ]);

        $contents->assertSee('');
    }
}
