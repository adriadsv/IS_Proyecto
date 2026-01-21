<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bodega extends Model
{
    protected $table = 'BODEGAS';

    protected $primaryKey = 'BOD_CODIGO';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'BOD_CODIGO',
        'BOD_DESCRIPCION',
        'BOD_DIRECCION',
        'BOD_NOMBRE_ENCARGADO',
        'BOD_TELEFONO_ENCARGADO',
    ];

    /**
     * Relación: Una bodega tiene muchos productos (a través de PROXBOD)
     */
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'PROXBOD', 'BOD_CODIGO', 'PRD_CODIGO')
            ->withPivot('DET_BOD_CANTIDAD', 'DET_BOD_UBICACION');
    }

    /**
     * Relación: Una bodega tiene muchos detalles de productos
     */
    public function detallesProductos(): HasMany
    {
        return $this->hasMany(Proxbod::class, 'BOD_CODIGO', 'BOD_CODIGO');
    }

    /**
     * Relación: Una bodega puede tener muchos registros en kardex
     */
    public function kardexes(): HasMany
    {
        return $this->hasMany(Kardex::class, 'BOD_CODIGO', 'BOD_CODIGO');
    }
}
