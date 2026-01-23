<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaccion extends Model
{
    protected $table = 'TRANSACCION';

    protected $primaryKey = 'TRN_ID';

    public $timestamps = false;

    protected $fillable = [
        'TRN_POS',
        'TRN_NEG',
    ];

    protected $casts = [
        'TRN_ID' => 'integer',
        'TRN_POS' => 'integer',
        'TRN_NEG' => 'integer',
    ];

    /**
     * Relación: Una transacción puede tener muchos registros en kardex
     */
    public function kardexes(): HasMany
    {
        return $this->hasMany(Kardex::class, 'TRN_ID', 'TRN_ID');
    }
}
