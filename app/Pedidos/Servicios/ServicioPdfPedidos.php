<?php

namespace App\Pedidos\Servicios;

use App\Models\Entrega;
use App\Models\Reserva;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ServicioPdfPedidos
{
    public function obtenerEntrega(Entrega $entrega): string
    {
        return $this->obtenerOGuardar(
            $this->rutaEntrega($entrega->id),
            fn () => $this->generarEntrega($entrega)
        );
    }

    public function obtenerReserva(Reserva $reserva): string
    {
        return $this->obtenerOGuardar(
            $this->rutaReserva($reserva->id),
            fn () => $this->generarReserva($reserva)
        );
    }

    public function guardarEntregaSinRomper(Entrega $entrega): void
    {
        $this->guardarSinRomper(
            fn () => $this->guardar(
                $this->rutaEntrega($entrega->id),
                fn () => $this->generarEntrega($entrega)
            ),
            'No se pudo guardar el PDF de la entrega.'
        );
    }

    public function guardarReservaSinRomper(Reserva $reserva): void
    {
        $this->guardarSinRomper(
            fn () => $this->guardar(
                $this->rutaReserva($reserva->id),
                fn () => $this->generarReserva($reserva)
            ),
            'No se pudo guardar el PDF de la reserva.'
        );
    }

    public function generarMensaje(array $datos): string
    {
        $html = view('pdf.mensaje', compact('datos'))->render();

        return $this->generarDesdeHtml($html);
    }

    private function obtenerOGuardar(string $ruta, callable $generador): string
    {
        $disk = Storage::disk($this->disk());

        if ($disk->exists($ruta)) {
            return $disk->get($ruta);
        }

        return $this->guardar($ruta, $generador);
    }

    private function guardar(string $ruta, callable $generador): string
    {
        $contenido = $generador();
        Storage::disk($this->disk())->put($ruta, $contenido);

        return $contenido;
    }

    private function guardarSinRomper(callable $accion, string $mensaje): void
    {
        try {
            $accion();
        } catch (\Throwable $e) {
            Log::error($mensaje, ['error' => $e->getMessage()]);
        }
    }

    private function generarEntrega(Entrega $entrega): string
    {
        $html = view('pdf.albaran', compact('entrega'))->render();

        return $this->generarDesdeHtml($html);
    }

    private function generarReserva(Reserva $reserva): string
    {
        $html = view('pdf.reserva', compact('reserva'))->render();

        return $this->generarDesdeHtml($html);
    }

    private function generarDesdeHtml(string $html): string
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

    private function rutaEntrega(int $id): string
    {
        return $this->carpetaBase() . "/entregas/entrega_{$id}.pdf";
    }

    private function rutaReserva(int $id): string
    {
        return $this->carpetaBase() . "/reservas/reserva_{$id}.pdf";
    }

    private function carpetaBase(): string
    {
        return trim(config('floristeria.pdfs.carpeta', 'pdfs/pedidos'), '/');
    }

    private function disk(): string
    {
        return config('floristeria.pdfs.disk', 'local');
    }
}
