<?php

namespace App\Http\Requests;

class UpdateReservaRequest extends BasePedidoRequest
{
    public function rules(): array
    {
        $comunes = $this->reglasComunes(isUpdate: true);

        return array_merge($comunes, [
            'dinero_dejado_a_cuenta' => 'nullable|numeric|min:0',
            'estado_pago'            => 'nullable|string|in:PAGADO,PENDIENTE',
            'hora_recogida' => 'nullable|string|max:10',
        ]);
    }
}