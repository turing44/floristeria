<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Services\PedidoService;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservaController extends Controller
{
    protected $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
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
        return response()->json([
            "num_reservas"   => $query->count(),
            "num_archivadas" => Reserva::onlyTrashed()->count(),
            "reservas"       => $query->get()
        ]);
    }

    public function store(StoreReservaRequest $request)
    {
        $datos = $request->validated();

        try {
            return DB::transaction(function () use ($datos) {
                $pedido = $this->pedidoService->crearPedidoBase($datos, 'TIENDA');
                $reserva = $pedido->reserva()->create([
                    'dinero_pendiente' => $datos['dinero_pendiente'] ?? 0,
                    'estado_pago'   => $datos['estado_pago'] ?? 'PENDIENTE',
                    'hora_recogida' => $datos['hora_recogida'] ?? null,
                ]);

                return response()->json($reserva->load('pedido'), 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return Reserva::with('pedido')->findOrFail($id);
    }

    public function update(UpdateReservaRequest $request, $id)
    {
        $reserva = Reserva::with('pedido')->findOrFail($id);
        $datos = $request->validated();

        try {
            return DB::transaction(function () use ($reserva, $datos) {
                $this->pedidoService->actualizarPedidoBase($reserva->pedido, $datos);
                $reserva->update($datos);

                return response()->json($reserva->load('pedido'), 200);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $reserva = Reserva::findOrFail($id);
                $reserva->pedido()->delete();
                $reserva->delete();
                return response()->json(null, 204);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
    public function generarPdf(int $id)
    {
        $reserva = Reserva::withTrashed()->with('pedido')->findOrFail($id);

        $html = view('pdf.reserva', ['reserva' => $reserva])->render(); 

        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];

        $process = proc_open('node "' . base_path('resources/js/generar_pdf.cjs') . '"', $descriptorspec, $pipes);

        if (is_resource($process)) {
            fwrite($pipes[0], $html);
            fclose($pipes[0]);

            $pdfBinary = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            
            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $status = proc_close($process);

            if ($status !== 0) {
                Log::error("Error Generando PDF: $errors");
                return response("Error generando PDF: $errors", 500);
            }

            return response($pdfBinary)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="reserva_'.$reserva->id.'.pdf"');
        }

        return response('Error iniciando Node.js', 500);
    }



    public function obtenerEliminadas()
    {
        $archivadas = Reserva::onlyTrashed()
            ->with(['pedido' => fn ($p) => $p->withTrashed()])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->json([
            "num_archivadas" => $archivadas->count(),
            "reservas"       => $archivadas
        ]);
    }

    public function obtenerReservaEliminada($id)
    {
        return Reserva::onlyTrashed()
            ->with(['pedido' => fn ($p) => $p->withTrashed()])
            ->where('id', $id)
            ->firstOrFail();
    }

        public function restaurar($id)
    {
        $reserva = Reserva::withTrashed()->with('pedido')->findOrFail($id);
        $reserva->restore();

        if ($reserva->pedido && $reserva->pedido->trashed()) {
            $reserva->pedido->restore();
        }

        return response()->json([
            'mensaje' => 'Reserva y Pedido restaurados correctamente',
            'reserva' => $reserva
        ]);
    } 
}