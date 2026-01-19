<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'identificacion',
        'nombres',
        'apellidos',
        'direccion',
        'telefono',
        'correo',
        'estado',
    ];
}
