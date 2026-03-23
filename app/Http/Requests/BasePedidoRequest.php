<?php

namespace App\Http\Requests;

use App\Pedidos\Contratos\ServicioContratoPedidos;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BasePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    abstract protected function entidadContrato(): string;

    abstract protected function operacionContrato(): string;

    public function rules(): array
    {
        return $this->contratos()->obtenerReglas(
            $this->entidadContrato(),
            $this->operacionContrato()
        );
    }

    public function messages(): array
    {
        return $this->contratos()->obtenerMensajes($this->entidadContrato());
    }

    protected function prepareForValidation(): void
    {
        $this->replace(
            $this->contratos()->normalizarEntrada($this->entidadContrato(), $this->all())
        );
    }

    protected function withValidator($validator): void
    {
        $this->contratos()->aplicarValidacionesAdicionales(
            $this->entidadContrato(),
            $validator,
            $this->all()
        );
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación.',
            'errors'  => $validator->errors()
        ], 422));
    }

    protected function contratos(): ServicioContratoPedidos
    {
        return app(ServicioContratoPedidos::class);
    }
}
