<?php

namespace App\Http\Requests;

class UpdateReservaRequest extends BasePedidoRequest
{
    protected function entidadContrato(): string
    {
        return 'reserva';
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
