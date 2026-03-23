<?php

namespace App\Http\Requests;

class StoreReservaRequest extends BasePedidoRequest
{
    protected function entidadContrato(): string
    {
        return 'reserva';
    }

    protected function operacionContrato(): string
    {
        return 'crear';
    }
}
