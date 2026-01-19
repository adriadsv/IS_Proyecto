<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Services\TxtJsonlStore;

class BodegaProductoTxtRepository
{
    private string $path;

    public function __construct(
        private readonly TxtJsonlStore $store,
    ) {
        $this->path = storage_path('app/txt/bodega_productos.txt');
    }

    public function all(): array
    {
        return $this->store->loadAll($this->path);
    }

    public function findByCodigo(string $codigo): ?array
    {
        $codigo = trim($codigo);
        foreach ($this->all() as $row) {
            if (($row['codigo'] ?? null) === $codigo) {
                return $row;
            }
        }

        return null;
    }

    public function existsCodigo(string $codigo, ?string $ignoreCodigo = null): bool
    {
        $codigo = trim($codigo);
        foreach ($this->all() as $row) {
            $c = (string) ($row['codigo'] ?? '');
            if ($ignoreCodigo !== null && $c === $ignoreCodigo) {
                continue;
            }
            if ($c === $codigo) {
                return true;
            }
        }

        return false;
    }

    public function existsNombre(string $nombre, ?string $ignoreCodigo = null): bool
    {
        $nombre = trim($nombre);
        foreach ($this->all() as $row) {
            $c = (string) ($row['codigo'] ?? '');
            if ($ignoreCodigo !== null && $c === $ignoreCodigo) {
                continue;
            }
            if (trim((string) ($row['nombre'] ?? '')) === $nombre) {
                return true;
            }
        }

        return false;
    }

    public function save(array $producto): void
    {
        $codigo = (string) ($producto['codigo'] ?? '');
        $rows = $this->all();

        $updated = false;
        foreach ($rows as $i => $row) {
            if (($row['codigo'] ?? null) === $codigo) {
                $rows[$i] = $producto;
                $updated = true;
                break;
            }
        }

        if (! $updated) {
            $rows[] = $producto;
        }

        $this->store->saveAll($this->path, $rows);
    }

    public function deleteByCodigo(string $codigo): bool
    {
        $codigo = trim($codigo);
        $rows = $this->all();

        $new = [];
        $deleted = false;

        foreach ($rows as $row) {
            if (($row['codigo'] ?? null) === $codigo) {
                $deleted = true;
                continue;
            }
            $new[] = $row;
        }

        if ($deleted) {
            $this->store->saveAll($this->path, $new);
        }

        return $deleted;
    }
}
