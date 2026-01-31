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
        'dinero_pendiente',
        'hora_recogida'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class)->withTrashed();
    }
}