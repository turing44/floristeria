<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// para forzar 
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            "producto" => "required|string|max:255",
            "direccion" => "required|string|max:255",
            "destinatario" => "required|string|max:255",
            "destinatario_telf" => "required|string|max:255",
            "cliente" => "required|string|max:255",
            "cliente_telf" => "required|string|max:255",
            "fecha_entrega" => "required|date",
            "observaciones" => "nullable|string|max:255",
            "horario" => "nullable|string|in:Mañana,Tarde",
            "mensaje" => "nullable|string|max:255",
        ];
    }

    public function messages(): array {
        return [
            "producto.required" => "Debe especificar un producto",
            "direccion.required" => "Debe especificar la direccion",
            "destinatario.required" => "Debe especificar el destinatario",
            "destinatario_telf.required" => "Debe especificar el telefono del destinatario",
            "cliente.required" => "Debe especificar el cliente",
            "cliente_telf.required" => "Debe especificar el telefono del cliente",
            "fecha_entrega.required" => "Debe especificar la fecha de entrega",
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(
            response()->json(
                [
                    "message" => "Los datos no son validos",
                    "errors" => $validator->errors()
                ], 422
            )
            );
    }
}
