<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntregaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            
            'cliente'           => 'required|string|max:255',
            'telf_cliente'      => 'required|string|max:20',
            'precio'            => 'required|numeric|min:0',
            'producto'          => 'required|string', // Descripción
            'estado'            => 'nullable|string', // PENDIENTE por defecto
            'observaciones'     => 'nullable|string',

            // --- DATOS DE LOGÍSTICA (Van a la Entrega) ---
            'direccion'         => 'required|string|max:255', // OBLIGATORIO
            'codigo_postal'     => 'required|string|max:10',
            'destinatario'      => 'required|string|max:255',
            'telf_destinatario' => 'required|string|max:20',
            'fecha_entrega'     => 'required|date',
            'horario'           => 'nullable|in:MAÑANA,TARDE,INDIFERENTE',
            'mensaje'           => 'nullable|string',
            'fuente'            => 'nullable|string',
        ];
    }
}