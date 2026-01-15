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
            // ⚠️ IMPORTANTE: Aquí validamos los nombres del FRONTEND (JSON)
            // No pongas los nombres de la BD (destinatario_nombre) o fallará.
            
            'destinatario'      => 'nullable|string|max:255', // Roberto manda 'destinatario'
            'telf_destinatario' => 'nullable|string|max:20',
            'mensaje'           => 'nullable|string',
            
            'direccion'         => 'nullable|string|max:255',
            'codigo_postal'     => 'nullable|string|max:10',
            'fecha_entrega'     => 'nullable|date',
            'horario'           => 'nullable|in:MAÑANA,TARDE,INDIFERENTE',
            'fuente'            => 'nullable|string',
            
            // Si quieres permitir editar el estado del pedido padre desde aquí:
            'estado'            => 'nullable|string',
        ];
    }
}