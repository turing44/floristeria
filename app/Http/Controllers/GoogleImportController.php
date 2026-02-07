<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleSheetService;

class GoogleImportController extends Controller
{

    public function generarLink(GoogleSheetService $googleService)
    {
        try {
            $url = $googleService->generarNuevoLink();

            return response()->json([
                'status'  => 'success',
                'mensaje' => 'Enlace generado correctamente',
                'link'    => $url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'mensaje' => 'Error al conectar: ' . $e->getMessage()
            ], 500);
        }
    }
}
