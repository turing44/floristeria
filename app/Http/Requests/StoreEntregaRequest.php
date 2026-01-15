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
    /**
     * Mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
          
            
            'fuente.required' => '¡Eh! Necesito saber de dónde viene el pedido (fuente).',
            'fuente.in'       => 'La fuente solo puede ser: Glovo, Web, Tienda o Telefono.',

            // 2. Errores del Producto
            'producto.required' => 'No podemos vender aire. Escribe qué producto es.',
            
            // 3. Errores de Precios
            'precio.required' => 'Pon un precio, que esto no es una ONG.',
            'precio.numeric'  => 'El precio tiene que ser un número (usa punto para decimales).',
            'precio.min'      => 'El precio no puede ser negativo.',

            // 4. Errores de Fechas
            'fecha_entrega.required'       => '¿Cuándo lo entregamos? Falta la fecha.',
            'fecha_entrega.date'           => 'Formato de fecha inválido.',
            'fecha_entrega.after_or_equal' => 'No tenemos un DeLorean. La fecha debe ser hoy o en el futuro.',

            // 5. Errores de Cliente/Destinatario
            'cliente.required'          => 'Necesito el nombre del cliente.',
            'telf_cliente.required'     => 'Falta el teléfono del cliente.',
            'destinatario.required'     => '¿A quién se lo llevamos? Falta el destinatario.',
            
            // 6. Genéricos (Atajo para todos los 'required' que no hayas definido arriba)
            'required' => 'El campo :attribute es obligatorio.',
        ];
    }
}