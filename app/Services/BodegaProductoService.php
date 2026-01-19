<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\BodegaProductoTxtRepository;
use App\Repositories\FacturaTxtRepository;

class BodegaProductoService
{
    public function __construct(
        private readonly BodegaProductoTxtRepository $repo,
        private readonly FacturaTxtRepository $facturaRepo,
    ) {
    }

    public function all(): array
    {
        return $this->repo->all();
    }

    public function find(string $codigo): ?array
    {
        return $this->repo->findByCodigo($codigo);
    }

    public function create(array $data): array
    {
        $codigo = trim((string) ($data['codigo'] ?? ''));
        $nombre = trim((string) ($data['nombre'] ?? ''));

        if ($this->repo->existsCodigo($codigo)) {
            return ['ok' => false, 'error' => 'duplicate', 'message' => 'Sistema notifica y no registra.'];
        }

        if ($this->repo->existsNombre($nombre)) {
            return ['ok' => false, 'error' => 'duplicate', 'message' => 'Sistema notifica y no registra.'];
        }

        $producto = [
            'codigo' => $codigo,
            'nombre' => $nombre,
            'categoria' => (string) ($data['categoria'] ?? ''),
            'unidad' => (string) ($data['unidad'] ?? ''),
            'stock_inicial' => (int) ($data['stock_inicial'] ?? 0),
            'stock' => (int) ($data['stock_inicial'] ?? 0),
            'stock_minimo' => (int) ($data['stock_minimo'] ?? 0),
            'precio' => null,
            'ubicacion' => $data['ubicacion'] === null || $data['ubicacion'] === '' ? null : (string) $data['ubicacion'],
            'estado' => (string) ($data['estado'] ?? 'activo'),
        ];

        $this->repo->save($producto);

        return ['ok' => true, 'producto' => $producto];
    }

    public function update(string $codigo, array $data): array
    {
        $existing = $this->repo->findByCodigo($codigo);
        if ($existing === null) {
            return ['ok' => false, 'error' => 'not_found', 'message' => 'No se encontró el registro solicitado.'];
        }

        $nuevoCodigo = trim((string) ($data['codigo'] ?? $codigo));
        $nombre = trim((string) ($data['nombre'] ?? ''));

        if ($this->repo->existsCodigo($nuevoCodigo, $codigo)) {
            return ['ok' => false, 'error' => 'duplicate', 'message' => 'Sistema notifica y no registra.'];
        }

        $producto = array_merge($existing, [
            'codigo' => $nuevoCodigo,
            'nombre' => $nombre,
            'categoria' => (string) ($data['categoria'] ?? ''),
            'unidad' => (string) ($data['unidad'] ?? ''),
            'stock_minimo' => (int) ($data['stock_minimo'] ?? 0),
            'precio' => null,
            'ubicacion' => $data['ubicacion'] === null || $data['ubicacion'] === '' ? null : (string) $data['ubicacion'],
            'estado' => (string) ($data['estado'] ?? 'activo'),
        ]);

        // Keep stock as current value; stock_inicial is immutable.
        $producto['stock'] = (int) ($existing['stock'] ?? 0);
        $producto['stock_inicial'] = (int) ($existing['stock_inicial'] ?? 0);

        // If codigo changed, remove old record and save new.
        if ($nuevoCodigo !== $codigo) {
            $this->repo->deleteByCodigo($codigo);
        }

        $this->repo->save($producto);

        return ['ok' => true, 'producto' => $producto];
    }

    public function inactivate(string $codigo): array
    {
        $producto = $this->repo->findByCodigo($codigo);
        if ($producto === null) {
            return ['ok' => false, 'error' => 'not_found', 'message' => 'No se encontró el registro solicitado.'];
        }

        if ($this->hasMovimientos($codigo)) {
            return ['ok' => false, 'error' => 'blocked', 'message' => 'El producto tiene movimientos asociados. No se puede eliminar.'];
        }

        $producto['estado'] = 'inactivo';
        $this->repo->save($producto);

        return ['ok' => true, 'producto' => $producto];
    }

    public function adjustStock(string $codigo, int $delta): array
    {
        $producto = $this->repo->findByCodigo($codigo);
        if ($producto === null) {
            return ['ok' => false, 'error' => 'not_found', 'message' => 'No se encontró el registro solicitado.'];
        }

        $stock = (int) ($producto['stock'] ?? 0);
        $newStock = $stock + $delta;

        if ($newStock < 0) {
            return ['ok' => false, 'error' => 'invalid', 'message' => 'No hay stock suficiente.'];
        }

        $producto['stock'] = $newStock;
        $this->repo->save($producto);

        return ['ok' => true, 'producto' => $producto];
    }

    public function filter(array $filters): array
    {
        $codigo = trim((string) ($filters['codigo'] ?? ''));
        $nombre = trim((string) ($filters['nombre'] ?? ''));
        $categoria = trim((string) ($filters['categoria'] ?? ''));
        $estado = $filters['estado'] ?? null;
        $stockBajo = (bool) ($filters['stock_bajo_minimo'] ?? false);

        $rows = $this->all();

        $rows = array_values(array_filter($rows, function (array $row) use ($codigo, $nombre, $categoria, $estado, $stockBajo) {
            if ($codigo !== '' && stripos((string) ($row['codigo'] ?? ''), $codigo) === false) {
                return false;
            }
            if ($nombre !== '' && stripos((string) ($row['nombre'] ?? ''), $nombre) === false) {
                return false;
            }
            if ($categoria !== '' && stripos((string) ($row['categoria'] ?? ''), $categoria) === false) {
                return false;
            }
            if (($estado === 'activo' || $estado === 'inactivo') && (string) ($row['estado'] ?? '') !== $estado) {
                return false;
            }
            if ($stockBajo) {
                $stock = (int) ($row['stock'] ?? 0);
                $min = (int) ($row['stock_minimo'] ?? 0);
                if ($stock >= $min) {
                    return false;
                }
            }

            return true;
        }));

        return $rows;
    }

    private function hasMovimientos(string $codigo): bool
    {
        foreach ($this->facturaRepo->all() as $factura) {
            $items = $factura['detalle'] ?? [];
            if (! is_array($items)) {
                continue;
            }
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                if (($item['producto_codigo'] ?? null) === $codigo) {
                    return true;
                }
            }
        }

        return false;
    }
}
