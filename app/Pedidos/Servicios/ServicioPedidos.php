<?php

namespace App\Pedidos\Servicios;

use App\Models\Pedido;
use App\Pedidos\Contratos\ServicioContratoPedidos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ServicioPedidos
{
    public function __construct(
        private ServicioContratoPedidos $contratos
    ) {
    }

    public function crear(
        string $entidad,
        string $tipoPedido,
        string $relacionPedido,
        array $datos
    ): Model {
        $registro = DB::transaction(function () use ($entidad, $tipoPedido, $relacionPedido, $datos) {
            $separados = $this->contratos->separarDatos($entidad, $datos);

            $pedido = Pedido::create(array_merge($separados['pedido'], [
                'tipo_pedido' => $tipoPedido,
                'user_id' => null,
                'fuente' => $separados['pedido']['fuente'] ?? 'local',
            ]));

            return $pedido->{$relacionPedido}()->create($separados[$entidad]);
        });

        return $registro->load('pedido');
    }

    public function actualizar(string $entidad, Model $registro, array $datos): Model
    {
        DB::transaction(function () use ($entidad, $registro, $datos) {
            $separados = $this->contratos->separarDatos($entidad, $datos);

            if ($separados['pedido'] !== []) {
                $registro->pedido->update($separados['pedido']);
            }

            if ($separados[$entidad] !== []) {
                $registro->update($separados[$entidad]);
            }
        });

        return $registro->refresh()->load('pedido');
    }

    public function archivar(Model $registro): void
    {
        DB::transaction(function () use ($registro) {
            $registro->pedido()->delete();
            $registro->delete();
        });
    }

    public function restaurar(Model $registro): Model
    {
        if ($registro->trashed()) {
            $registro->restore();
        }

        if ($registro->pedido && $registro->pedido->trashed()) {
            $registro->pedido->restore();
        }

        return $registro->refresh()->load('pedido');
    }
}
