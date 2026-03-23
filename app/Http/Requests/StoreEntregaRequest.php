<?php

namespace App\Http\Requests;

class StoreEntregaRequest extends BasePedidoRequest
{
    protected function entidadContrato(): string
    {
        return 'entrega';
    }

    protected function operacionContrato(): string
    {
        return 'crear';
    }
}
