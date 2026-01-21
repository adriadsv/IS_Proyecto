<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kardex extends Model
{
    protected $table = 'KARDEX';

    protected $primaryKey = 'KAR_ID';

    public $timestamps = false;

    protected $fillable = [
        'PRD_CODIGO',
        'BOD_CODIGO',
        'FAC_CODIGO',
        'CMP_CODIGO',
        'TRN_ID',
        'KAR_FECHA',
        'KAR_SALDO',
    ];

    protected $casts = [
        'KAR_ID' => 'integer',
        'FAC_CODIGO' => 'integer',
        'CMP_CODIGO' => 'integer',
        'TRN_ID' => 'integer',
        'KAR_FECHA' => 'datetime',
        'KAR_SALDO' => 'integer',
    ];

    /**
     * Relación: Un kardex pertenece a un producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'PRD_CODIGO', 'PRD_CODIGO');
    }

    /**
     * Relación: Un kardex pertenece a una bodega
     */
    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'BOD_CODIGO', 'BOD_CODIGO');
    }

    /**
     * Relación: Un kardex puede pertenecer a una factura (nullable)
     */
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'FAC_CODIGO', 'FAC_CODIGO');
    }

    /**
     * Relación: Un kardex puede pertenecer a una compra (nullable)
     */
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'CMP_CODIGO', 'CMP_CODIGO');
    }

    /**
     * Relación: Un kardex puede pertenecer a una transacción (nullable)
     */
    public function transaccion(): BelongsTo
    {
        return $this->belongsTo(Transaccion::class, 'TRN_ID', 'TRN_ID');
    }
}
