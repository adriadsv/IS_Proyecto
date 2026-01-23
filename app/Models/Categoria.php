<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'CATEGORIA';

    protected $primaryKey = 'CAT_CODIGO';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'CAT_CODIGO',
        'CAT_NOMBRE',
        'CAT_DESCRIPCION',
    ];

    /**
     * Relación: Una categoría tiene muchos productos
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'CAT_CODIGO', 'CAT_CODIGO');
    }
}
