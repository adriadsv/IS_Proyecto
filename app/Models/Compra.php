<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    protected $table = 'COMPRAS';

    protected $primaryKey = 'CMP_CODIGO';

    public $timestamps = false;

    protected $fillable = [
        'PRV_ID',
        'CMP_FECHA_ENTREGA',
        'CMP_ESTADO',
    ];

    protected $casts = [
        'CMP_CODIGO' => 'integer',
        'PRV_ID' => 'integer',
        'CMP_FECHA_ENTREGA' => 'date',
    ];

    /**
     * Relación: Una compra pertenece a un proveedor
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'PRV_ID', 'PRV_ID');
    }

    /**
     * Relación: Una compra tiene muchos detalles (PROXCMP)
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(Proxcmp::class, 'CMP_CODIGO', 'CMP_CODIGO');
    }

    /**
     * Relación: Una compra puede tener muchos registros en kardex
     */
    public function kardexes(): HasMany
    {
        return $this->hasMany(Kardex::class, 'CMP_CODIGO', 'CMP_CODIGO');
    }
}
