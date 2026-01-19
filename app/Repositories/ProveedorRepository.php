<?php

namespace App\Repositories;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProveedorRepository
{
    public function listActive(): Collection
    {
        return Proveedor::query()
            ->where('estado', 'activo')
            ->orderBy('razon_social')
            ->get();
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Proveedor::query();

        $this->applyFilters($query, $filters);

        return $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Proveedor
    {
        return Proveedor::query()->whereKey($id)->first();
    }

    public function existsByIdentificacion(string $identificacion, ?int $ignoreId = null): bool
    {
        return Proveedor::query()
            ->where('identificacion', $identificacion)
            ->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))
            ->exists();
    }

    public function existsByCorreo(?string $correo, ?int $ignoreId = null): bool
    {
        if ($correo === null || $correo === '') {
            return false;
        }

        return Proveedor::query()
            ->where('correo', $correo)
            ->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))
            ->exists();
    }

    public function create(array $data): Proveedor
    {
        return Proveedor::query()->create($data);
    }

    public function update(Proveedor $proveedor, array $data): Proveedor
    {
        $proveedor->fill($data);
        $proveedor->save();

        return $proveedor;
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        $identificacion = trim((string) ($filters['identificacion'] ?? ''));
        $razonSocial = trim((string) ($filters['razon_social'] ?? ''));
        $nombreComercial = trim((string) ($filters['nombre_comercial'] ?? ''));
        $q = trim((string) ($filters['q'] ?? ''));
        $estado = $filters['estado'] ?? 'todos';

        if ($identificacion !== '') {
            $query->where('identificacion', 'like', "%{$identificacion}%");
        }

        if ($razonSocial !== '') {
            $query->where('razon_social', 'like', "%{$razonSocial}%");
        }

        if ($nombreComercial !== '') {
            $query->where('nombre_comercial', 'like', "%{$nombreComercial}%");
        }

        if ($q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                $sub
                    ->where('identificacion', 'like', "%{$q}%")
                    ->orWhere('razon_social', 'like', "%{$q}%")
                    ->orWhere('nombre_comercial', 'like', "%{$q}%")
                    ->orWhere('correo', 'like', "%{$q}%");
            });
        }

        if ($estado === 'activo' || $estado === 'inactivo') {
            $query->where('estado', $estado);
        }
    }
}
