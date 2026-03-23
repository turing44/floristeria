<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'guest_token_id',
        'tipo_pedido',
        'fuente',
        'nombre_cliente',
        'telefono_cliente',
        'producto',
        'precio',
        'fecha',
        'horario',
        'observaciones',
        'nombre_destinatario',
        'mensaje_tarjeta',
    ];

    protected $casts = [
        'fecha' => 'date',
        'precio' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guestToken()
    {
        return $this->belongsTo(GuestToken::class);
    }

    public function entrega()
    {
        return $this->hasOne(Entrega::class);
    }

    public function reserva()
    {
        return $this->hasOne(Reserva::class);
    }

    public function getTipoServicioAttribute()
    {
        return $this->entrega ?? $this->reserva;
    }
}
