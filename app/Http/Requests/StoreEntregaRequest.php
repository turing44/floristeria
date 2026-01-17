<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEntregaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'fuente'            => 'nullable|string',
            'producto'          => 'required|string',
            'direccion'         => 'required|string|max:255',
            'codigo_postal'     => 'required|string|max:10',
            'destinatario'      => 'required|string|max:255',
            'telf_destinatario' => 'required|string|max:20',
            'cliente'           => 'required|string|max:255',
            'telf_cliente'      => 'required|string|max:20',
            'fecha_entrega'     => 'required|date',
            'precio'            => 'required|numeric|min:0',
            'observaciones'     => 'nullable|string',
            'horario'           => 'nullable|in:MAÑANA,TARDE,INDIFERENTE',
            'mensaje'           => 'nullable|string',
            'estado'            => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'fuente.required' => 'La fuente del pedido es obligatoria.',
            'producto.required' => 'Debes especificar el producto.',
            'direccion.required' => 'La dirección de entrega es obligatoria.',
            'codigo_postal.required' => 'El código postal es obligatorio.',
            'destinatario.required' => 'Debes indicar el nombre del destinatario.',
            'telf_destinatario.required' => 'El teléfono del destinatario es obligatorio.',
            'cliente.required' => 'El nombre del cliente es obligatorio.',
            'telf_cliente.required' => 'El teléfono del cliente es obligatorio.',
            'fecha_entrega.required' => 'La fecha de entrega es obligatoria.',
            'fecha_entrega.date' => 'La fecha de entrega no tiene un formato válido.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'horario.in' => 'El horario debe ser MAÑANA, TARDE o INDIFERENTE.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación en el formulario.',
            'errors'  => $validator->errors()
        ], 422));
    }
}