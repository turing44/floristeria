<?php

namespace App\Pedidos\Servicios;

use App\Models\Entrega;
use App\Models\Reserva;
use App\Pedidos\Servicios\Pdf\AlmacenPdfPedidos;
use App\Pedidos\Servicios\Pdf\EjecutorPlaywrightPdf;
use App\Pedidos\Servicios\Pdf\RenderizadorHtmlPdfPedidos;
use Illuminate\Support\Facades\Log;

class ServicioPdfPedidos
{
    public function __construct(
        private RenderizadorHtmlPdfPedidos $renderizador,
        private EjecutorPlaywrightPdf $ejecutor,
        private AlmacenPdfPedidos $almacen
    ) {
    }

    public function obtenerEntrega(Entrega $entrega): string
    {
        return $this->obtenerOGuardar(
            $this->almacen->rutaEntrega($entrega->id),
            fn () => $this->generarEntrega($entrega)
        );
    }

    public function obtenerReserva(Reserva $reserva): string
    {
        return $this->obtenerOGuardar(
            $this->almacen->rutaReserva($reserva->id),
            fn () => $this->generarReserva($reserva)
        );
    }

    public function guardarEntregaSinRomper(Entrega $entrega): void
    {
        $this->guardarSinRomper(
            fn () => $this->guardar(
                $this->almacen->rutaEntrega($entrega->id),
                fn () => $this->generarEntrega($entrega)
            ),
            'No se pudo guardar el PDF de la entrega.'
        );
    }

    public function guardarReservaSinRomper(Reserva $reserva): void
    {
        $this->guardarSinRomper(
            fn () => $this->guardar(
                $this->almacen->rutaReserva($reserva->id),
                fn () => $this->generarReserva($reserva)
            ),
            'No se pudo guardar el PDF de la reserva.'
        );
    }

    public function generarMensaje(array $datos): string
    {
        return $this->ejecutor->generar($this->renderizador->renderizarMensaje($datos));
    }

    private function obtenerOGuardar(string $ruta, callable $generador): string
    {
        $existente = $this->almacen->obtenerSiExiste($ruta);

        if ($existente !== null) {
            return $existente;
        }

        return $this->guardar($ruta, $generador);
    }

    private function guardar(string $ruta, callable $generador): string
    {
        $contenido = $generador();

        return $this->almacen->guardar($ruta, $contenido);
    }

    private function guardarSinRomper(callable $accion, string $mensaje): void
    {
        try {
            $accion();
        } catch (\Throwable $e) {
            Log::error($mensaje, ['error' => $e->getMessage()]);
        }
    }

    private function generarEntrega(Entrega $entrega): string
    {
        return $this->ejecutor->generar($this->renderizador->renderizarEntrega($entrega));
    }

    private function generarReserva(Reserva $reserva): string
    {
        return $this->ejecutor->generar($this->renderizador->renderizarReserva($reserva));
    }
}
