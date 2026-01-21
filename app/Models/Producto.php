<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producto extends Model
{
    protected $table = 'PRODUCTOS';

    protected $primaryKey = 'PRD_CODIGO';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'PRD_CODIGO',
        'CAT_CODIGO',
        'PRD_DESCRIPCION',
        'PRD_PRECIO',
        'PRD_COSTO_ADQUISICION',
    ];

    protected $casts = [
        'PRD_PRECIO' => 'decimal:2',
        'PRD_COSTO_ADQUISICION' => 'decimal:2',
    ];

    /**
     * Relación: Un producto pertenece a una categoría
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'CAT_CODIGO', 'CAT_CODIGO');
    }

    /**
     * Relación: Un producto puede estar en muchas bodegas (a través de PROXBOD)
     */
    public function bodegas(): BelongsToMany
    {
        return $this->belongsToMany(Bodega::class, 'PROXBOD', 'PRD_CODIGO', 'BOD_CODIGO')
            ->withPivot('DET_BOD_CANTIDAD', 'DET_BOD_UBICACION');
    }

    /**
     * Relación: Un producto puede estar en muchas compras (a través de PROXCMP)
     */
    public function compras(): BelongsToMany
    {
        return $this->belongsToMany(Compra::class, 'PROXCMP', 'PRD_CODIGO', 'CMP_CODIGO')
            ->withPivot('DET_CMP_CANTIDAD', 'DET_CMP_COSTO_UNITARIO', 'ESTADO_PROXCMP');
    }

    /**
     * Relación: Un producto puede estar en muchas facturas (a través de PROXFAC)
     */
    public function facturas(): BelongsToMany
    {
        return $this->belongsToMany(Factura::class, 'PROXFAC', 'PRD_CODIGO', 'FAC_CODIGO')
            ->withPivot('DET_FAC_CANTIDAD', 'DET_FAC_PRECIO_UNITARIO', 'ESTADO_PROXFAC');
    }

    /**
     * Relación: Un producto tiene muchos detalles de bodega
     */
    public function detallesBodega(): HasMany
    {
        return $this->hasMany(Proxbod::class, 'PRD_CODIGO', 'PRD_CODIGO');
    }

    /**
     * Relación: Un producto tiene muchos detalles de compra
     */
    public function detallesCompra(): HasMany
    {
        return $this->hasMany(Proxcmp::class, 'PRD_CODIGO', 'PRD_CODIGO');
    }

    /**
     * Relación: Un producto tiene muchos detalles de factura
     */
    public function detallesFactura(): HasMany
    {
        return $this->hasMany(Proxfac::class, 'PRD_CODIGO', 'PRD_CODIGO');
    }

    /**
     * Relación: Un producto tiene muchos detalles de carrito
     */
    public function detallesCarrito(): HasMany
    {
        return $this->hasMany(DetalleCarrito::class, 'PRD_CODIGO', 'PRD_CODIGO');
    }

    /**
     * Relación: Un producto puede tener muchos registros en kardex
     */
    public function kardexes(): HasMany
    {
        return $this->hasMany(Kardex::class, 'PRD_CODIGO', 'PRD_CODIGO');
    }
}
