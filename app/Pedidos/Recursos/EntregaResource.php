<?php

namespace App\Pedidos\Recursos;

use App\Pedidos\Contratos\ServicioContratoPedidos;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntregaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return app(ServicioContratoPedidos::class)->serializarModelo('entrega', $this->resource);
    }
}
