<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MensajeController extends Controller
{
    public function generarPdf(Request $request)
    {
        $data = $request->validate([
            'texto_mensaje'  => 'required|string|max:5000',
            'nombre_mensaje' => 'required|string|max:255', 
        ]);

        $html = view('pdf.mensaje', compact('data'))->render();

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
                Log::error("Error PDF: $errors");
                return response()->json(['error' => $errors], 500);
            }

            return response($pdfBinary)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="tarjeta.pdf"');
        }

        return response()->json(['error' => 'Error Node.js'], 500);
    }
}