<?php

namespace App\Http\Requests;

use App\Pedidos\Contratos\ServicioContratoPedidos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BasePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    abstract protected function entidadContrato(): string;

    abstract protected function operacionContrato(): string;

    protected function reglasDesdeContrato(): array
    {
        return app(ServicioContratoPedidos::class)->obtenerReglas(
            $this->entidadContrato(),
            $this->operacionContrato()
        );
    }

    public function messages(): array
    {
        return [
            'cliente_nombre.required' => 'El nombre del cliente es obligatorio.',
            'cliente_nombre.max' => 'El nombre del cliente no puede superar los 40 caracteres.',
            'cliente_telf.required' => 'El telefono del cliente es obligatorio.',
            'cliente_telf.max' => 'El telefono del cliente es demasiado largo.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un numero.',
            'precio.min' => 'El precio no puede ser negativo.',
            'producto.required' => 'Debes especificar el producto.',
            'producto.max' => 'El producto no puede superar los 150 caracteres.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no es valida.',
            'horario.in' => 'El horario debe ser MAÑANA, TARDE o INDIFERENTE.',
            'nombre_mensaje.regex' => 'El destinatario puede tener como maximo 5 palabras y cada una no puede superar los 12 caracteres.',
            'texto_mensaje.max' => 'El mensaje no puede superar los 430 caracteres.',
            'direccion.required' => 'La direccion es obligatoria para envios.',
            'codigo_postal.required' => 'El codigo postal es obligatorio.',
            'destinatario_telf.required' => 'El teléfono del destinatario es obligatorio.',
            'hora_recogida.integer' => 'La hora de recogida debe ser un numero entero.',
            'hora_recogida.min' => 'La hora de recogida no puede ser menor de 0.',
            'hora_recogida.max' => 'La hora de recogida no puede ser mayor de 23.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('horario') === 'MANANA') {
            $this->merge(['horario' => 'MAÑANA']);
        }
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->camposContrato() as $campo) {
                $restricciones = $campo['restricciones'] ?? [];

                if (!isset($restricciones['maximoCampo'])) {
                    continue;
                }

                $clave = $campo['clave'];
                $campoLimite = $restricciones['maximoCampo'];
                $valor = $this->input($clave);
                $limite = $this->input($campoLimite);

                if ($valor === null || $valor === '' || $limite === null || $limite === '') {
                    continue;
                }

                if ((float) $valor > (float) $limite) {
                    $validator->errors()->add(
                        $clave,
                        'El valor no puede ser mayor que ' . $campoLimite . '.'
                    );
                }
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación.',
            'errors'  => $validator->errors()
        ], 422));
    }

    protected function camposContrato(): array
    {
        return app(ServicioContratoPedidos::class)->obtenerCampos($this->entidadContrato());
    }
}
