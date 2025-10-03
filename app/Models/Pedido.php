<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    /** @use HasFactory<\Database\Factories\PedidoFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'producto',
        'direccion',
        'destinatario',
        'destinatario_telf',
        'cliente',
        'cliente_telf',
        'fecha_entrega',
        'observaciones',
        'horario',
        'mensaje',
    ];

}
