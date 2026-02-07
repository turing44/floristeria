<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Pedido;
use App\Models\Entrega;
use App\Models\Reserva;
use App\Models\GuestToken;

class GoogleSheetService
{
    // =========================================================================
    // TUS COLUMNAS EXACTAS (NO TOCAR)
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

    
    private $urlAppsScript = "https://script.google.com/macros/s/AKfycbx1Nc0gh95S2Nx6TrbIDkWwhjEUymbx8nX-5G9qYhb0piGaz7gHubfpE2QtCJ-EAz-v/exec"; 
    // Pega aquí tu enlace CSV publicado (Archivo > Compartir > Publicar en la web > CSV)
    private $urlHojaCalculoCsv = "https://docs.google.com/spreadsheets/d/e/2PACX-1vS7lemc60mII-T9NcBnTOgq_F-BhdnMdZRhmWiJG08bajNObCO3MAT40TTyFB-Cf5k-R03izkB1FfRC/pub?gid=744467455&single=true&output=csv";


    public function generarNuevoLink()
    {
        $tokenUUID = (string) Str::uuid();

        // 1. Guardamos el token en nuestra DB
        GuestToken::create([
            'token'     => $tokenUUID,
            'active'    => true,
            'is_used'   => false,
            'fecha_exp' => now()->addMonths(3),
            'tipo'      => 'link_generado'
        ]);

        // 2. Enviamos a Google Apps Script
        $response = Http::post($this->urlAppsScript, ['token' => $tokenUUID]);

        // Devolvemos la URL lista para usar
        return $this->urlAppsScript . "?token=" . $tokenUUID;
    }

    public function importarPedidos()
    {
        $csvData = file_get_contents($this->urlHojaCalculoCsv);
        
        if (!$csvData) return 0;

        $lineas = array_map('str_getcsv', explode("\n", $csvData));
        $contadorImportados = 0;

        foreach ($lineas as $index => $columna) {
            if ($index == 0 || empty($columna[self::COL_TOKEN])) continue;

            $tokenString = trim($columna[self::COL_TOKEN]);

            $tokenEnDB = GuestToken::where('token', $tokenString)->first();

            // Si el token no existe o el pedido ya se creó, saltamos
            if (!$tokenEnDB || Pedido::where('guest_token_id', $tokenEnDB->id)->exists()) {
                continue; 
            }

            // TRANSACCIÓN PARA GUARDAR TODO
            DB::transaction(function () use ($columna, $tokenString, $tokenEnDB, &$contadorImportados) {
                
                // Actualizamos token (o lo creamos si venía de fuera)
                $tokenEnDB->update([
                    'active' => true, 
                    'is_used' => true, 
                    'tipo' => 'importado'
                ]);

                $esEnvio = (stripos($columna[self::COL_TIPO], 'Envío') !== false);
                $horario = $this->normalizarHorario($columna[self::COL_HORARIO] ?? '');
                $fecha   = $this->parsearFecha($columna[self::COL_FECHA] ?? '');

                // Creamos el Pedido Base
                $pedido = Pedido::create([
                    'user_id'        => null,
                    'guest_token_id' => $tokenEnDB->id, 
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
                        'estado_pago'     => 'PENDIENTE', 
                    ]);
                }

                $contadorImportados++;
            });
        }

        return $contadorImportados;
    }


    //HELPERS PARA NORMALIZAR DATOS (FECHA, HORARIO, ETC)
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
        
        if (stripos($textoLimpio, 'Ma') !== false) return 'MAÑANA';
        if (stripos($textoLimpio, 'Tar') !== false) return 'TARDE';
        
        return 'INDIFERENTE';
    }
}