<?php

namespace App\Http\Controllers;

use App\Pedidos\Contratos\ServicioContratoPedidos;
use Illuminate\Http\Request;

class ContratoPedidoController extends Controller
{
    public function entrega(Request $request, ServicioContratoPedidos $contratos)
    {
        $operacion = $this->resolverOperacion($request);

        return response()->json($contratos->obtenerContratoFormulario('entrega', $operacion));
    }

    public function reserva(Request $request, ServicioContratoPedidos $contratos)
    {
        $operacion = $this->resolverOperacion($request);

        return response()->json($contratos->obtenerContratoFormulario('reserva', $operacion));
    }

    private function resolverOperacion(Request $request): string
    {
        $operacion = $request->query('operacion', 'crear');

        return in_array($operacion, ['crear', 'actualizar'], true)
            ? $operacion
            : 'crear';
    }
}
