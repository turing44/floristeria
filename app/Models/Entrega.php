<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entrega extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "entregas";

    protected $fillable = [
        'pedido_id',
        'fuente',
        'direccion',
        'codigo_postal',
        'destinatario_nombre', 
        'destinatario_telf', 
        'fecha_entrega',
        'horario',
        'mensaje_dedicatoria',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}