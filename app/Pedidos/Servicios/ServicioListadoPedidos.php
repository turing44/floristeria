<?php

namespace App\Pedidos\Servicios;

use App\Models\Entrega;
use App\Models\Reserva;
use Illuminate\Database\Eloquent\Builder;

class ServicioListadoPedidos
{
    public function listarEntregas(array $filtros): array
    {
        return $this->listar($this->configuracionEntrega(), $filtros);
    }

    public function listarReservas(array $filtros): array
    {
        return $this->listar($this->configuracionReserva(), $filtros);
    }

    private function listar(array $configuracion, array $filtros): array
    {
        $filtros = $this->normalizarFiltros($filtros);
        $query = $this->crearQuery($configuracion, $filtros);

        $registros = $query->get();
        $resumen = ($configuracion['resumen'])();

        return [
            'registros' => $registros,
            'meta' => [
                'total' => $registros->count(),
                'archivadas' => $resumen['archivadas'],
                'resumen' => $resumen,
                'filtros' => $filtros,
            ],
        ];
    }

    private function crearQuery(array $configuracion, array $filtros): Builder
    {
        $modelo = $configuracion['modelo'];
        $tabla = $configuracion['tabla'];

        $query = $modelo::query()
            ->with('pedido')
            ->join('pedidos', "{$tabla}.pedido_id", '=', 'pedidos.id')
            ->select("{$tabla}.*");

        if ($filtros['archivados']) {
            $query->onlyTrashed();
        }

        if ($filtros['buscar'] !== '') {
            $this->aplicarBusqueda($query, $configuracion, $filtros['buscar']);
        }

        if ($filtros['fecha'] !== '') {
            $query->whereDate('pedidos.fecha', $filtros['fecha']);
        }

        ($configuracion['filtros'])($query, $filtros);

        $this->aplicarOrden($query, $configuracion['ordenes'], $filtros['ordenar']);

        return $query;
    }

    private function aplicarBusqueda(Builder $query, array $configuracion, string $buscar): void
    {
        $query->where(function ($subconsulta) use ($configuracion, $buscar) {
            $tieneCondiciones = false;

            if (ctype_digit($buscar)) {
                $subconsulta->where($configuracion['tabla'] . '.id', (int) $buscar);
                $tieneCondiciones = true;
            }

            foreach ($configuracion['columnas_busqueda'] as $columna) {
                $metodo = $tieneCondiciones ? 'orWhere' : 'where';
                $subconsulta->{$metodo}($columna, 'LIKE', '%' . $buscar . '%');
                $tieneCondiciones = true;
            }
        });
    }

    private function aplicarOrden(Builder $query, array $ordenes, string $orden): void
    {
        $resolver = $ordenes[$orden] ?? $ordenes['fecha_desc'];
        $resolver($query);
    }

    private function normalizarFiltros(array $filtros): array
    {
        return [
            'archivados' => filter_var($filtros['archivados'] ?? false, FILTER_VALIDATE_BOOL),
            'ordenar' => (string) ($filtros['ordenar'] ?? 'fecha_desc'),
            'buscar' => trim((string) ($filtros['buscar'] ?? '')),
            'fecha' => trim((string) ($filtros['fecha'] ?? '')),
            'horario' => trim((string) ($filtros['horario'] ?? '')),
            'pendientes_pago' => filter_var($filtros['pendientes_pago'] ?? false, FILTER_VALIDATE_BOOL),
        ];
    }

    private function configuracionEntrega(): array
    {
        return [
            'modelo' => Entrega::class,
            'tabla' => 'entregas',
            'columnas_busqueda' => [
                'pedidos.nombre_cliente',
                'pedidos.telefono_cliente',
                'pedidos.producto',
                'pedidos.nombre_destinatario',
                'entregas.telefono_destinatario',
                'entregas.direccion',
                'entregas.codigo_postal',
            ],
            'ordenes' => [
                'fecha_desc' => fn (Builder $query) => $query->orderBy('pedidos.fecha', 'desc'),
                'fecha_asc' => fn (Builder $query) => $query->orderBy('pedidos.fecha', 'asc'),
                'cp' => fn (Builder $query) => $query->orderBy('entregas.codigo_postal')->orderBy('pedidos.fecha'),
            ],
            'filtros' => function (Builder $query, array $filtros): void {
                if ($filtros['horario'] !== '') {
                    $query->where('pedidos.horario', $filtros['horario']);
                }
            },
            'resumen' => fn () => $this->resumenEntregas(),
        ];
    }

    private function configuracionReserva(): array
    {
        return [
            'modelo' => Reserva::class,
            'tabla' => 'reservas',
            'columnas_busqueda' => [
                'pedidos.nombre_cliente',
                'pedidos.telefono_cliente',
                'pedidos.producto',
            ],
            'ordenes' => [
                'fecha_desc' => fn (Builder $query) => $query->orderBy('pedidos.fecha', 'desc'),
                'fecha_asc' => fn (Builder $query) => $query->orderBy('pedidos.fecha', 'asc'),
            ],
            'filtros' => function (Builder $query, array $filtros): void {
                if ($filtros['pendientes_pago']) {
                    $query->where('reservas.dinero_pendiente', '>', 0);
                }
            },
            'resumen' => fn () => $this->resumenReservas(),
        ];
    }

    private function resumenEntregas(): array
    {
        return [
            'total' => Entrega::withTrashed()->count(),
            'activas' => Entrega::count(),
            'archivadas' => Entrega::onlyTrashed()->count(),
            'hoy' => Entrega::query()
                ->join('pedidos', 'entregas.pedido_id', '=', 'pedidos.id')
                ->whereNull('entregas.deleted_at')
                ->whereDate('pedidos.fecha', today())
                ->count(),
        ];
    }

    private function resumenReservas(): array
    {
        return [
            'total' => Reserva::withTrashed()->count(),
            'activas' => Reserva::count(),
            'archivadas' => Reserva::onlyTrashed()->count(),
            'hoy' => Reserva::query()
                ->join('pedidos', 'reservas.pedido_id', '=', 'pedidos.id')
                ->whereNull('reservas.deleted_at')
                ->whereDate('pedidos.fecha', today())
                ->count(),
            'pendientes_pago' => Reserva::query()
                ->whereNull('deleted_at')
                ->where('dinero_pendiente', '>', 0)
                ->count(),
        ];
    }
}
