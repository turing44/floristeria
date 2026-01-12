<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Playwright\Playwright;
use App\Models\Entrega;

class PedidoController extends Controller
{
    // Método para listar pedidos (útil para verlos en JSON antes de imprimir)
    public function index()
    {
        return Pedido::with(['entrega', 'reserva', 'user'])->latest()->get();
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