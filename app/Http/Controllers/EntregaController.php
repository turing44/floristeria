<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Pedido;
use App\Http\Requests\StoreEntregaRequest;
use App\Http\Requests\UpdateEntregaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntregaController extends Controller
{

    public function index(Request $request)
    {
        $query = Entrega::with('pedido');

        if ($request->has('estado')) {
            $estado = $request->input('estado');
            $query->whereHas('pedido', function($q) use ($estado) {
                $q->where('estado', $estado);
            });
        }

        if ($request->has('ordenar')) {
            switch ($request->input('ordenar')) {
                case 'cp':
                    $query->orderBy('codigo_postal', 'asc');
                    break;
                case 'fecha_asc':
                    $query->join('pedidos', 'entregas.pedido_id', '=', 'pedidos.id')
                          ->orderBy('pedidos.fecha', 'asc')
                          ->select('entregas.*'); 
                    break;
                case 'fecha_desc':
                    $query->join('pedidos', 'entregas.pedido_id', '=', 'pedidos.id')
                          ->orderBy('pedidos.fecha', 'desc')
                          ->select('entregas.*');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        return response()->json([
            "num_entregas" => $query->count(),
            "entregas" => $query->get()
        ]);
    }


    public function store(StoreEntregaRequest $request)
    {
        $datos = $request->validated();

        try {
            return DB::transaction(function () use ($datos) {
                
                $pedido = Pedido::create([
                    'cliente_nombre' => $datos['cliente'],      
                    'cliente_telf'   => $datos['telf_cliente'],   
                    'precio'         => $datos['precio'],
                    'producto'       => $datos['producto'],      
                    'estado'         => $datos['estado'] ?? 'PENDIENTE',
                    'observaciones'  => $datos['observaciones'] ?? null,
                    'tipo_pedido'    => 'DOMICILIO',
                    
                    'fuente'         => $datos['fuente'] ?? null,
                    'fecha'          => $datos['fecha_entrega'], 
                    'horario'        => $datos['horario'] ?? 'INDIFERENTE',
                    'texto_mensaje'  => $datos['mensaje'] ?? null, 
                    'nombre_mensaje' => null, 
                    
                    'user_id'        => null, 
                    'guest_token_id' => null, 
                ]);

                $entrega = $pedido->entrega()->create([
                    'direccion'           => $datos['direccion'],
                    'codigo_postal'       => $datos['codigo_postal'],
                    'destinatario_nombre' => $datos['destinatario'],      
                    'destinatario_telf'   => $datos['telf_destinatario'], 
                ]);

                return response()->json($entrega->load('pedido'), 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'ERROR' => 'Error creando la entrega',
                'DETALLE' => $e->getMessage(),
            ], 500);
        }
    }


    public function show(Entrega $entrega)
    {
        return $entrega->load('pedido');
    }


    public function update(UpdateEntregaRequest $request, Entrega $entrega)
    {
        $datos = $request->validated();
        
        $datosEntrega = [];
        if (isset($datos['destinatario']))      $datosEntrega['destinatario_nombre'] = $datos['destinatario'];
        if (isset($datos['telf_destinatario'])) $datosEntrega['destinatario_telf'] = $datos['telf_destinatario'];
        if (isset($datos['direccion']))         $datosEntrega['direccion'] = $datos['direccion'];
        if (isset($datos['codigo_postal']))     $datosEntrega['codigo_postal'] = $datos['codigo_postal'];
        
        if (!empty($datosEntrega)) {
            $entrega->update($datosEntrega);
        }

        $datosPedido = [];
        if (isset($datos['cliente']))       $datosPedido['cliente_nombre'] = $datos['cliente'];
        if (isset($datos['telf_cliente']))  $datosPedido['cliente_telf'] = $datos['telf_cliente'];
        if (isset($datos['precio']))        $datosPedido['precio'] = $datos['precio'];
        if (isset($datos['producto']))      $datosPedido['producto'] = $datos['producto'];
        if (isset($datos['estado']))        $datosPedido['estado'] = $datos['estado'];
        if (isset($datos['observaciones'])) $datosPedido['observaciones'] = $datos['observaciones'];
        if (isset($datos['fuente']))        $datosPedido['fuente'] = $datos['fuente'];
        if (isset($datos['fecha_entrega'])) $datosPedido['fecha'] = $datos['fecha_entrega'];
        if (isset($datos['horario']))       $datosPedido['horario'] = $datos['horario'];
        if (isset($datos['mensaje']))       $datosPedido['texto_mensaje'] = $datos['mensaje'];

        if (!empty($datosPedido)) {
            $entrega->pedido->update($datosPedido);
        }

        return $entrega->load('pedido');
    }

    public function destroy(Entrega $entrega)
    {
        $entrega->pedido->delete(); 
        $entrega->delete(); 
        return response()->json(["message" => "Entrega archivada correctamente"], 204);
    }


    public function generarPdf(Entrega $entrega)
    {
        $entrega->load('pedido'); 

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
        return Entrega::onlyTrashed()->with('pedido')->get();
    }

    public function obtenerEntregaEliminada($id)
    {
        return Entrega::onlyTrashed()->with('pedido')->where("id", $id)->get();
    }
}