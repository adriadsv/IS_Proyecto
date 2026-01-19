<?php

namespace App\Services;

use App\Models\Producto;
use App\Repositories\BodegaProductoTxtRepository;
use App\Repositories\ProductoRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductoService
{
    public function __construct(
        private readonly ProductoRepository $productoRepository,
        private readonly BodegaProductoTxtRepository $bodegaRepo,
    ) {
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $paginator = $this->productoRepository->paginate($filters, $perPage);

        foreach ($paginator->getCollection() as $p) {
            if (! $p instanceof Producto) {
                continue;
            }
            $this->ensureProductoEnBodega($p, 0);
            $this->attachStockDesdeBodega($p);
        }

        return $paginator;
    }

    public function listActive(): Collection
    {
        $productos = $this->productoRepository->listActive();
        foreach ($productos as $p) {
            if (! $p instanceof Producto) {
                continue;
            }
            $this->ensureProductoEnBodega($p, 0);
            $this->attachStockDesdeBodega($p);
        }

        return $productos;
    }

    public function create(array $data): array
    {
        $data['estado'] = $data['estado'] ?? 'activo';

        if ($this->productoRepository->existsByCodigo($data['codigo'])) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'codigo',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        if ($this->productoRepository->existsByNombre($data['nombre'])) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'nombre',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        $producto = $this->productoRepository->create($data);
        $this->ensureProductoEnBodega($producto, 0);
        $this->attachStockDesdeBodega($producto);

        return [
            'ok' => true,
            'producto' => $producto,
        ];
    }

    public function update(int $id, array $data): array
    {
        $producto = $this->productoRepository->findById($id);

        if ($producto === null) {
            return [
                'ok' => false,
                'error' => 'not_found',
                'message' => 'No se encontró el registro solicitado.',
            ];
        }

        if ($this->productoRepository->existsByCodigo($data['codigo'], $producto->id)) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'codigo',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        if ($this->productoRepository->existsByNombre($data['nombre'], $producto->id)) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'nombre',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        $oldCodigo = (string) ($producto->codigo ?? '');
        $producto = $this->productoRepository->update($producto, $data);
        $this->ensureProductoEnBodega($producto, 0);
        $this->moveProductoEnBodegaSiCambioCodigo($oldCodigo, (string) ($producto->codigo ?? ''));
        $this->attachStockDesdeBodega($producto);

        return [
            'ok' => true,
            'producto' => $producto,
        ];
    }

    public function inactivate(int $id): array
    {
        $producto = $this->productoRepository->findById($id);

        if ($producto === null) {
            return [
                'ok' => false,
                'error' => 'not_found',
                'message' => 'No se encontró el registro solicitado.',
            ];
        }

        if ($producto->estado !== 'inactivo') {
            $producto->estado = 'inactivo';
            $producto->save();
        }

        return [
            'ok' => true,
            'producto' => $producto,
        ];
    }

    public function find(int $id): ?Producto
    {
        $producto = $this->productoRepository->findById($id);
        if ($producto === null) {
            return null;
        }

        $this->ensureProductoEnBodega($producto, 0);
        $this->attachStockDesdeBodega($producto);

        return $producto;
    }

    private function ensureProductoEnBodega(Producto $producto, int $stock): void
    {
        $codigo = trim((string) ($producto->codigo ?? ''));
        if ($codigo === '') {
            return;
        }

        $existing = $this->bodegaRepo->findByCodigo($codigo);
        if ($existing !== null) {
            $existing['nombre'] = (string) ($producto->nombre ?? '');
            $existing['categoria'] = (string) ($producto->categoria ?? '');
            $this->bodegaRepo->save($existing);

            return;
        }

        $row = [
            'codigo' => $codigo,
            'nombre' => (string) ($producto->nombre ?? ''),
            'categoria' => (string) ($producto->categoria ?? ''),
            'unidad' => '',
            'stock_inicial' => max(0, $stock),
            'stock' => max(0, $stock),
            'stock_minimo' => 0,
            'precio' => null,
            'ubicacion' => null,
            'estado' => 'activo',
        ];

        $this->bodegaRepo->save($row);
    }

    private function attachStockDesdeBodega(Producto $producto): void
    {
        $codigo = trim((string) ($producto->codigo ?? ''));
        if ($codigo === '') {
            return;
        }

        $row = $this->bodegaRepo->findByCodigo($codigo);
        $producto->setAttribute('stock', (int) ($row['stock'] ?? 0));
    }

    private function moveProductoEnBodegaSiCambioCodigo(string $oldCodigo, string $newCodigo): void
    {
        $oldCodigo = trim($oldCodigo);
        $newCodigo = trim($newCodigo);
        if ($oldCodigo === '' || $newCodigo === '' || $oldCodigo === $newCodigo) {
            return;
        }

        $oldRow = $this->bodegaRepo->findByCodigo($oldCodigo);
        if ($oldRow === null) {
            return;
        }

        $newRow = $this->bodegaRepo->findByCodigo($newCodigo);
        if ($newRow !== null) {
            return;
        }

        $oldRow['codigo'] = $newCodigo;
        $this->bodegaRepo->save($oldRow);
        $this->bodegaRepo->deleteByCodigo($oldCodigo);
    }
}
