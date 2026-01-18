<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEntregaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fuente'            => 'nullable|string',
            'producto'          => 'nullable|string',
            'direccion'         => 'nullable|string|max:255',
            'codigo_postal'     => 'nullable|string|max:10',
            'destinatario_telf' => 'nullable|string|max:20',
            'cliente_nombre'    => 'nullable|string|max:255',
            'cliente_telf'      => 'nullable|string|max:20',
            'fecha'             => 'nullable|date',
            'precio'            => 'nullable|numeric|min:0',
            'observaciones'     => 'nullable|string',
            'horario'           => 'nullable|in:MAÑANA,TARDE,INDIFERENTE',
            'texto_mensaje'     => 'nullable|string',
            'nombre_mensaje'    => 'nullable|string|max:255',
            'estado'            => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'fuente.string'              => 'La fuente debe ser un texto válido.',

            'producto.string'            => 'El producto debe ser un texto válido.',

            'direccion.string'           => 'La dirección debe ser un texto válido.',
            'direccion.max'              => 'La dirección no puede superar los 255 caracteres.',

            'codigo_postal.string'       => 'El código postal debe ser un texto válido.',
            'codigo_postal.max'          => 'El código postal no puede superar los 10 caracteres.',

            'destinatario_telf.string'   => 'El teléfono del destinatario debe ser un texto válido.',
            'destinatario_telf.max'      => 'El teléfono del destinatario no puede superar los 20 caracteres.',

            'cliente_nombre.string'      => 'El nombre del cliente debe ser un texto válido.',
            'cliente_nombre.max'         => 'El nombre del cliente no puede superar los 255 caracteres.',

            'cliente_telf.string'        => 'El teléfono del cliente debe ser un texto válido.',
            'cliente_telf.max'           => 'El teléfono del cliente no puede superar los 20 caracteres.',

            'fecha.date'                 => 'La fecha de entrega no es válida.',

            'precio.numeric'             => 'El precio debe ser un número válido.',
            'precio.min'                 => 'El precio no puede ser negativo.',

            'observaciones.string'       => 'Las observaciones deben ser un texto válido.',

            'horario.in'                 => 'El horario debe ser MAÑANA, TARDE o INDIFERENTE.',

            'texto_mensaje.string'       => 'El texto de la tarjeta debe ser un texto válido.',

            'nombre_mensaje.string'      => 'El nombre de la tarjeta debe ser un texto válido.',
            'nombre_mensaje.max'         => 'El nombre de la tarjeta no puede superar los 255 caracteres.',

            'estado.string'              => 'El estado debe ser un texto válido.',
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
