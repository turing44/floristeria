<?php

namespace App\Pedidos\Contratos;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;

class ServicioContratoPedidos
{
    public function __construct(
        private DefinicionesCamposPedido $definiciones
    ) {
    }

    public function obtenerContratoFormulario(string $entidad, string $operacion = 'crear'): array
    {
        $campos = $this->obtenerCampos($entidad);
        $secciones = [];

        foreach ($campos as $campo) {
            if ($campo['entrada'] === 'hidden') {
                continue;
            }

            $idSeccion = $campo['seccion']['id'];

            if (!isset($secciones[$idSeccion])) {
                $secciones[$idSeccion] = [
                    'id' => $idSeccion,
                    'titulo' => $campo['seccion']['titulo'],
                    'campos' => [],
                ];
            }

            $secciones[$idSeccion]['campos'][] = $this->mapearCampoFormulario($campo, $operacion);
        }

        return [
            'entidad' => $entidad,
            'version' => 1,
            'operacion' => $operacion,
            'secciones' => array_values($secciones),
            'campos_ocultos' => $this->obtenerCamposOcultos($campos, $operacion),
            'envio' => $this->obtenerDestinoEnvio($entidad, $operacion),
        ];
    }

    public function obtenerReglas(string $entidad, string $operacion): array
    {
        $reglas = [];

        foreach ($this->obtenerCampos($entidad) as $campo) {
            $reglas[$campo['clave']] = $campo['reglas'][$operacion] ?? 'nullable';
        }

        return $reglas;
    }

    public function obtenerMensajes(string $entidad): array
    {
        $mensajes = [];

        foreach ($this->obtenerCampos($entidad) as $campo) {
            $mensajes = array_merge($mensajes, $campo['mensajes'] ?? []);
        }

        return $mensajes;
    }

    public function obtenerCampos(string $entidad): array
    {
        return $this->definiciones->obtener($entidad);
    }

    public function normalizarEntrada(string $entidad, array $datos): array
    {
        foreach ($this->obtenerCampos($entidad) as $campo) {
            $clave = $campo['clave'];
            $normalizar = $campo['restricciones']['normalizar'] ?? null;

            if (!isset($datos[$clave]) || !is_array($normalizar)) {
                continue;
            }

            $datos[$clave] = $normalizar[$datos[$clave]] ?? $datos[$clave];
        }

        return $datos;
    }

    public function aplicarValidacionesAdicionales(
        string $entidad,
        Validator $validator,
        array $datos
    ): void {
        $validator->after(function ($validator) use ($entidad, $datos) {
            foreach ($this->obtenerCampos($entidad) as $campo) {
                $restricciones = $campo['restricciones'] ?? [];

                if (!isset($restricciones['maximoCampo'])) {
                    continue;
                }

                $clave = $campo['clave'];
                $campoLimite = $restricciones['maximoCampo'];
                $valor = $datos[$clave] ?? null;
                $limite = $datos[$campoLimite] ?? null;

                if ($valor === null || $valor === '' || $limite === null || $limite === '') {
                    continue;
                }

                if ((float) $valor > (float) $limite) {
                    $validator->errors()->add(
                        $clave,
                        $restricciones['mensajeMaximoCampo']
                            ?? 'El valor no puede ser mayor que ' . $campoLimite . '.'
                    );
                }
            }
        });
    }

    public function separarDatos(string $entidad, array $datos): array
    {
        $separados = [
            'pedido' => [],
            $entidad => [],
        ];

        foreach ($this->obtenerCampos($entidad) as $campo) {
            $clave = $campo['clave'];

            if (!array_key_exists($clave, $datos)) {
                continue;
            }

            $separados[$campo['modelo']][$campo['columna']] = $datos[$clave];
        }

        return $separados;
    }

    public function serializarModelo(string $entidad, Model $registro): array
    {
        $pedido = $registro->pedido;
        $origenes = [
            'pedido' => $pedido,
            'entrega' => $entidad === 'entrega' ? $registro : null,
            'reserva' => $entidad === 'reserva' ? $registro : null,
        ];

        $datos = [
            'id' => $registro->id,
            'pedido_id' => $registro->pedido_id,
            'esta_archivado' => $registro->trashed(),
            'eliminado_en' => $registro->deleted_at,
        ];

        foreach ($this->obtenerCampos($entidad) as $campo) {
            $origen = $origenes[$campo['modelo']] ?? null;
            $valor = $origen?->{$campo['columna']} ?? null;
            $datos[$campo['clave']] = $valor ?? $campo['valor_inicial'];
        }

        return $datos;
    }

    private function mapearCampoFormulario(array $campo, string $operacion): array
    {
        return [
            'clave' => $campo['clave'],
            'etiqueta' => $campo['etiqueta'],
            'entrada' => $campo['entrada'],
            'valor_inicial' => $campo['valor_inicial'],
            'requerido' => $campo['requerido'][$operacion] ?? false,
            'restricciones' => $campo['restricciones'],
        ];
    }

    private function obtenerCamposOcultos(array $campos, string $operacion): array
    {
        $ocultos = [];

        foreach ($campos as $campo) {
            if ($campo['entrada'] !== 'hidden') {
                continue;
            }

            $ocultos[] = $this->mapearCampoFormulario($campo, $operacion);
        }

        return $ocultos;
    }

    private function obtenerDestinoEnvio(string $entidad, string $operacion): array
    {
        $base = $entidad === 'entrega' ? '/api/entregas' : '/api/reservas';

        return [
            'metodo' => $operacion === 'actualizar' ? 'PUT' : 'POST',
            'ruta' => $operacion === 'actualizar' ? "{$base}/{id}" : $base,
        ];
    }
}
