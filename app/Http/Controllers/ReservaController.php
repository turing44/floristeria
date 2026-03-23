<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use App\Models\Reserva;
use App\Pedidos\Recursos\ReservaResource;
use App\Pedidos\Servicios\ServicioListadoPedidos;
use App\Pedidos\Servicios\ServicioPdfPedidos;
use App\Pedidos\Servicios\ServicioReservas;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function __construct(
        private ServicioReservas $servicioReservas,
        private ServicioListadoPedidos $servicioListados,
        private ServicioPdfPedidos $servicioPdfs
    ) {
    }

    public function index(Request $request)
    {
        return $this->respuestaListado(
            $this->servicioListados->listarReservas($request->all())
        );
    }

    public function store(StoreReservaRequest $request)
    {
        $reserva = $this->servicioReservas->crear($request->validated());

        return $this->respuestaReserva($reserva, 201);
    }

    public function show($id)
    {
        $reserva = Reserva::with('pedido')->findOrFail($id);

        return $this->respuestaReserva($reserva);
    }

    public function update(UpdateReservaRequest $request, $id)
    {
        $reserva = Reserva::with('pedido')->findOrFail($id);
        $reserva = $this->servicioReservas->actualizar($reserva, $request->validated());

        return $this->respuestaReserva($reserva);
    }

    public function destroy($id)
    {
        $reserva = Reserva::with('pedido')->findOrFail($id);
        $this->servicioReservas->archivar($reserva);

        return response()->json(null, 204);
    }

    public function generarPdf(int $id)
    {
        $reserva = Reserva::withTrashed()->with('pedido')->findOrFail($id);

        try {
            $pdf = $this->servicioPdfs->obtenerReserva($reserva);

            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="reserva_' . $reserva->id . '.pdf"');
        } catch (\Throwable $e) {
            return response('Error generando PDF: ' . $e->getMessage(), 500);
        }
    }

    public function obtenerEliminadas(Request $request)
    {
        return $this->respuestaListado(
            $this->servicioListados->listarReservas(array_merge($request->all(), [
                'archivados' => true,
            ]))
        );
    }

    public function obtenerReservaEliminada($id)
    {
        $reserva = Reserva::onlyTrashed()
            ->with('pedido')
            ->where('id', $id)
            ->firstOrFail();

        return $this->respuestaReserva($reserva);
    }

    public function restaurar($id)
    {
        $reserva = Reserva::withTrashed()->with('pedido')->findOrFail($id);
        $reserva = $this->servicioReservas->restaurar($reserva);

        return $this->respuestaReserva($reserva);
    }

    private function respuestaReserva(Reserva $reserva, int $status = 200)
    {
        return response()->json([
            'data' => (new ReservaResource($reserva))->resolve(),
        ], $status);
    }

    private function respuestaListado(array $resultado)
    {
        return response()->json([
            'data' => ReservaResource::collection($resultado['registros'])->resolve(),
            'meta' => $resultado['meta'],
        ]);
    }
}
