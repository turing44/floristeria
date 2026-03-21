<?php

namespace App\Http\Controllers;

use App\Pedidos\Servicios\ServicioPdfPedidos;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function __construct(
        private ServicioPdfPedidos $servicioPdfs
    ) {
    }

    public function generarPdf(Request $request)
    {
        $datos = $request->validate([
            'texto_mensaje'  => 'required|string|max:5000',
            'nombre_mensaje' => 'required|string|max:255',
        ]);

        try {
            $pdf = $this->servicioPdfs->generarMensaje($datos);

            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="tarjeta.pdf"');
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
