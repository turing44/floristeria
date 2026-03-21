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

    public function rules(): array
    {
        return $this->reglasDesdeContrato();
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'dinero_pendiente.max' => 'El dinero pendiente no puede ser mayor que el precio total del pedido.',
            'dinero_pendiente.numeric' => 'El dinero pendiente debe ser un numero.',
            'dinero_pendiente.min' => 'El dinero pendiente no puede ser negativo.',
        ]);
    }
}
