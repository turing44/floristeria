<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // --- TABLA PEDIDOS---
            'cliente_nombre'    => 'required|string|max:255',
            'cliente_telf'      => 'required|string|max:20',
            'precio'            => 'required|numeric|min:0',
            'producto'          => 'required|string|max:255',
            'fecha'             => 'required|date',         
            'horario'           => 'nullable|in:MAÑANA,TARDE,INDIFERENTE',
            'observaciones'     => 'nullable|string|max:1000',
            'texto_mensaje'     => 'nullable|string|max:500',       
            'nombre_mensaje'    => 'nullable|string|max:255',       

            // --- TABLA RESERVAS---
            'dinero_dejado_a_cuenta' => 'nullable|numeric|min:0',
            'estado_pago'            => 'nullable|string|in:PAGADO,PENDIENTE',
        ];
    }

    public function messages(): array
    {
        return [
            // Cliente
            'cliente_nombre.required' => 'El nombre del cliente es obligatorio. No podemos hacer una reserva anónima.',
            'cliente_nombre.string'   => 'El nombre del cliente debe ser texto válido.',
            'cliente_nombre.max'      => 'El nombre del cliente es demasiado largo (máx. 255 caracteres).',

            // Teléfono
            'cliente_telf.required'   => 'El teléfono es obligatorio para contactar al cliente.',
            'cliente_telf.string'     => 'El formato del teléfono no es válido.',
            'cliente_telf.max'        => 'El teléfono es demasiado largo (máx. 20 caracteres).',

            // Precio
            'precio.required'         => 'Indica el precio total del pedido.',
            'precio.numeric'          => 'El precio debe ser un número (usa punto para los decimales).',
            'precio.min'              => 'El precio no puede ser negativo.',

            // Producto
            'producto.required'       => 'Debes especificar qué producto se reserva.',
            'producto.string'         => 'El producto debe ser texto válido.',
            'producto.max'            => 'El nombre del producto es demasiado largo (máx. 255 caracteres).',

            // Fecha (Recogida)
            'fecha.required'          => 'La fecha de recogida es obligatoria.',
            'fecha.date'              => 'La fecha introducida no es válida.',

            // Horario
            'horario.in'              => 'El horario solo puede ser: MAÑANA, TARDE o INDIFERENTE.',

            // Mensajes / Dedicatorias
            'texto_mensaje.string'    => 'El mensaje de la tarjeta debe ser texto.',
            'texto_mensaje.max'       => 'El mensaje es demasiado largo (máx. 500 caracteres).',
            'nombre_mensaje.string'   => 'El nombre de la firma/tarjeta debe ser texto.',
            'nombre_mensaje.max'      => 'El nombre de la firma es demasiado largo (máx. 255 caracteres).',

            // Dinero a cuenta
            'dinero_dejado_a_cuenta.numeric' => 'El dinero a cuenta debe ser una cifra numérica.',
            'dinero_dejado_a_cuenta.min'     => 'El dinero a cuenta no puede ser negativo.',

            // Estado Pago
            'estado_pago.in'          => 'El estado de pago solo puede ser: PAGADO o PENDIENTE.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación en la reserva.',
            'errors'  => $validator->errors()
        ], 422));
    }
}