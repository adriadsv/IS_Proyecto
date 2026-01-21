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
            ->orderBy('PRV_RAZON_SOCIAL')
            ->get();
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Proveedor::query();

        $this->applyFilters($query, $filters);

        return $query
            ->orderByDesc('PRV_ID')
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
            ->where('PRV_RUC', $identificacion)
            ->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))
            ->exists();
    }

    public function existsByCorreo(?string $correo, ?int $ignoreId = null): bool
    {
        if ($correo === null || $correo === '') {
            return false;
        }

        return Proveedor::query()
            ->where('PRV_CORREO', $correo)
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
        $q = trim((string) ($filters['q'] ?? ''));

        if ($identificacion !== '') {
            $query->where('PRV_RUC', 'like', "%{$identificacion}%");
        }

        if ($razonSocial !== '') {
            $query->where('PRV_RAZON_SOCIAL', 'like', "%{$razonSocial}%");
        }

        if ($q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                $sub
                    ->where('PRV_RUC', 'like', "%{$q}%")
                    ->orWhere('PRV_RAZON_SOCIAL', 'like', "%{$q}%")
                    ->orWhere('PRV_CORREO', 'like', "%{$q}%");
            });
        }
    }
}
