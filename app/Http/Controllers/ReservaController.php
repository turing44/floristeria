<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Pedido;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservaController extends Controller
{

    public function index(Request $request)
    {
        $query = Reserva::with('pedido');

        if ($request->has('sort')) {
            switch ($request->input('sort')) {
                // El filtro 'cp' no tiene sentido en reservas (se recogen en tienda), lo quitamos.
                case 'fecha_asc':
                    $query->join('pedidos', 'reservas.pedido_id', '=', 'pedidos.id')
                          ->orderBy('pedidos.fecha', 'asc')
                          ->select('reservas.*'); 
                    break;
                case 'fecha_desc':
                    $query->join('pedidos', 'reservas.pedido_id', '=', 'pedidos.id')
                          ->orderBy('pedidos.fecha', 'desc')
                          ->select('reservas.*');
                    break;
                default:
                    $query->latest();
                    break;
            }

        } else {
            $query->latest();
        }

        return response()->json([
            "num_reservas" => $query->count(),
            "num_archivadas" => Reserva::onlyTrashed()->count(),
            "reservas" => $query->get()
        ]);
    }


    public function store(StoreReservaRequest $request)
    {
        $datos = $request->validated();

        try {
            return DB::transaction(function () use ($datos) {
                
                // 1. Crear PADRE (Pedido)
                $pedido = Pedido::create([
                    'cliente_nombre' => $datos['cliente_nombre'],      
                    'cliente_telf'   => $datos['cliente_telf'],   
                    'precio'         => $datos['precio'],
                    'producto'       => $datos['producto'],      
                    'observaciones'  => $datos['observaciones'] ?? null,
                    'tipo_pedido'    => 'TIENDA', 
                    
                    'fuente'         => $datos['fuente'] ?? 'Tienda',
                    'fecha'          => $datos['fecha'], 
                    'horario'        => $datos['horario'] ?? 'INDIFERENTE',
                    'texto_mensaje'  => $datos['texto_mensaje'] ?? null, 
                    'nombre_mensaje' => $datos['nombre_mensaje'] ?? null, 
                    
                    'user_id'        => null, 
                    'guest_token_id' => null, 
                ]);

                $reserva = $pedido->reserva()->create([
                    'dinero_dejado_a_cuenta' => $datos['dinero_dejado_a_cuenta'] ?? 0,
                    'estado_pago'            => $datos['estado_pago'] ?? 'PENDIENTE',
                ]);

                return response()->json($reserva->load('pedido'), 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'ERROR' => 'Error creando la reserva',
                'DETALLE' => $e->getMessage(),
            ], 500);
        }
    }


    public function show(Reserva $reserva)
    {
        return $reserva->load('pedido');
    }


    public function update(UpdateReservaRequest $request, Reserva $reserva)
    {
        $datos = $request->validated();

        $datosReserva = [];
        if (isset($datos['dinero_dejado_a_cuenta'])) $datosReserva['dinero_dejado_a_cuenta'] = $datos['dinero_dejado_a_cuenta'];
        if (isset($datos['estado_pago']))            $datosReserva['estado_pago'] = $datos['estado_pago'];

        if (!empty($datosReserva)) {
            $reserva->update($datosReserva);
        }

        $datosPedido = [];
        if (isset($datos['cliente_nombre'])) $datosPedido['cliente_nombre'] = $datos['cliente_nombre'];
        if (isset($datos['cliente_telf']))   $datosPedido['cliente_telf']   = $datos['cliente_telf'];
        if (isset($datos['precio']))         $datosPedido['precio']         = $datos['precio'];
        if (isset($datos['producto']))       $datosPedido['producto']       = $datos['producto'];
        if (isset($datos['observaciones']))  $datosPedido['observaciones']  = $datos['observaciones'];
        if (isset($datos['fuente']))         $datosPedido['fuente']         = $datos['fuente'];
        if (isset($datos['fecha']))          $datosPedido['fecha']          = $datos['fecha'];
        if (isset($datos['horario']))        $datosPedido['horario']        = $datos['horario'];
        if (isset($datos['texto_mensaje']))  $datosPedido['texto_mensaje']  = $datos['texto_mensaje'];
        if (isset($datos['nombre_mensaje'])) $datosPedido['nombre_mensaje'] = $datos['nombre_mensaje'];

        if (!empty($datosPedido)) {
            $reserva->pedido->update($datosPedido);
        }

        return $reserva->load('pedido');
    }

    public function destroy(Reserva $reserva)
    {
        $reserva->pedido->delete(); 
        $reserva->delete(); 
        return response()->json(["message" => "Reserva archivada correctamente"], 204);
    }


    public function generarPdf(Reserva $reserva)
    {
        $reserva->load('pedido'); 

        $html = view('pdf.albaran', ['entrega' => $reserva])->render(); 

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
            ->with(['pedido' => fn ($pedido) => $pedido->withTrashed()])
            ->orderBy('deleted_at', 'desc') 
            ->get();
        return response()->json([
            "num_archivadas" => $archivadas->count(),
            "entregas"       => $archivadas
        ]);
    }

    public function obtenerReservaEliminada($id)
    {
        return Reserva::onlyTrashed()
            ->with(['pedido' => fn ($pedido) => $pedido->withTrashed()])
            ->where('id', $id)
            ->firstOrFail();
    }

}