<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
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
            'cliente' => 'required|string|max:255',
            'telf_cliente' => 'required|string|max:20',
            'precio' => 'required|numeric|min:0',
            'dinero_a_cuenta' => 'nullable|numeric|min:0',
            'fecha_recogida' => 'required|date|after_or_equal:today',
            'observaciones' => 'nullable|string|max:500',
            'horario' => 'required|in:MAÑANA,Tarde,INDIFERENTE',
            'destinatario' => 'nullable|string|max:255',
            'mensaje' => 'nullable|string|max:255',
            'estado' => 'required|in:Archivado,Pendiente,Activo',
        ];
    }
    
    public function messages(): array
    {
        return [

            'cliente.required' => 'El nombre del cliente es obligatorio.',
            'cliente.string' => 'El nombre del cliente debe ser texto.',
            'telf_cliente.required' => 'El teléfono del cliente es obligatorio.',
            'telf_cliente.string' => 'El teléfono debe ser texto.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'dinero_a_cuenta.numeric' => 'El dinero a cuenta debe ser un número.',
            'fecha_recogida.required' => 'La fecha de entrega es obligatoria.',
            'fecha_recogida.date' => 'La fecha de entrega debe ser una fecha válida.',
            'fecha_recogida.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy.',
            'horario.required' => 'El horario es obligatorio.',
            'horario.in' => 'El horario debe ser Mañana, Tarde o Indiferente.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser Archivado, Pendiente o Activo.',
            
        ];
    }
}
