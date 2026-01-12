<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestToken extends Model
{
    protected $fillable = [
        'token', 
        'tipo', 
        'is_used', 
        'fecha_exp'
    ];

    protected $casts = [
        'fecha_exp' => 'datetime', 
        'is_used' => 'boolean',
    ];
    
    public function isValid() {
        
        return !$this->is_used && $this->fecha_exp->isFuture();
    }
}