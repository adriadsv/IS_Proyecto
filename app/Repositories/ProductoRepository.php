<?php

namespace App\Repositories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductoRepository
{
    public function listActive(): Collection
    {
        return Producto::query()
            ->orderBy('PRD_DESCRIPCION')
            ->get();
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Producto::query();

        $this->applyFilters($query, $filters);

        return $query
            ->orderBy('PRD_CODIGO')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int|string $id): ?Producto
    {
        return Producto::query()->whereKey($id)->first();
    }

    public function existsByCodigo(string $codigo, int|string|null $ignoreKey = null): bool
    {
        return Producto::query()
            ->where('PRD_CODIGO', $codigo)
            ->when($ignoreKey, fn(Builder $q) => $q->where('PRD_CODIGO', '!=', $ignoreKey))
            ->exists();
    }

    public function existsByNombre(string $nombre, int|string|null $ignoreKey = null): bool
    {
        return Producto::query()
            ->where('PRD_DESCRIPCION', $nombre)
            ->when($ignoreKey, fn(Builder $q) => $q->where('PRD_CODIGO', '!=', $ignoreKey))
            ->exists();
    }

    public function create(array $data): Producto
    {
        return Producto::query()->create($data);
    }

    public function update(Producto $producto, array $data): Producto
    {
        $producto->fill($data);
        $producto->save();

        return $producto;
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        $codigo = trim((string) ($filters['codigo'] ?? ''));
        $nombre = trim((string) ($filters['nombre'] ?? ''));
        $categoria = trim((string) ($filters['categoria'] ?? ''));
        $q = trim((string) ($filters['q'] ?? ''));

        if ($codigo !== '') {
            $query->where('PRD_CODIGO', 'like', "%{$codigo}%");
        }

        if ($nombre !== '') {
            $query->where('PRD_DESCRIPCION', 'like', "%{$nombre}%");
        }

        if ($categoria !== '') {
            $query->where('CAT_CODIGO', 'like', "%{$categoria}%");
        }

        if ($q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                $sub
                    ->where('PRD_CODIGO', 'like', "%{$q}%")
                    ->orWhere('PRD_DESCRIPCION', 'like', "%{$q}%")
                    ->orWhere('CAT_CODIGO', 'like', "%{$q}%");
            });
        }
    }
}
