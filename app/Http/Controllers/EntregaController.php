<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntregaRequest;
use App\Http\Requests\UpdateEntregaRequest;
use App\Models\Entrega;
use App\Pedidos\Recursos\EntregaResource;
use App\Pedidos\Servicios\ServicioEntregas;
use App\Pedidos\Servicios\ServicioListadoPedidos;
use App\Pedidos\Servicios\ServicioPdfPedidos;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    public function __construct(
        private ServicioEntregas $servicioEntregas,
        private ServicioListadoPedidos $servicioListados,
        private ServicioPdfPedidos $servicioPdfs
    ) {
    }

    public function index(Request $request)
    {
        return $this->respuestaListado(
            $this->servicioListados->listarEntregas($request->all())
        );
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

    public function obtenerEliminadas(Request $request)
    {
        return $this->respuestaListado(
            $this->servicioListados->listarEntregas(array_merge($request->all(), [
                'archivados' => true,
            ]))
        );
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

    private function respuestaListado(array $resultado)
    {
        return response()->json([
            'data' => EntregaResource::collection($resultado['registros'])->resolve(),
            'meta' => $resultado['meta'],
        ]);
    }
}
