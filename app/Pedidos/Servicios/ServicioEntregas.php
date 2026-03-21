<?php

namespace App\Pedidos\Servicios;

use App\Models\Entrega;
use App\Models\Pedido;
use App\Pedidos\Contratos\ServicioContratoPedidos;
use Illuminate\Support\Facades\DB;

class ServicioEntregas
{
    public function __construct(
        private ServicioContratoPedidos $contratos,
        private ServicioPdfPedidos $pdfs
    ) {
    }

    public function crear(array $datos): Entrega
    {
        $entrega = DB::transaction(function () use ($datos) {
            $separados = $this->contratos->separarDatos('entrega', $datos);

            $pedido = Pedido::create(array_merge($separados['pedido'], [
                'tipo_pedido' => 'DOMICILIO',
                'user_id' => null,
                'fuente' => $separados['pedido']['fuente'] ?? 'local',
            ]));

            return $pedido->entrega()->create($separados['entrega']);
        });

        $entrega->load('pedido');
        $this->pdfs->guardarEntregaSinRomper($entrega);

        return $entrega;
    }

    public function actualizar(Entrega $entrega, array $datos): Entrega
    {
        DB::transaction(function () use ($entrega, $datos) {
            $separados = $this->contratos->separarDatos('entrega', $datos);

            if ($separados['pedido'] !== []) {
                $entrega->pedido->update($separados['pedido']);
            }

            if ($separados['entrega'] !== []) {
                $entrega->update($separados['entrega']);
            }
        });

        $entrega->refresh()->load('pedido');
        $this->pdfs->guardarEntregaSinRomper($entrega);

        return $entrega;
    }

    public function archivar(Entrega $entrega): void
    {
        DB::transaction(function () use ($entrega) {
            $entrega->pedido()->delete();
            $entrega->delete();
        });
    }

    public function restaurar(Entrega $entrega): Entrega
    {
        if ($entrega->trashed()) {
            $entrega->restore();
        }

        if ($entrega->pedido && $entrega->pedido->trashed()) {
            $entrega->pedido->restore();
        }

        return $entrega->refresh()->load('pedido');
    }
}
