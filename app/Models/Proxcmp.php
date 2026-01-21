<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proxcmp extends Model
{
    protected $table = 'PROXCMP';

    protected $primaryKey = ['CMP_CODIGO', 'PRD_CODIGO'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'CMP_CODIGO',
        'PRD_CODIGO',
        'DET_CMP_CANTIDAD',
        'DET_CMP_COSTO_UNITARIO',
        'ESTADO_PROXCMP',
    ];

    protected $casts = [
        'CMP_CODIGO' => 'integer',
        'DET_CMP_CANTIDAD' => 'integer',
        'DET_CMP_COSTO_UNITARIO' => 'decimal:2',
    ];

    /**
     * Relación: Un detalle de compra pertenece a una compra
     */
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'CMP_CODIGO', 'CMP_CODIGO');
    }

    /**
     * Relación: Un detalle de compra pertenece a un producto
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
