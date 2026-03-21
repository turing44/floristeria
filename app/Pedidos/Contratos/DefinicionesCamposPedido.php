<?php

namespace App\Pedidos\Contratos;

class DefinicionesCamposPedido
{
    public function obtener(string $entidad): array
    {
        return match ($entidad) {
            'entrega' => array_merge($this->camposBase(), $this->camposEntrega()),
            'reserva' => array_merge($this->camposBase(), $this->camposReserva()),
            default => throw new \InvalidArgumentException("Entidad desconocida: {$entidad}"),
        };
    }

    private function camposBase(): array
    {
        return [
            $this->campo('cliente_nombre', 'pedido', 'cliente_nombre', 'cliente', 'Cliente', 'text', '', [
                'crear' => 'required|string|max:40',
                'actualizar' => 'nullable|string|max:40',
            ], [
                'maxLength' => 40,
                'inputMode' => 'text',
                'autoComplete' => 'off',
            ]),
            $this->campo('cliente_telf', 'pedido', 'cliente_telf', 'cliente', 'Telefono cliente', 'tel', '', [
                'crear' => 'required|string|max:20',
                'actualizar' => 'nullable|string|max:20',
            ], [
                'maxLength' => 20,
                'inputMode' => 'tel',
                'autoComplete' => 'off',
            ]),
            $this->campo('fuente', 'pedido', 'fuente', 'interno', 'Fuente', 'hidden', 'local', [
                'crear' => 'nullable|string',
                'actualizar' => 'nullable|string',
            ]),
            $this->campo('producto', 'pedido', 'producto', 'producto', 'Producto', 'textarea', '', [
                'crear' => 'required|string|max:150',
                'actualizar' => 'nullable|string|max:150',
            ], [
                'rows' => 2,
                'maxLength' => 150,
            ]),
            $this->campo('precio', 'pedido', 'precio', 'producto', 'Precio EUR', 'number', '', [
                'crear' => 'required|numeric|min:0',
                'actualizar' => 'nullable|numeric|min:0',
            ], [
                'min' => 0,
                'step' => '0.01',
                'inputMode' => 'decimal',
            ]),
            $this->campo('fecha', 'pedido', 'fecha', 'fecha', 'Fecha', 'date', now()->format('Y-m-d'), [
                'crear' => 'required|date',
                'actualizar' => 'nullable|date',
            ]),
            $this->campo('observaciones', 'pedido', 'observaciones', 'observaciones', 'Observaciones (opcional)', 'textarea', '', [
                'crear' => 'nullable|string|max:230',
                'actualizar' => 'nullable|string|max:230',
            ], [
                'rows' => 4,
                'maxLength' => 230,
            ]),
        ];
    }

    private function camposEntrega(): array
    {
        return [
            $this->campo('horario', 'pedido', 'horario', 'fecha', 'Horario', 'select', 'INDIFERENTE', [
                'crear' => 'nullable|in:MANANA,TARDE,INDIFERENTE,MAÑANA',
                'actualizar' => 'nullable|in:MANANA,TARDE,INDIFERENTE,MAÑANA',
            ], [
                'options' => [
                    ['value' => 'INDIFERENTE', 'label' => 'INDIFERENTE'],
                    ['value' => 'MAÑANA', 'label' => 'MAÑANA'],
                    ['value' => 'TARDE', 'label' => 'TARDE'],
                ],
            ]),
            $this->campo('nombre_mensaje', 'pedido', 'nombre_mensaje', 'envio', 'Nombre del destinatario (ira en la tarjeta)', 'text', '', [
                'crear' => ['nullable', 'string', 'max:64', 'regex:/^\S{1,12}(?:\s+\S{1,12}){0,4}$/u'],
                'actualizar' => ['nullable', 'string', 'max:64', 'regex:/^\S{1,12}(?:\s+\S{1,12}){0,4}$/u'],
            ], [
                'maxLength' => 64,
                'autoComplete' => 'off',
                'pattern' => '^\S{1,12}(?:\s+\S{1,12}){0,4}$',
            ]),
            $this->campo('destinatario_telf', 'entrega', 'destinatario_telf', 'envio', 'Telefono del destinatario', 'tel', '', [
                'crear' => 'required|string|max:20',
                'actualizar' => 'nullable|string|max:20',
            ], [
                'maxLength' => 20,
                'inputMode' => 'tel',
                'autoComplete' => 'off',
            ]),
            $this->campo('direccion', 'entrega', 'direccion', 'envio', 'Direccion', 'text', '', [
                'crear' => 'required|string|max:255',
                'actualizar' => 'nullable|string|max:255',
            ], [
                'maxLength' => 255,
            ]),
            $this->campo('codigo_postal', 'entrega', 'codigo_postal', 'envio', 'Codigo postal', 'text', '', [
                'crear' => 'required|string|max:10',
                'actualizar' => 'nullable|string|max:10',
            ], [
                'maxLength' => 10,
                'inputMode' => 'numeric',
            ]),
            $this->campo('texto_mensaje', 'pedido', 'texto_mensaje', 'mensaje', 'Mensaje (opcional)', 'textarea', '', [
                'crear' => 'nullable|string|max:430',
                'actualizar' => 'nullable|string|max:430',
            ], [
                'rows' => 4,
                'maxLength' => 430,
            ]),
        ];
    }

    private function camposReserva(): array
    {
        return [
            $this->campo('hora_recogida', 'reserva', 'hora_recogida', 'fecha', 'Hora de recogida', 'number', '', [
                'crear' => 'nullable|integer|min:0|max:23',
                'actualizar' => 'nullable|integer|min:0|max:23',
            ], [
                'min' => 0,
                'max' => 23,
                'step' => 1,
                'inputMode' => 'numeric',
            ]),
            $this->campo('dinero_pendiente', 'reserva', 'dinero_pendiente', 'pago', 'Dinero pendiente', 'number', 0, [
                'crear' => 'nullable|numeric|min:0',
                'actualizar' => 'nullable|numeric|min:0',
            ], [
                'min' => 0,
                'step' => '0.01',
                'inputMode' => 'decimal',
                'maximoCampo' => 'precio',
            ]),
        ];
    }

    private function campo(
        string $clave,
        string $modelo,
        string $columna,
        string $seccionId,
        string $etiqueta,
        string $entrada,
        mixed $valorInicial,
        array $reglas,
        array $restricciones = []
    ): array {
        return [
            'clave' => $clave,
            'modelo' => $modelo,
            'columna' => $columna,
            'seccion' => $this->seccion($seccionId),
            'etiqueta' => $etiqueta,
            'entrada' => $entrada,
            'valor_inicial' => $valorInicial,
            'requerido' => [
                'crear' => $this->esRequerido($reglas['crear'] ?? null),
                'actualizar' => $this->esRequerido($reglas['actualizar'] ?? null),
            ],
            'restricciones' => $restricciones,
            'reglas' => $reglas,
        ];
    }

    private function seccion(string $id): array
    {
        return match ($id) {
            'cliente' => ['id' => 'cliente', 'titulo' => 'Cliente'],
            'producto' => ['id' => 'producto', 'titulo' => 'Producto'],
            'fecha' => ['id' => 'fecha', 'titulo' => 'Fecha'],
            'envio' => ['id' => 'envio', 'titulo' => 'Envio'],
            'mensaje' => ['id' => 'mensaje', 'titulo' => 'Texto mensaje'],
            'pago' => ['id' => 'pago', 'titulo' => 'Pago'],
            'observaciones' => ['id' => 'observaciones', 'titulo' => 'Observaciones'],
            default => ['id' => 'interno', 'titulo' => 'Interno'],
        };
    }

    private function esRequerido(string|array|null $reglas): bool
    {
        if ($reglas === null) {
            return false;
        }

        $lista = is_array($reglas) ? $reglas : explode('|', $reglas);

        return in_array('required', $lista, true);
    }
}
