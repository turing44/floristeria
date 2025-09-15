<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    /** @use HasFactory<\Database\Factories\PedidoFactory> */
    use HasFactory;

    protected $table = "pedidos";

    protected $fillable = [
        "producto",
        "destinatario",
        "destinatario_telf",
        "cliente",
        "cliente_telf",
        "fecha_entrega",
        "observaciones",
        "horario",
        "mensaje",
    ];

}

