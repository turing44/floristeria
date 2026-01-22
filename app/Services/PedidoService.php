<?php

namespace App\Services;

use App\Models\Pedido;

class PedidoService
{

    public function crearPedidoBase(array $datos, string $tipo): Pedido
    {
        return Pedido::create([
            'cliente_nombre' => $datos['cliente_nombre'],
            'cliente_telf'   => $datos['cliente_telf'],
            'precio'         => $datos['precio'],
            'producto'       => $datos['producto'],
            'fecha'          => $datos['fecha'],
            'horario'        => $datos['horario'] ?? 'INDIFERENTE',
            'observaciones'  => $datos['observaciones'] ?? null,
            'texto_mensaje'  => $datos['texto_mensaje'] ?? null,
            'nombre_mensaje' => $datos['nombre_mensaje'] ?? null,
            'fuente'         => $datos['fuente'] ?? 'Tienda',
            'tipo_pedido'    => $tipo,
            'user_id'        => null,
        ]);
    }

    public function actualizarPedidoBase(Pedido $pedido, array $datos): void
    {
            $pedido->update($datos);
        
    }
}