<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePedidoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'producto' => 'required|string|max:100',
            'direccion' => 'required|string|max:255',
            'destinatario' => 'required|string|max:100',
            'destinatario_telf' => 'required|string|max:30', 
            'cliente' => 'required|string|max:100',
            'cliente_telf' => 'required|string|max:30',
            'fecha_entrega' => 'required|date|after:today',
            'observaciones' => 'string|max:255',
            'horario' => 'string|in:MAÑANA,TARDE',
            'mensaje' => 'string|max:400',
        ];
    }

    public function messages(): array 
    {
        return [
            'producto.required' => "Producto vacio",

        ];
    }
}
