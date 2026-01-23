<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'PROVEEDORES';

    protected $primaryKey = 'PRV_ID';

    public $timestamps = false;

    protected $fillable = [
        'PRV_RUC',
        'PRV_RAZON_SOCIAL',
        'PRV_CORREO',
        'PRV_DIRECCION',
        'PRV_TELEFONO',
    ];

    // Accessors for compatibility
    public function getIdentificacionAttribute()
    {
        return $this->PRV_RUC;
    }

    public function getEstadoAttribute()
    {
        // Schema doesn't have status, assuming active
        return 'activo';
    }

    protected $casts = [
        'PRV_ID' => 'integer',
    ];

    /**
     * Relación: Un proveedor puede tener muchas compras
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'PRV_ID', 'PRV_ID');
    }

    // Accessors for compatibility
    public function getIdAttribute()
    {
        return $this->PRV_ID;
    }

    public function getRucAttribute()
    {
        return $this->PRV_RUC;
    }

    public function getNombreAttribute()
    {
        return $this->PRV_RAZON_SOCIAL; // Map nombre to razon social
    }

    public function getRazonSocialAttribute()
    {
        return $this->PRV_RAZON_SOCIAL;
    }

    public function getCorreoAttribute()
    {
        return $this->PRV_CORREO;
    }

    public function getEmailAttribute()
    {
        return $this->PRV_CORREO;
    }

    public function getDireccionAttribute()
    {
        return $this->PRV_DIRECCION;
    }

    public function getTelefonoAttribute()
    {
        return $this->PRV_TELEFONO;
    }
}
