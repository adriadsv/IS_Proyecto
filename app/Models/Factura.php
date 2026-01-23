<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Factura extends Model
{
    protected $table = 'FACTURAS';

    protected $primaryKey = 'FAC_CODIGO';

    public $timestamps = false;

    protected $fillable = [
        'FAC_FECHA',
        'FAC_SUBTOTAL',
        'FAC_IVA',
        'FAC_MONTO_TOTAL',
        'FAC_ESTADO',
        'ID_CARRITO',
        'CLI_ID',
    ];

    protected $casts = [
        'FAC_CODIGO' => 'integer',
        'FAC_FECHA' => 'datetime',
        'FAC_SUBTOTAL' => 'decimal:2',
        'FAC_IVA' => 'decimal:2',
        'FAC_MONTO_TOTAL' => 'decimal:2',
        'ID_CARRITO' => 'integer',
        'CLI_ID' => 'integer',
    ];

    /**
     * Relación: Una factura pertenece a un cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'CLI_ID', 'CLI_ID');
    }

    /**
     * Relación: Una factura tiene muchos productos (a través de PROXFAC)
     */
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'PROXFAC', 'FAC_CODIGO', 'PRD_CODIGO')
            ->withPivot('DET_FAC_CANTIDAD', 'DET_FAC_PRECIO_UNITARIO', 'ESTADO_PROXFAC');
    }

    /**
     * Relación: Una factura tiene muchos detalles (PROXFAC)
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(Proxfac::class, 'FAC_CODIGO', 'FAC_CODIGO');
    }

    /**
     * Relación: Una factura puede tener muchos registros en kardex
     */
    public function kardexes(): HasMany
    {
        return $this->hasMany(Kardex::class, 'FAC_CODIGO', 'FAC_CODIGO');
    }

    // Accessors for compatibility
    public function getCodigoAttribute()
    {
        return $this->FAC_CODIGO;
    }

    public function getNumeroAttribute() // Often accessed as numero in routes
    {
        return $this->FAC_CODIGO;
    }

    public function getFechaAttribute()
    {
        return $this->FAC_FECHA;
    }

    public function getSubtotalAttribute()
    {
        return $this->FAC_SUBTOTAL;
    }

    public function getIvaAttribute()
    {
        return $this->FAC_IVA;
    }

    public function getTotalAttribute()
    {
        return $this->FAC_MONTO_TOTAL;
    }

    public function getEstadoAttribute()
    {
        return $this->FAC_ESTADO;
    }
}
