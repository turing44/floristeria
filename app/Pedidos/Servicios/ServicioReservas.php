<?php

namespace App\Pedidos\Servicios;

use App\Models\Pedido;
use App\Models\Reserva;
use App\Pedidos\Contratos\ServicioContratoPedidos;
use Illuminate\Support\Facades\DB;

class ServicioReservas
{
    public function __construct(
        private ServicioContratoPedidos $contratos,
        private ServicioPdfPedidos $pdfs
    ) {
    }

    public function crear(array $datos): Reserva
    {
        $reserva = DB::transaction(function () use ($datos) {
            $separados = $this->contratos->separarDatos('reserva', $datos);

            $pedido = Pedido::create(array_merge($separados['pedido'], [
                'tipo_pedido' => 'TIENDA',
                'user_id' => null,
                'fuente' => $separados['pedido']['fuente'] ?? 'local',
            ]));

            return $pedido->reserva()->create($separados['reserva']);
        });

        $reserva->load('pedido');
        $this->pdfs->guardarReservaSinRomper($reserva);

        return $reserva;
    }

    public function actualizar(Reserva $reserva, array $datos): Reserva
    {
        DB::transaction(function () use ($reserva, $datos) {
            $separados = $this->contratos->separarDatos('reserva', $datos);

            if ($separados['pedido'] !== []) {
                $reserva->pedido->update($separados['pedido']);
            }

            if ($separados['reserva'] !== []) {
                $reserva->update($separados['reserva']);
            }
        });

        $reserva->refresh()->load('pedido');
        $this->pdfs->guardarReservaSinRomper($reserva);

        return $reserva;
    }

    public function archivar(Reserva $reserva): void
    {
        DB::transaction(function () use ($reserva) {
            $reserva->pedido()->delete();
            $reserva->delete();
        });
    }

    public function restaurar(Reserva $reserva): Reserva
    {
        if ($reserva->trashed()) {
            $reserva->restore();
        }

        if ($reserva->pedido && $reserva->pedido->trashed()) {
            $reserva->pedido->restore();
        }

        return $reserva->refresh()->load('pedido');
    }
}
