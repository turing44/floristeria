<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use App\Models\Reserva;
use App\Pedidos\Recursos\ReservaResource;
use App\Pedidos\Servicios\ServicioPdfPedidos;
use App\Pedidos\Servicios\ServicioReservas;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function __construct(
        private ServicioReservas $servicioReservas,
        private ServicioPdfPedidos $servicioPdfs
    ) {
    }

    public function index(Request $request)
    {
        $query = Reserva::with('pedido')
            ->join('pedidos', 'reservas.pedido_id', '=', 'pedidos.id')
            ->select('reservas.*');

        if ($request->filled('telefono')) {
            $query->where('pedidos.cliente_telf', 'LIKE', '%' . $request->telefono . '%');
        }

        if ($request->has('ordenar')) {
            switch ($request->input('ordenar')) {
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

        $reservas = $query->get();

        return response()->json([
            'data' => ReservaResource::collection($reservas)->resolve(),
            'meta' => [
                'total' => $reservas->count(),
                'archivadas' => Reserva::onlyTrashed()->count(),
            ],
        ]);
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

    public function obtenerEliminadas()
    {
        $archivadas = Reserva::onlyTrashed()
            ->with('pedido')
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->json([
            'data' => ReservaResource::collection($archivadas)->resolve(),
            'meta' => [
                'total' => $archivadas->count(),
                'archivadas' => $archivadas->count(),
            ],
        ]);
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
}
