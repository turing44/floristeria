<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BasePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function reglasComunes(bool $isUpdate = false): array
    {
        $prefix = $isUpdate ? 'nullable' : 'required';

        return [
            'cliente_nombre' => "$prefix|string|max:255",
            'cliente_telf'   => "$prefix|string|max:20",
            'precio'         => "$prefix|numeric|min:0",
            'producto'       => "$prefix|string|max:255",
            'fecha'          => "$prefix|date",
            'horario'        => 'nullable|in:MAÑANA,TARDE,INDIFERENTE',
            'observaciones'  => 'nullable|string|max:1000',
            'texto_mensaje'  => 'nullable|string|max:500',
            'nombre_mensaje' => 'nullable|string|max:255',
            'fuente'         => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_nombre.required' => 'El nombre del cliente es obligatorio.',
            'cliente_nombre.max'      => 'El nombre del cliente es demasiado largo (máx 255).',
            'cliente_telf.required'   => 'El teléfono es obligatorio.',
            'cliente_telf.max'        => 'El teléfono es demasiado largo.',
            
            'precio.required'         => 'El precio es obligatorio.',
            'precio.numeric'          => 'El precio debe ser un número.',
            'precio.min'              => 'El precio no puede ser negativo.',
            
            'producto.required'       => 'Debes especificar el producto.',
            'fecha.required'          => 'La fecha es obligatoria.',
            'fecha.date'              => 'La fecha no es válida.',
            
            'horario.in'              => 'El horario debe ser: MAÑANA, TARDE o INDIFERENTE.',
            'estado_pago.in'          => 'El estado de pago solo puede ser: PAGADO o PENDIENTE.',
            
            'direccion.required'      => 'La dirección es obligatoria para envíos.',
            'codigo_postal.required'  => 'El código postal es obligatorio.',
            'destinatario_telf.required' => 'El teléfono del destinatario es obligatorio.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación.',
            'errors'  => $validator->errors()
        ], 422));
    }
}