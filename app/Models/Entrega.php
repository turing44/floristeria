<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entrega extends Model
{
    /** @use HasFactory<\Database\Factories\EntregaFactory> */
    use HasFactory, SoftDeletes;

    protected $table = "entregas";

    protected $fillable = [
        'fuente',
        'producto',
        'direccion',
        'codigo_postal',
        'destinatario',
        'telf_destinatario',
        'cliente',
        'telf_cliente',
        'fecha_entrega',
        'precio',
        'observaciones',
        'horario', 
        'mensaje',
        'estado', 
    ];

    
}
