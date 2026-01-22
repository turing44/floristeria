<?php

namespace App\Http\Requests;

class UpdateEntregaRequest extends BasePedidoRequest
{
    public function rules(): array
    {
        $comunes = $this->reglasComunes(isUpdate: true);

        return array_merge($comunes, [
            'direccion'         => 'nullable|string|max:255',
            'codigo_postal'     => 'nullable|string|max:10',
            'destinatario_telf' => 'nullable|string|max:20',
        ]);
    }
}