<?php

namespace App\Pedidos\Contratos\Definiciones;

use App\Pedidos\Contratos\Soporte\FabricaCampoPedido;

class CamposReserva
{
    public function __construct(
        private FabricaCampoPedido $campos
    ) {
    }

    public function obtener(): array
    {
        return [
            $this->campos->numero('hora_recogida', 'reserva', 'hora_recogida', 'fecha', 'Hora de recogida', [
                'reglas' => [
                    'crear' => 'nullable|integer|min:0|max:23',
                    'actualizar' => 'nullable|integer|min:0|max:23',
                ],
                'restricciones' => [
                    'min' => 0,
                    'max' => 23,
                    'step' => 1,
                    'inputMode' => 'numeric',
                    'placeholder' => 'Hora de recogida',
                ],
                'mensajes' => [
                    'hora_recogida.integer' => 'La hora de recogida debe ser un numero entero.',
                    'hora_recogida.min' => 'La hora de recogida no puede ser menor de 0.',
                    'hora_recogida.max' => 'La hora de recogida no puede ser mayor de 23.',
                ],
            ]),
            $this->campos->numero('dinero_pendiente', 'reserva', 'dinero_pendiente', 'reserva', 'Dinero pendiente', [
                'valor_inicial' => 0,
                'reglas' => [
                    'crear' => 'nullable|numeric|min:0',
                    'actualizar' => 'nullable|numeric|min:0',
                ],
                'restricciones' => [
                    'min' => 0,
                    'step' => '0.01',
                    'inputMode' => 'decimal',
                    'maximoCampo' => 'precio',
                    'mensajeMaximoCampo' => 'El dinero pendiente no puede ser mayor que el precio total del pedido.',
                    'placeholder' => '0.00',
                ],
                'mensajes' => [
                    'dinero_pendiente.numeric' => 'El dinero pendiente debe ser un numero.',
                    'dinero_pendiente.min' => 'El dinero pendiente no puede ser negativo.',
                ],
            ]),
        ];
    }
}
