<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntregaRequest extends FormRequest
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
            'fuente' => 'required|string|max:150',
            'producto' => 'required|string|max:150',
            'direccion' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:10',
            'destinatario' => 'required|string|max:150',
            'telf_destinatario' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\s-]{6,20}$/', // valida teléfonos simples
            ],
            'cliente' => 'required|string|max:150',
            'telf_cliente' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\s-]{6,20}$/',
            ],
            'fecha_entrega' => 'required|date|after_or_equal:today',
            'precio' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000',
            'horario' => 'required|in:MAÑANA,TARDE,INDIFERENTE',
            'mensaje' => 'nullable|string|max:500',
            'estado' => 'required|in:ARCHIVADO,PENDIENTE,ACTIVO',
        ];
    }

    public function messages(): array
    {
        return [
            'fuente.required' => 'La fuente es obligatoria.',
            'producto.required' => 'El producto es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'codigo_postal.required' => 'El código postal es obligatorio.',
            'destinatario.required' => 'El destinatario es obligatorio.',
            'telf_destinatario.required' => 'El teléfono del destinatario es obligatorio.',
            'telf_destinatario.regex' => 'El teléfono del destinatario tiene un formato inválido.',
            'cliente.required' => 'El cliente es obligatorio.',
            'telf_cliente.required' => 'El teléfono del cliente es obligatorio.',
            'telf_cliente.regex' => 'El teléfono del cliente tiene un formato inválido.',
            'fecha_entrega.required' => 'La fecha de entrega es obligatoria.',
            'fecha_entrega.date' => 'La fecha de entrega debe ser una fecha válida.',
            'fecha_entrega.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
            'horario.required' => 'El horario es obligatorio.',
            'horario.in' => 'El horario debe ser MAÑANA, TARDE o INDIFERENTE.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser ARCHIVADO, PENDIENTE o ACTIVO.',
        ];
    }
}
