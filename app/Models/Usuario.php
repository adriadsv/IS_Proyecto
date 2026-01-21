<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usuario extends Model
{
    protected $table = 'USUARIOS';

    protected $primaryKey = 'USU_ID';

    public $timestamps = false;

    protected $fillable = [
        'CLI_ID',
        'USU_NOMBRE',
        'USU_CONTRASENA',
    ];

    protected $casts = [
        'USU_ID' => 'integer',
        'CLI_ID' => 'integer',
    ];

    protected $hidden = [
        'USU_CONTRASENA',
    ];

    /**
     * Relación: Un usuario pertenece a un cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'CLI_ID', 'CLI_ID');
    }
}
