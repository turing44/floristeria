<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Entrega; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;
class PedidoController extends Controller
{
    
    public function index(Request $request)
    {
        
        $query = Pedido::with(['entrega', 'reserva', 'user']);

        // --- FILTRO: PENDIENTES ---
        if ($request->has('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        // --- ORDENACIÓN:POR FECHA O CÓDIGO POSTAL ---
        $orden = $request->input('ordenar', 'fecha_desc'); 

        switch ($orden) {
            case 'cp': 
                $query->select('pedidos.*') 
                      ->join('entregas', 'pedidos.id', '=', 'entregas.pedido_id')
                      ->orderBy('entregas.codigo_postal', 'asc');
                break;

            case 'fecha_asc':
                $query->orderBy('created_at', 'asc');
                break;

            case 'fecha_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->get();
    }


    public function store(Request $request)
    {
        
        $datos = $request->validate([
            // Datos que irán a Pedido
            'cliente'           => 'required|string',
            'telf_cliente'      => 'required|string',
            'precio'            => 'required|numeric',
            'producto'          => 'required|string',
            'estado'            => 'nullable|string',
            'observaciones'     => 'nullable|string',

            // Datos que irán a Entrega
            'fuente'            => 'nullable|string',
            'direccion'         => 'required|string', 
            'codigo_postal'     => 'required|string',
            'destinatario'      => 'required|string', 
            'telf_destinatario' => 'required|string', 
            'fecha_entrega'     => 'nullable|date',
            'horario'           => 'nullable|in:MAÑANA,TARDE,INDIFERENTE',
            'mensaje'           => 'nullable|string',
        ]);

        //TRANSACCIÓN 
        return DB::transaction(function () use ($datos) {
            
            // A. Crear el Pedido
            $pedido = Pedido::create([
                'cliente_nombre' => $datos['cliente'],
                'cliente_telf'   => $datos['telf_cliente'],
                'precio'         => $datos['precio'],
                'descripcion'    => $datos['producto'], 
                'estado'         => $datos['estado'] ?? 'PENDIENTE',
                'observaciones'  => $datos['observaciones'] ?? null,
                'tipo_pedido'    => 'DOMICILIO', 
            ]);

            // B. Crear la Entrega a través de la relación
            $pedido->entrega()->create([
                'fuente'            => $datos['fuente'] ?? null,
                'direccion'         => $datos['direccion'],
                'codigo_postal'     => $datos['codigo_postal'],
                'destinatario'      => $datos['destinatario'],       
                'telf_destinatario' => $datos['telf_destinatario'] ?? '', 
                'fecha_entrega'     => $datos['fecha_entrega'] ?? now(),
                'horario'           => $datos['horario'] ?? 'INDIFERENTE',
                'mensaje'           => $datos['mensaje'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado e integrado correctamente.',
                'data'    => $pedido->load('entrega')
            ], 201);
        });
    }


    public function generarpdf($pedido_id)
    {
        // Buscar el pedido con su relación entrega
        $pedido = Pedido::with(['entrega'])->findOrFail($pedido_id);

        //Generar HTML desde la vista Blade
        $html = view('pdf.albaran', compact('pedido'))->render();

        //Configurar tuberías para comunicarse con Node.js
        $descriptorspec = [
            0 => ["pipe", "r"], // stdin: enviamos HTML a Node
            1 => ["pipe", "w"], // stdout: recibimos PDF desde Node
            2 => ["pipe", "w"]  // stderr: recibimos errores desde Node
        ];

        //Ejecutar el script Node.js que genera el PDF
        $process = proc_open('node "' . base_path('resources/js/generar_pdf.cjs') . '"', $descriptorspec, $pipes);

        if (is_resource($process)) {
            //Enviar HTML a Node
            fwrite($pipes[0], $html);
            fclose($pipes[0]); // cerramos stdin

            //Leer PDF generado desde stdout
            $pdfBinary = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            // Leer errores desde stderr
            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            // Cerrar proceso Node y obtener código de salida
            $status = proc_close($process);

            // Si hay error, devolver mensaje
            if ($status !== 0) {
                Log::error("Error generando PDF: $errors");
                return response("Error generando PDF: $errors", 500);
            }

            //Devolver PDF directamente al navegador
            return response($pdfBinary)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="entrega_'.$pedido_id.'.pdf"');
        }

        // Error si Node no pudo ejecutarse
        return response('Error iniciando Node.js', 500);
    }
}