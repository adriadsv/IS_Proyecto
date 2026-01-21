<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'CLIENTES';

    protected $primaryKey = 'CLI_ID';

    public $timestamps = false;

    protected $fillable = [
        'CLI_CEDULA_RUC',
        'CLI_NOMBRE',
        'CLI_TELEFONO',
        'CLI_CORREO',
    ];

    protected $casts = [
        'CLI_ID' => 'integer',
    ];

    /**
     * Relación: Un cliente puede tener muchas facturas
     */
    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class, 'CLI_ID', 'CLI_ID');
    }

    /**
     * Relación: Un cliente puede tener muchos usuarios
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'CLI_ID', 'CLI_ID');
    }
}
