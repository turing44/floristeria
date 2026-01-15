<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
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
            'producto'          => 'required|string',
            'estado'            => 'nullable|string',
            'observaciones'     => 'nullable|string',

            // --- DATOS DE TIENDA (Van a la Reserva) ---
            'fecha_recogida'    => 'required|date', // OBLIGATORIO
            'dinero_a_cuenta'   => 'nullable|numeric|min:0',
        ];
    }
}