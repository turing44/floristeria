<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token_id',
        'tipo_pedido',      
        'descripcion',
        'precio',
        'estado',
        'observaciones',
        'cliente_nombre',
        'cliente_telf',
    ];

    // --- RELACIONES ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guestToken()
    {
        return $this->belongsTo(GuestToken::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    // --- HERENCIA / ESPECIALIZACIÓN ---

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