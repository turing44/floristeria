<?php

namespace App\Http\Requests;

class UpdateEntregaRequest extends BasePedidoRequest
{
    protected function entidadContrato(): string
    {
        return 'entrega';
    }

    protected function operacionContrato(): string
    {
        return 'actualizar';
    }

    public function rules(): array
    {
        return $this->reglasDesdeContrato();
    }
}
