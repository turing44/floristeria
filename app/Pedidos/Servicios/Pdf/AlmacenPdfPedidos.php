<?php

namespace App\Pedidos\Servicios\Pdf;

use Illuminate\Support\Facades\Storage;

class AlmacenPdfPedidos
{
    public function obtenerSiExiste(string $ruta): ?string
    {
        $disk = Storage::disk($this->disk());

        if (!$disk->exists($ruta)) {
            return null;
        }

        return $disk->get($ruta);
    }

    public function guardar(string $ruta, string $contenido): string
    {
        Storage::disk($this->disk())->put($ruta, $contenido);

        return $contenido;
    }

    public function rutaEntrega(int $id): string
    {
        return $this->carpetaBase() . "/entregas/entrega_{$id}.pdf";
    }

    public function rutaReserva(int $id): string
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
