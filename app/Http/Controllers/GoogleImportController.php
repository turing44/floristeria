<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Pedido;
use App\Models\Entrega;
use App\Models\Reserva;
use App\Models\GuestToken;

class GoogleImportController extends Controller
{
    // =========================================================================
    // MAPA DE COLUMNAS DEL EXCEL (Índices comenzando en 0)
    // =========================================================================
    const COL_TOKEN         = 1;  // Columna B
    const COL_NOMBRE        = 2;  // Columna C
    const COL_TLF_CLIENTE   = 3;  // Columna D
    const COL_TIPO          = 4;  // Columna E (Envío / Recogida)
    const COL_TLF_DESTINO   = 5;  // Columna F
    const COL_DIRECCION     = 6;  // Columna G
    const COL_CP            = 7;  // Columna H
    const COL_PRODUCTO      = 8;  // Columna I
    const COL_FECHA         = 9;  // Columna J
    const COL_HORARIO       = 10; // Columna K
    const COL_TARJ_NOMBRE   = 11; // Columna L
    const COL_TARJ_MENSAJE  = 12; // Columna M
    const COL_OBSERVACIONES = 13; // Columna N

    // CONFIGURACIÓN DE URLS
    private $urlAppsScript = "https://script.google.com/macros/s/AKfycbxjrZgpAP6OYQMoeIAG2V1eEEvAQVv1pMo29jWhuKz14kNbQhiXDdFu7FMqgQNzTFr33w/exec"; 
    // Pega aquí tu enlace CSV publicado (Archivo > Compartir > Publicar en la web > CSV)
    private $urlHojaCalculoCsv = "https://docs.google.com/spreadsheets/d/e/2PACX-1vTcWlE55K4GT4zoO3S1x18DVNPMaL0hg05khWyJPywt12v5Hebiw8dZVr16cNA-viNz6L4M5ydoaiIY/pub?gid=1554317534&single=true&output=csv";

    public function generarLink()
    {
        $tokenUUID = (string) Str::uuid();

        // 1. Guardamos el token en nuestra DB inmediatamente (estado inactivo)
        GuestToken::create([
            'token'     => $tokenUUID,
            'active'    => true, // Lo activamos para que pueda usarse
            'is_used'   => false,
            'fecha_exp' => now()->addMonths(3), // Validez de 3 meses
            'tipo'      => 'link_generado'
        ]);

        try {
            Http::post($this->urlAppsScript, ['token' => $tokenUUID]);

            return response()->json([
                'status' => 'success',
                'mensaje' => '✅ Enlace generado',
                'link' => $this->urlAppsScript . "?token=" . $tokenUUID
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'mensaje' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function importarPedidos()
    {
        try {
            $csvData = file_get_contents($this->urlHojaCalculoCsv);
            $lineas = array_map('str_getcsv', explode("\n", $csvData));
            
            $contadorImportados = 0;

            foreach ($lineas as $index => $columna) {
                if ($index == 0 || empty($columna[self::COL_TOKEN])) continue;

                $tokenString = trim($columna[self::COL_TOKEN]);

                $tokenEnDB = GuestToken::where('token', $tokenString)->first();

                if ($tokenEnDB && Pedido::where('guest_token_id', $tokenEnDB->id)->exists()) {
                    continue; 
                }
                DB::transaction(function () use ($columna, $tokenString, &$contadorImportados) {
                    
                    $tokenRow = GuestToken::firstOrCreate(
                        ['token' => $tokenString],
                        ['active' => true, 'is_used' => true, 'fecha_exp' => now()->addYear(), 'tipo' => 'importado']
                    );
                    $esEnvio = (stripos($columna[self::COL_TIPO], 'Envío') !== false);
                    $horario = $this->normalizarHorario($columna[self::COL_HORARIO] ?? '');
                    $fecha   = $this->parsearFecha($columna[self::COL_FECHA] ?? '');

                    // C. Crear Pedido Padre
                    $pedido = Pedido::create([
                        'user_id'        => null,
                        'guest_token_id' => $tokenRow->id, 
                        'tipo_pedido'    => $esEnvio ? 'DOMICILIO' : 'TIENDA', 
                        'fuente'         => 'Google Forms',
                        'cliente_nombre' => $columna[self::COL_NOMBRE],
                        'cliente_telf'   => $columna[self::COL_TLF_CLIENTE],
                        'producto'       => $columna[self::COL_PRODUCTO] ?? 'Sin especificar',
                        'fecha'          => $fecha,
                        'horario'        => $horario, 
                        'nombre_mensaje' => $columna[self::COL_TARJ_NOMBRE] ?? null,
                        'texto_mensaje'  => $columna[self::COL_TARJ_MENSAJE] ?? null,
                        'observaciones'  => $columna[self::COL_OBSERVACIONES] ?? null,
                        'precio'         => 0, 
                    ]);

                    if ($esEnvio) {
                        Entrega::create([
                            'pedido_id'         => $pedido->id,
                            'destinatario_telf' => !empty($columna[self::COL_TLF_DESTINO]) ? $columna[self::COL_TLF_DESTINO] : $columna[self::COL_TLF_CLIENTE],
                            'direccion'         => $columna[self::COL_DIRECCION] ?? 'Dirección pendiente',
                            'codigo_postal'     => $columna[self::COL_CP] ?? '',
                        ]);
                    } else {
                        Reserva::create([
                            'pedido_id'       => $pedido->id,
                            'dinero_a_cuenta' => 0,
                            'estado_pago'     => 'PENDIENTE', // MAYÚSCULAS OBLIGATORIAS
                        ]);
                    }

                    // E. Marcar token como usado
                    $tokenRow->update(['is_used' => true]);
                    
                    $contadorImportados++;
                });
            }

            return response()->json([
                'status' => 'success', 
                'mensaje' => "✅ Se han importado $contadorImportados pedidos nuevos correctamente."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'mensaje' => '❌ Error crítico: ' . $e->getMessage()
            ], 500);
        }
    }

    private function parsearFecha($fechaString)
    {
        try {
            return Carbon::createFromFormat('d/m/Y', $fechaString)->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d'); 
        }
    }

    private function normalizarHorario($texto)
    {
        $textoLimpio = mb_convert_encoding($texto, 'UTF-8', 'auto');
        
        if (stripos($textoLimpio, 'Ma') !== false) {
            return 'MAÑANA'; 
        }
        
        if (stripos($textoLimpio, 'Tar') !== false) {
            return 'TARDE';
        }

        
        return 'INDIFERENTE';
    }
}