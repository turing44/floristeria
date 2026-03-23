<?php

namespace App\Pedidos\Servicios\Pdf;

use App\Models\Entrega;
use App\Models\Reserva;

class RenderizadorHtmlPdfPedidos
{
    public function renderizarEntrega(Entrega $entrega): string
    {
        return view('pdf.albaran', compact('entrega'))->render();
    }

    public function renderizarReserva(Reserva $reserva): string
    {
        return view('pdf.reserva', compact('reserva'))->render();
    }

    public function renderizarMensaje(array $datos): string
    {
        return view('pdf.mensaje', compact('datos'))->render();
    }
}
