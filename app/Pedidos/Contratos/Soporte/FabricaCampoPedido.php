<?php

namespace App\Pedidos\Contratos\Soporte;

class FabricaCampoPedido
{
    public function texto(
        string $clave,
        string $modelo,
        string $columna,
        string $seccion,
        string $etiqueta,
        array $config = []
    ): array {
        return $this->construir($clave, $modelo, $columna, $seccion, $etiqueta, 'text', $config);
    }

    public function telefono(
        string $clave,
        string $modelo,
        string $columna,
        string $seccion,
        string $etiqueta,
        array $config = []
    ): array {
        return $this->construir($clave, $modelo, $columna, $seccion, $etiqueta, 'tel', $config);
    }

    public function oculto(
        string $clave,
        string $modelo,
        string $columna,
        string $seccion,
        string $etiqueta,
        array $config = []
    ): array {
        return $this->construir($clave, $modelo, $columna, $seccion, $etiqueta, 'hidden', $config);
    }

    public function textarea(
        string $clave,
        string $modelo,
        string $columna,
        string $seccion,
        string $etiqueta,
        array $config = []
    ): array {
        return $this->construir($clave, $modelo, $columna, $seccion, $etiqueta, 'textarea', $config);
    }

    public function numero(
        string $clave,
        string $modelo,
        string $columna,
        string $seccion,
        string $etiqueta,
        array $config = []
    ): array {
        return $this->construir($clave, $modelo, $columna, $seccion, $etiqueta, 'number', $config);
    }

    public function fecha(
        string $clave,
        string $modelo,
        string $columna,
        string $seccion,
        string $etiqueta,
        array $config = []
    ): array {
        return $this->construir($clave, $modelo, $columna, $seccion, $etiqueta, 'date', $config);
    }

    public function select(
        string $clave,
        string $modelo,
        string $columna,
        string $seccion,
        string $etiqueta,
        array $config = []
    ): array {
        return $this->construir($clave, $modelo, $columna, $seccion, $etiqueta, 'select', $config);
    }

    private function construir(
        string $clave,
        string $modelo,
        string $columna,
        string $seccion,
        string $etiqueta,
        string $entrada,
        array $config
    ): array {
        $reglas = $config['reglas'] ?? [];

        return [
            'clave' => $clave,
            'modelo' => $modelo,
            'columna' => $columna,
            'seccion' => $this->seccion($seccion),
            'etiqueta' => $etiqueta,
            'entrada' => $entrada,
            'valor_inicial' => $config['valor_inicial'] ?? '',
            'requerido' => [
                'crear' => $this->esRequerido($reglas['crear'] ?? null),
                'actualizar' => $this->esRequerido($reglas['actualizar'] ?? null),
            ],
            'restricciones' => $config['restricciones'] ?? [],
            'reglas' => $reglas,
            'mensajes' => $config['mensajes'] ?? [],
        ];
    }

    private function seccion(string $id): array
    {
        return match ($id) {
            'cliente' => ['id' => 'cliente', 'titulo' => 'Cliente'],
            'producto' => ['id' => 'producto', 'titulo' => 'Producto'],
            'fecha' => ['id' => 'fecha', 'titulo' => 'Fecha'],
            'envio' => ['id' => 'envio', 'titulo' => 'Envio'],
            'mensaje' => ['id' => 'mensaje', 'titulo' => 'Tarjeta'],
            'reserva' => ['id' => 'reserva', 'titulo' => 'Reserva'],
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
