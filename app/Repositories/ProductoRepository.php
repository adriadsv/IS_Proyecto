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
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Producto::query();

        $this->applyFilters($query, $filters);

        return $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Producto
    {
        return Producto::query()->whereKey($id)->first();
    }

    public function existsByCodigo(string $codigo, ?int $ignoreId = null): bool
    {
        return Producto::query()
            ->where('codigo', $codigo)
            ->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))
            ->exists();
    }

    public function existsByNombre(string $nombre, ?int $ignoreId = null): bool
    {
        return Producto::query()
            ->where('nombre', $nombre)
            ->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))
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
        $estado = $filters['estado'] ?? 'todos';

        if ($codigo !== '') {
            $query->where('codigo', 'like', "%{$codigo}%");
        }

        if ($nombre !== '') {
            $query->where('nombre', 'like', "%{$nombre}%");
        }

        if ($categoria !== '') {
            $query->where('categoria', 'like', "%{$categoria}%");
        }

        if ($q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                $sub
                    ->where('codigo', 'like', "%{$q}%")
                    ->orWhere('nombre', 'like', "%{$q}%")
                    ->orWhere('categoria', 'like', "%{$q}%");
            });
        }

        if ($estado === 'activo' || $estado === 'inactivo') {
            $query->where('estado', $estado);
        }
    }
}
