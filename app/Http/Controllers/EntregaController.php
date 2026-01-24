<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Services\PedidoService;
use App\Http\Requests\StoreEntregaRequest;
use App\Http\Requests\UpdateEntregaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntregaController extends Controller
{
    protected $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
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
        return response()->json([
            "num_entregas"   => $query->count(),
            "num_archivadas" => Entrega::onlyTrashed()->count(),
            "entregas"       => $query->get()
        ]);
    }

    public function store(StoreEntregaRequest $request)
    {
        $datos = $request->validated();

        try {
            return DB::transaction(function () use ($datos) {
                $pedido = $this->pedidoService->crearPedidoBase($datos, 'DOMICILIO');

                $entrega = $pedido->entrega()->create([
                    'direccion'         => $datos['direccion'],
                    'codigo_postal'     => $datos['codigo_postal'],
                    'destinatario_telf' => $datos['destinatario_telf'],
                ]);

                return response()->json($entrega->load('pedido'), 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return Entrega::with('pedido')->findOrFail($id);
    }

    public function update(UpdateEntregaRequest $request, $id)
    {
        $entrega = Entrega::with('pedido')->findOrFail($id);
        $datos = $request->validated();

        try {
            return DB::transaction(function () use ($entrega, $datos) {
                $this->pedidoService->actualizarPedidoBase($entrega->pedido, $datos);
                $entrega->update($datos);

                return response()->json($entrega->load('pedido'), 200);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $entrega = Entrega::findOrFail($id);
                $entrega->pedido()->delete(); 
                $entrega->delete();      
                return response()->json(null, 204);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
    
    public function generarPdf(int $id)
    {
        $entrega = Entrega::withTrashed()->with('pedido')->findOrFail($id);   

        $html = view('pdf.albaran', compact('entrega'))->render(); 

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
                ->header('Content-Disposition', 'inline; filename="entrega_'.$entrega->id.'.pdf"');
        }

        return response('Error iniciando Node.js', 500);
    }


    public function obtenerEliminadas()
    {
        $archivadas = Entrega::onlyTrashed()
            ->with(['pedido' => fn ($p) => $p->withTrashed()])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->json([
            "num_archivadas" => $archivadas->count(),
            "entregas"       => $archivadas
        ]);
    }

    public function obtenerEntregaEliminada($id)
    {
        return Entrega::onlyTrashed()
            ->with(['pedido' => fn ($p) => $p->withTrashed()])
            ->where('id', $id)
            ->firstOrFail();
    }

    public function restaurar($id)
    {
        $entrega = Entrega::withTrashed()->with('pedido')->findOrFail($id);
        $entrega->restore();

        if ($entrega->pedido && $entrega->pedido->trashed()) {
            $entrega->pedido->restore();
        }

        return response()->json([
            'mensaje' => 'Entrega y Pedido restaurados correctamente',
            'entrega' => $entrega
        ]);
    }
}