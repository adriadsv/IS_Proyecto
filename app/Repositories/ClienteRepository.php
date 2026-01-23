<?php

namespace App\Repositories;

use App\Models\Cliente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ClienteRepository
{
    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Cliente::query();

        $this->applyFilters($query, $filters);

        return $query
            ->orderByDesc('CLI_ID')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Cliente
    {
        return Cliente::query()->whereKey($id)->first();
    }

    public function existsByIdentificacion(string $identificacion, ?int $ignoreId = null): bool
    {
        return Cliente::query()
            ->where('CLI_CEDULA_RUC', $identificacion)
            ->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))
            ->exists();
    }

    public function existsByCorreo(?string $correo, ?int $ignoreId = null): bool
    {
        if ($correo === null || $correo === '') {
            return false;
        }

        return Cliente::query()
            ->where('CLI_CORREO', $correo)
            ->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))
            ->exists();
    }

    public function create(array $data): Cliente
    {
        return Cliente::query()->create($data);
    }

    public function update(Cliente $cliente, array $data): Cliente
    {
        $cliente->fill($data);
        $cliente->save();

        return $cliente;
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        $identificacion = trim((string) ($filters['identificacion'] ?? ''));
        $nombre = trim((string) ($filters['nombre'] ?? ''));
        $correo = trim((string) ($filters['correo'] ?? ''));
        $q = trim((string) ($filters['q'] ?? ''));

        if ($identificacion !== '') {
            $query->where('CLI_CEDULA_RUC', 'like', "%{$identificacion}%");
        }

        if ($nombre !== '') {
            $query->where('CLI_NOMBRE', 'like', "%{$nombre}%");
        }

        if ($correo !== '') {
            $query->where('CLI_CORREO', 'like', "%{$correo}%");
        }

        if ($q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                $sub
                    ->where('CLI_CEDULA_RUC', 'like', "%{$q}%")
                    ->orWhere('CLI_NOMBRE', 'like', "%{$q}%")
                    ->orWhere('CLI_CORREO', 'like', "%{$q}%");
            });
        }
    }
}
