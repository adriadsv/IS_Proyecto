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

    /**
     * Relación: Una compra tiene muchos detalles (PROXCMP)
     */
    public function detallesCompra(): HasMany
    {
        return $this->hasMany(Proxcmp::class, 'CMP_CODIGO', 'CMP_CODIGO');
    }

    // Accessors for compatibility
    public function getFechaCompraAttribute()
    {
        return $this->CMP_FECHA_ENTREGA;
    }

    public function getTipoComprobanteAttribute()
    {
        return 'Factura'; // Default value as column doesn't exist
    }

    public function getNumeroComprobanteAttribute()
    {
        return str_pad((string) $this->CMP_CODIGO, 6, '0', STR_PAD_LEFT);
    }

    public function getTotalAttribute()
    {
        // Calculate total from details
        return $this->detallesCompra->sum(function ($detalle) {
            return $detalle->DET_CMP_CANTIDAD * $detalle->DET_CMP_COSTO_UNITARIO;
        });
    }

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

    // Accessors for compatibility
    public function getIdAttribute()
    {
        return $this->CMP_CODIGO;
    }

    public function getFechaAttribute()
    {
        return $this->CMP_FECHA_ENTREGA;
    }

    public function getEstadoAttribute()
    {
        return $this->CMP_ESTADO;
    }
}
