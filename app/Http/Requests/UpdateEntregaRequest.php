<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEntregaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fuente'            => 'nullable|string',
            'producto'          => 'nullable|string',
            'direccion'         => 'nullable|string|max:255',
            'codigo_postal'     => 'nullable|string|max:10',
            'destinatario'      => 'nullable|string|max:255',
            'telf_destinatario' => 'nullable|string|max:20',
            'cliente'           => 'nullable|string|max:255',
            'telf_cliente'      => 'nullable|string|max:20',
            'fecha_entrega'     => 'nullable|date',
            'precio'            => 'nullable|numeric|min:0',
            'observaciones'     => 'nullable|string',
            'horario'           => 'nullable|in:MAÑANA,TARDE,INDIFERENTE',
            'mensaje'           => 'nullable|string',
            'estado'            => 'nullable|string'
        ];
    }
}