<?php

namespace App\Pedidos\Servicios\Pdf;

class EjecutorPlaywrightPdf
{
    public function generar(string $html): string
    {
        $descriptores = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $proceso = proc_open('node "' . base_path('resources/js/generar_pdf.cjs') . '"', $descriptores, $pipes);

        if (!is_resource($proceso)) {
            throw new \RuntimeException('No se pudo iniciar Node.js para generar el PDF.');
        }

        fwrite($pipes[0], $html);
        fclose($pipes[0]);

        $pdf = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $errores = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $estado = proc_close($proceso);

        if ($estado !== 0) {
            throw new \RuntimeException(trim($errores) ?: 'Error desconocido generando el PDF.');
        }

        return $pdf;
    }
}
