<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntregaRequest;
use App\Http\Requests\UpdateEntregaRequest;
use App\Models\Entrega;
use App\Pedidos\Recursos\EntregaResource;
use App\Pedidos\Servicios\ServicioEntregas;
use App\Pedidos\Servicios\ServicioPdfPedidos;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    public function __construct(
        private ServicioEntregas $servicioEntregas,
        private ServicioPdfPedidos $servicioPdfs
    ) {
    }

    public function index(Request $request)
    {
        $query = Entrega::with('pedido')
            ->join('pedidos', 'entregas.pedido_id', '=', 'pedidos.id')
            ->select('entregas.*');

        if ($request->filled('codigo_postal')) {
            $query->where('entregas.codigo_postal', 'LIKE', '%' . $request->codigo_postal . '%');
        }

        if ($request->filled('telefono')) {
            $telf = $request->telefono;
            $query->where(function($q) use ($telf) {
                $q->where('pedidos.cliente_telf', 'LIKE', '%' . $telf . '%')
                  ->orWhere('entregas.destinatario_telf', 'LIKE', '%' . $telf . '%');
            });
        }

        if ($request->has('ordenar')) {
            switch ($request->input('ordenar')) {
                case 'cp':
                    $query->orderBy('entregas.codigo_postal', 'asc');
                    break;
                case 'fecha_asc':
                    $query->orderBy('pedidos.fecha', 'asc');
                    break;
                case 'fecha_desc':
                    $query->orderBy('pedidos.fecha', 'desc');
                    break;
                default:
                    $query->orderBy('pedidos.fecha', 'desc');
                    break;
            }
        } else {
            $query->orderBy('pedidos.fecha', 'desc');
        }

        $entregas = $query->get();

        return response()->json([
            'data' => EntregaResource::collection($entregas)->resolve(),
            'meta' => [
                'total' => $entregas->count(),
                'archivadas' => Entrega::onlyTrashed()->count(),
            ],
        ]);
    }

    public function store(StoreEntregaRequest $request)
    {
        $entrega = $this->servicioEntregas->crear($request->validated());

        return $this->respuestaEntrega($entrega, 201);
    }

    public function show($id)
    {
        $entrega = Entrega::with('pedido')->findOrFail($id);

        return $this->respuestaEntrega($entrega);
    }

    public function update(UpdateEntregaRequest $request, $id)
    {
        $entrega = Entrega::with('pedido')->findOrFail($id);
        $entrega = $this->servicioEntregas->actualizar($entrega, $request->validated());

        return $this->respuestaEntrega($entrega);
    }

    public function destroy($id)
    {
        $entrega = Entrega::with('pedido')->findOrFail($id);
        $this->servicioEntregas->archivar($entrega);

        return response()->json(null, 204);
    }

    public function generarPdf(int $id)
    {
        $entrega = Entrega::withTrashed()->with('pedido')->findOrFail($id);

        try {
            $pdf = $this->servicioPdfs->obtenerEntrega($entrega);

            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="entrega_' . $entrega->id . '.pdf"');
        } catch (\Throwable $e) {
            return response('Error generando PDF: ' . $e->getMessage(), 500);
        }
    }

    public function obtenerEliminadas()
    {
        $archivadas = Entrega::onlyTrashed()
            ->with('pedido')
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->json([
            'data' => EntregaResource::collection($archivadas)->resolve(),
            'meta' => [
                'total' => $archivadas->count(),
                'archivadas' => $archivadas->count(),
            ],
        ]);
    }

    public function obtenerEntregaEliminada($id)
    {
        $entrega = Entrega::onlyTrashed()
            ->with('pedido')
            ->where('id', $id)
            ->firstOrFail();

        return $this->respuestaEntrega($entrega);
    }

    public function restaurar($id)
    {
        $entrega = Entrega::withTrashed()->with('pedido')->findOrFail($id);
        $entrega = $this->servicioEntregas->restaurar($entrega);

        return $this->respuestaEntrega($entrega);
    }

    private function respuestaEntrega(Entrega $entrega, int $status = 200)
    {
        return response()->json([
            'data' => (new EntregaResource($entrega))->resolve(),
        ], $status);
    }
}
