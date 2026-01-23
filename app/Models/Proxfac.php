<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proxfac extends Model
{
    protected $table = 'PROXFAC';

    protected $primaryKey = ['FAC_CODIGO', 'PRD_CODIGO'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'FAC_CODIGO',
        'PRD_CODIGO',
        'DET_FAC_CANTIDAD',
        'DET_FAC_PRECIO_UNITARIO',
        'ESTADO_PROXFAC',
    ];

    protected $casts = [
        'FAC_CODIGO' => 'integer',
        'DET_FAC_CANTIDAD' => 'integer',
        'DET_FAC_PRECIO_UNITARIO' => 'decimal:2',
    ];

    /**
     * Relación: Un detalle de factura pertenece a una factura
     */
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'FAC_CODIGO', 'FAC_CODIGO');
    }

    /**
     * Relación: Un detalle de factura pertenece a un producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'PRD_CODIGO', 'PRD_CODIGO');
    }

    /**
     * Set the keys for a save update query.
     * Soporte para llaves primarias compuestas
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     * Soporte para llaves primarias compuestas
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}
