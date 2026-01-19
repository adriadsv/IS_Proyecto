<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Services\TxtJsonlStore;

class FacturaTxtRepository
{
    private string $path;

    public function __construct(
        private readonly TxtJsonlStore $store,
    ) {
        $this->path = storage_path('app/txt/facturas.txt');
    }

    public function all(): array
    {
        return $this->store->loadAll($this->path);
    }

    public function nextNumero(): int
    {
        $max = 0;
        foreach ($this->all() as $row) {
            $n = (int) ($row['numero'] ?? 0);
            if ($n > $max) {
                $max = $n;
            }
        }

        return $max + 1;
    }

    public function findByNumero(int $numero): ?array
    {
        foreach ($this->all() as $row) {
            if ((int) ($row['numero'] ?? 0) === $numero) {
                return $row;
            }
        }

        return null;
    }

    public function save(array $factura): void
    {
        $numero = (int) ($factura['numero'] ?? 0);
        $rows = $this->all();

        $updated = false;
        foreach ($rows as $i => $row) {
            if ((int) ($row['numero'] ?? 0) === $numero) {
                $rows[$i] = $factura;
                $updated = true;
                break;
            }
        }

        if (! $updated) {
            $rows[] = $factura;
        }

        $this->store->saveAll($this->path, $rows);
    }
}
