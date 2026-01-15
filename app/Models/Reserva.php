<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "reservas";

    protected $fillable = [
        'pedido_id',
        'dinero_a_cuenta',
        'fecha_recogida',
        'nombre_mensaje', 
        'texto_mensaje',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}