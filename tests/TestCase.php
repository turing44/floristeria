<?php

namespace Tests;

use App\Pedidos\Servicios\Pdf\EjecutorPlaywrightPdf;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Evita lanzar Playwright/Chromium real durante los tests:
        // sustituye al ejecutor por un doble que devuelve un PDF ficticio.
        $this->app->bind(EjecutorPlaywrightPdf::class, function () {
            $doble = Mockery::mock(EjecutorPlaywrightPdf::class);
            $doble->shouldReceive('generar')->andReturn('%PDF-FAKE');
            return $doble;
        });
    }
}
