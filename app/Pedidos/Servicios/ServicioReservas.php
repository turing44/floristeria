<?php

namespace App\Pedidos\Servicios;

use App\Models\Reserva;

class ServicioReservas
{
    public function __construct(
        private ServicioPedidos $pedidos,
        private ServicioPdfPedidos $pdfs
    ) {
    }

    public function crear(array $datos): Reserva
    {
        $reserva = $this->pedidos->crear('reserva', 'TIENDA', 'reserva', $datos);
        $this->pdfs->guardarReservaSinRomper($reserva);

        return $reserva;
    }

    public function actualizar(Reserva $reserva, array $datos): Reserva
    {
        $reserva = $this->pedidos->actualizar('reserva', $reserva, $datos);
        $this->pdfs->guardarReservaSinRomper($reserva);

        return $reserva;
    }

    public function archivar(Reserva $reserva): void
    {
        $this->pedidos->archivar($reserva);
    }

    public function restaurar(Reserva $reserva): Reserva
    {
        return $this->pedidos->restaurar($reserva);
    }
}
