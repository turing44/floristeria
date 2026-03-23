<?php

namespace App\Pedidos\Contratos\Definiciones;

use App\Pedidos\Contratos\Soporte\FabricaCampoPedido;

class CamposEntrega
{
    public function __construct(
        private FabricaCampoPedido $campos
    ) {
    }

    public function obtener(): array
    {
        return [
            $this->campos->select('horario', 'pedido', 'horario', 'fecha', 'Horario', [
                'valor_inicial' => 'INDIFERENTE',
                'reglas' => [
                    'crear' => 'nullable|in:MANANA,TARDE,INDIFERENTE,MAÑANA',
                    'actualizar' => 'nullable|in:MANANA,TARDE,INDIFERENTE,MAÑANA',
                ],
                'restricciones' => [
                    'options' => [
                        ['value' => 'INDIFERENTE', 'label' => 'INDIFERENTE'],
                        ['value' => 'MAÑANA', 'label' => 'MAÑANA'],
                        ['value' => 'TARDE', 'label' => 'TARDE'],
                    ],
                    'normalizar' => [
                        'MANANA' => 'MAÑANA',
                    ],
                ],
                'mensajes' => [
                    'horario.in' => 'El horario debe ser MAÑANA, TARDE o INDIFERENTE.',
                ],
            ]),
            $this->campos->texto('nombre_destinatario', 'pedido', 'nombre_destinatario', 'envio', 'Nombre del destinatario (ira en la tarjeta)', [
                'reglas' => [
                    'crear' => ['nullable', 'string', 'max:64', 'regex:/^\\S{1,12}(?:\\s+\\S{1,12}){0,4}$/u'],
                    'actualizar' => ['nullable', 'string', 'max:64', 'regex:/^\\S{1,12}(?:\\s+\\S{1,12}){0,4}$/u'],
                ],
                'restricciones' => [
                    'maxLength' => 64,
                    'autoComplete' => 'off',
                    'pattern' => '^\\S{1,12}(?:\\s+\\S{1,12}){0,4}$',
                    'placeholder' => 'Nombre para la tarjeta',
                ],
                'mensajes' => [
                    'nombre_destinatario.regex' => 'El destinatario puede tener como maximo 5 palabras y cada una no puede superar los 12 caracteres.',
                ],
            ]),
            $this->campos->telefono('telefono_destinatario', 'entrega', 'telefono_destinatario', 'envio', 'Telefono del destinatario', [
                'reglas' => [
                    'crear' => 'required|string|max:20',
                    'actualizar' => 'nullable|string|max:20',
                ],
                'restricciones' => [
                    'maxLength' => 20,
                    'inputMode' => 'tel',
                    'autoComplete' => 'off',
                    'placeholder' => 'Telefono del destinatario',
                ],
                'mensajes' => [
                    'telefono_destinatario.required' => 'El telefono del destinatario es obligatorio.',
                ],
            ]),
            $this->campos->texto('direccion', 'entrega', 'direccion', 'envio', 'Direccion', [
                'reglas' => [
                    'crear' => 'required|string|max:255',
                    'actualizar' => 'nullable|string|max:255',
                ],
                'restricciones' => [
                    'maxLength' => 255,
                    'placeholder' => 'Calle, numero o referencia',
                ],
                'mensajes' => [
                    'direccion.required' => 'La direccion es obligatoria para envios.',
                ],
            ]),
            $this->campos->texto('codigo_postal', 'entrega', 'codigo_postal', 'envio', 'Codigo postal', [
                'reglas' => [
                    'crear' => 'required|string|max:10',
                    'actualizar' => 'nullable|string|max:10',
                ],
                'restricciones' => [
                    'maxLength' => 10,
                    'inputMode' => 'numeric',
                    'placeholder' => 'Codigo postal',
                ],
                'mensajes' => [
                    'codigo_postal.required' => 'El codigo postal es obligatorio.',
                ],
            ]),
            $this->campos->textarea('mensaje_tarjeta', 'pedido', 'mensaje_tarjeta', 'mensaje', 'Mensaje (opcional)', [
                'reglas' => [
                    'crear' => 'nullable|string|max:430',
                    'actualizar' => 'nullable|string|max:430',
                ],
                'restricciones' => [
                    'rows' => 4,
                    'maxLength' => 430,
                    'placeholder' => 'Texto de la tarjeta',
                ],
                'mensajes' => [
                    'mensaje_tarjeta.max' => 'El mensaje no puede superar los 430 caracteres.',
                ],
            ]),
        ];
    }
}
