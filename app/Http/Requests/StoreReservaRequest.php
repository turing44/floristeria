<?php

namespace App\Http\Requests;

class StoreReservaRequest extends BasePedidoRequest
{
    public function rules(): array
    {
        $comunes = $this->reglasComunes(isUpdate: false);

        return array_merge($comunes, [
            'dinero_pendiente' => 'nullable|numeric|min:0|' . "max:" . ($this->input('precio') ?? 0),
            'estado_pago'   => 'required|string|in:PAGADO,PENDIENTE',
            'hora_recogida' => 'nullable|string|max:10',
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'dinero_pendiente.max' => 'El dinero pendiente no puede ser mayor que el precio total del pedido.',
            'estado_pago.required' => 'El estado de pago es obligatorio.',
            'estado_pago.in'       => 'El estado de pago solo puede ser: PAGADO o PENDIENTE.',
        ]);
    }
}