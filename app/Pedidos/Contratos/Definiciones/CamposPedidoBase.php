<?php

namespace App\Pedidos\Contratos\Definiciones;

use App\Pedidos\Contratos\Soporte\FabricaCampoPedido;

class CamposPedidoBase
{
    public function __construct(
        private FabricaCampoPedido $campos
    ) {
    }

    public function obtener(): array
    {
        return [
            $this->campos->texto('nombre_cliente', 'pedido', 'nombre_cliente', 'cliente', 'Cliente', [
                'reglas' => [
                    'crear' => 'required|string|max:40',
                    'actualizar' => 'nullable|string|max:40',
                ],
                'restricciones' => [
                    'maxLength' => 40,
                    'inputMode' => 'text',
                    'autoComplete' => 'off',
                    'placeholder' => 'Nombre del cliente',
                ],
                'mensajes' => [
                    'nombre_cliente.required' => 'El nombre del cliente es obligatorio.',
                    'nombre_cliente.max' => 'El nombre del cliente no puede superar los 40 caracteres.',
                ],
            ]),
            $this->campos->telefono('telefono_cliente', 'pedido', 'telefono_cliente', 'cliente', 'Telefono del cliente', [
                'reglas' => [
                    'crear' => 'required|string|max:20',
                    'actualizar' => 'nullable|string|max:20',
                ],
                'restricciones' => [
                    'maxLength' => 20,
                    'inputMode' => 'tel',
                    'autoComplete' => 'off',
                    'placeholder' => 'Telefono del cliente',
                ],
                'mensajes' => [
                    'telefono_cliente.required' => 'El telefono del cliente es obligatorio.',
                    'telefono_cliente.max' => 'El telefono del cliente es demasiado largo.',
                ],
            ]),
            $this->campos->oculto('fuente', 'pedido', 'fuente', 'interno', 'Fuente', [
                'valor_inicial' => 'local',
                'reglas' => [
                    'crear' => 'nullable|string',
                    'actualizar' => 'nullable|string',
                ],
            ]),
            $this->campos->textarea('producto', 'pedido', 'producto', 'producto', 'Producto', [
                'reglas' => [
                    'crear' => 'required|string|max:150',
                    'actualizar' => 'nullable|string|max:150',
                ],
                'restricciones' => [
                    'rows' => 2,
                    'maxLength' => 150,
                    'placeholder' => 'Ramo, centro o composicion',
                ],
                'mensajes' => [
                    'producto.required' => 'Debes especificar el producto.',
                    'producto.max' => 'El producto no puede superar los 150 caracteres.',
                ],
            ]),
            $this->campos->numero('precio', 'pedido', 'precio', 'producto', 'Precio EUR', [
                'reglas' => [
                    'crear' => 'required|numeric|min:0',
                    'actualizar' => 'nullable|numeric|min:0',
                ],
                'restricciones' => [
                    'min' => 0,
                    'step' => '0.01',
                    'inputMode' => 'decimal',
                    'placeholder' => '0.00',
                ],
                'mensajes' => [
                    'precio.required' => 'El precio es obligatorio.',
                    'precio.numeric' => 'El precio debe ser un numero.',
                    'precio.min' => 'El precio no puede ser negativo.',
                ],
            ]),
            $this->campos->fecha('fecha', 'pedido', 'fecha', 'fecha', 'Fecha', [
                'valor_inicial' => now()->format('Y-m-d'),
                'reglas' => [
                    'crear' => 'required|date',
                    'actualizar' => 'nullable|date',
                ],
                'mensajes' => [
                    'fecha.required' => 'La fecha es obligatoria.',
                    'fecha.date' => 'La fecha no es valida.',
                ],
            ]),
            $this->campos->textarea('observaciones', 'pedido', 'observaciones', 'observaciones', 'Observaciones (opcional)', [
                'reglas' => [
                    'crear' => 'nullable|string|max:230',
                    'actualizar' => 'nullable|string|max:230',
                ],
                'restricciones' => [
                    'rows' => 4,
                    'maxLength' => 230,
                    'placeholder' => 'Instrucciones internas o detalles de entrega',
                ],
                'mensajes' => [
                    'observaciones.max' => 'Las observaciones no pueden superar los 230 caracteres.',
                ],
            ]),
        ];
    }
}
