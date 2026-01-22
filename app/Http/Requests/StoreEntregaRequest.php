<?php

namespace App\Http\Requests;

class StoreEntregaRequest extends BasePedidoRequest
{
    public function rules(): array
    {
        $comunes = $this->reglasComunes(isUpdate: false);
        return array_merge($comunes, [
            'direccion'         => 'required|string|max:255',
            'codigo_postal'     => 'required|string|max:10',
            'destinatario_telf' => 'required|string|max:20',
        ]);
    }
}