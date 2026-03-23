<?php

namespace App\Pedidos\Contratos;

use App\Pedidos\Contratos\Definiciones\CamposEntrega;
use App\Pedidos\Contratos\Definiciones\CamposPedidoBase;
use App\Pedidos\Contratos\Definiciones\CamposReserva;

class DefinicionesCamposPedido
{
    public function __construct(
        private CamposPedidoBase $camposBase,
        private CamposEntrega $camposEntrega,
        private CamposReserva $camposReserva
    ) {
    }

    public function obtener(string $entidad): array
    {
        return match ($entidad) {
            'entrega' => array_merge($this->camposBase->obtener(), $this->camposEntrega->obtener()),
            'reserva' => array_merge($this->camposBase->obtener(), $this->camposReserva->obtener()),
            default => throw new \InvalidArgumentException("Entidad desconocida: {$entidad}"),
        };
    }
}
