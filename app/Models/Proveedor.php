<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'PROVEEDORES';

    protected $primaryKey = 'PRV_ID';

    public $timestamps = false;

    protected $fillable = [
        'PRV_RUC',
        'PRV_RAZON_SOCIAL',
        'PRV_CORREO',
        'PRV_DIRECCION',
        'PRV_TELEFONO',
    ];

    protected $casts = [
        'PRV_ID' => 'integer',
    ];

    /**
     * Relación: Un proveedor puede tener muchas compras
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'PRV_ID', 'PRV_ID');
    }
}
