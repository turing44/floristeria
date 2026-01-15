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
        // 1. Cargamos siempre la relación con el padre (Pedido)
        $query = Entrega::with('pedido');

        // 2. FILTRO DE ESTADO (PENDIENTE, PAGADO...)
        if ($request->has('estado')) {
            $estado = $request->input('estado');
            // Buscamos dentro de la relación 'pedido'
            $query->whereHas('pedido', function($q) use ($estado) {
                $q->where('estado', $estado);
            });
        }

        // 3. ORDENACIÓN
        if ($request->has('ordenar')) {
            switch ($request->input('ordenar')) {
                case 'cp':
                    $query->orderBy('codigo_postal', 'asc');
                    break;
                case 'fecha_asc':
                    $query->orderBy('fecha_entrega', 'asc');
                    break;
                case 'fecha_desc':
                    $query->orderBy('fecha_entrega', 'desc');
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
                
                // A. Crear el PADRE (Facturación)
                $pedido = Pedido::create([
                    'cliente_nombre' => $datos['cliente'],      
                    'cliente_telf'   => $datos['telf_cliente'],  
                    'precio'         => $datos['precio'],
                    'producto'    => $datos['producto'],      
                    'estado'         => $datos['estado'] ?? 'PENDIENTE',
                    'observaciones'  => $datos['observaciones'] ?? null,
                    'tipo_pedido'    => 'DOMICILIO',
                    'user_id'        => null, 
                    'guest_token_id' => null, 
                ]);

                // B. Crear la HIJA 
                $entrega = $pedido->entrega()->create([
                    'fuente'              => $datos['fuente'] ?? null,
                    'direccion'           => $datos['direccion'],
                    'codigo_postal'       => $datos['codigo_postal'],
                    
                    // --- MAPEO DE NOMBRES (JSON -> BD) ---
                    'destinatario_nombre' => $datos['destinatario'],      
                    'destinatario_telf'   => $datos['telf_destinatario'], 
                    'mensaje_dedicatoria' => $datos['mensaje'] ?? null,   
                    // -------------------------------------
                    
                    'fecha_entrega'       => $datos['fecha_entrega'],
                    'horario'             => $datos['horario'] ?? 'INDIFERENTE',
                ]);

                return response()->json($entrega->load('pedido'), 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'ERROR' => 'Error creando la entrega',
                'DETALLE' => $e->getMessage(),
                'ARCHIVO' => $e->getFile(),
                'LINEA' => $e->getLine()
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
        
        // Preparamos el array con los nombres de la BD
        $datosBD = [];

        // Mapeo manual de campos conflictivos (si vienen en la petición)
        if (isset($datos['destinatario']))      $datosBD['destinatario_nombre'] = $datos['destinatario'];
        if (isset($datos['telf_destinatario'])) $datosBD['destinatario_telf'] = $datos['telf_destinatario'];
        if (isset($datos['mensaje']))           $datosBD['mensaje_dedicatoria'] = $datos['mensaje'];
        
        // Campos directos (que se llaman igual)
        if (isset($datos['direccion']))     $datosBD['direccion'] = $datos['direccion'];
        if (isset($datos['codigo_postal'])) $datosBD['codigo_postal'] = $datos['codigo_postal'];
        if (isset($datos['fecha_entrega'])) $datosBD['fecha_entrega'] = $datos['fecha_entrega'];
        if (isset($datos['horario']))       $datosBD['horario'] = $datos['horario'];
        if (isset($datos['fuente']))        $datosBD['fuente'] = $datos['fuente'];

        // Actualizamos la entrega
        if (!empty($datosBD)) {
            $entrega->update($datosBD);
        }

        // Si también quieres actualizar datos del padre (precio/cliente), hazlo aquí:
        // if (isset($datos['precio'])) $entrega->pedido->update(['precio' => $datos['precio']]);

        return $entrega->load('pedido');
    }

    public function destroy(Entrega $entrega)
    {
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

        // Llamada al script de Node
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

    // --- MÉTODOS DE PAPELERA (SOFT DELETES) ---

    public function obtenerEliminadas()
    {
        return Entrega::onlyTrashed()->with('pedido')->get();
    }

    public function obtenerEntregaEliminada($id)
    {
        return Entrega::onlyTrashed()->with('pedido')->where("id", $id)->get();
    }
}