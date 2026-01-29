<?php

namespace App\Http\Requests;

class StoreReservaRequest extends BasePedidoRequest
{
    public function rules(): array
    {
        $comunes = $this->reglasComunes(isUpdate: false);

        return array_merge($comunes, [
            'dinero_pendiente' => 'nullable|numeric|min:0',
            'estado_pago'   => 'required|string|in:PAGADO,PENDIENTE',
        ]);
    }
}