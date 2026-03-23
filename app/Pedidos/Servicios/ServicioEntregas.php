<?php

namespace App\Pedidos\Servicios;

use App\Models\Entrega;

class ServicioEntregas
{
    public function __construct(
        private ServicioPedidos $pedidos,
        private ServicioPdfPedidos $pdfs
    ) {
    }

    public function crear(array $datos): Entrega
    {
        $entrega = $this->pedidos->crear('entrega', 'DOMICILIO', 'entrega', $datos);
        $this->pdfs->guardarEntregaSinRomper($entrega);

        return $entrega;
    }

    public function actualizar(Entrega $entrega, array $datos): Entrega
    {
        $entrega = $this->pedidos->actualizar('entrega', $entrega, $datos);
        $this->pdfs->guardarEntregaSinRomper($entrega);

        return $entrega;
    }

    public function archivar(Entrega $entrega): void
    {
        $this->pedidos->archivar($entrega);
    }

    public function restaurar(Entrega $entrega): Entrega
    {
        return $this->pedidos->restaurar($entrega);
    }
}
