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
        'producto',    
        'precio',
        'fecha',
        'cliente_nombre',
        'cliente_telf',
        'horario',
        'observaciones',
        'nombre_mensaje',
        'texto_mensaje'
    ];

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