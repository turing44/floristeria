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
        'direccion',
        'codigo_postal',
        'destinatario_telf', 
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class)->withTrashed();
    }
}