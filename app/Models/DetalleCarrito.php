<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleCarrito extends Model
{
    protected $table = 'DETALLE_CARRITO';

    protected $primaryKey = ['PRD_CODIGO', 'CAR_CODIGO'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'PRD_CODIGO',
        'CAR_CODIGO',
        'DET_CAR_CANTIDAD',
    ];

    protected $casts = [
        'CAR_CODIGO' => 'integer',
        'DET_CAR_CANTIDAD' => 'integer',
    ];

    /**
     * Relación: Un detalle de carrito pertenece a un producto
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
