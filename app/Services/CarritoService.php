<?php

namespace App\Services;

use App\Models\Producto;
use App\Repositories\BodegaProductoTxtRepository;

class CarritoService
{
    private const SESSION_KEY = 'carrito_items';

    public function __construct(
        private readonly BodegaProductoTxtRepository $bodegaRepo,
        private readonly BodegaProductoService $bodegaService,
    ) {
    }

    private function isProductoActivo(Producto $producto): bool
    {
        return strtolower((string) $producto->estado) === 'activo';
    }

    /**
     * @return array<int,int> producto_id => cantidad
     */
    public function items(): array
    {
        /** @var array<int,int> $items */
        $items = session()->get(self::SESSION_KEY, []);

        return $items;
    }

    public function cantidadTotal(): int
    {
        return array_sum($this->items());
    }

    public function agregar(int $productoId, int $cantidad = 1): array
    {
        $cantidad = max(1, $cantidad);

        $producto = Producto::query()->find($productoId);
        if ($producto === null) {
            return [
                'ok' => false,
                'message' => 'No se encontró el registro solicitado.',
            ];
        }

        $codigo = trim((string) ($producto->codigo ?? ''));
        $bodega = $codigo === '' ? null : $this->bodegaRepo->findByCodigo($codigo);
        $stock = (int) ($bodega['stock'] ?? 0);
        $estadoBodega = (string) ($bodega['estado'] ?? '');

        if (! $this->isProductoActivo($producto) || $bodega === null || strtolower($estadoBodega) !== 'activo' || $stock <= 0) {
            return [
                'ok' => false,
                'message' => 'Revisa los campos marcados. Hay datos inválidos o incompletos.',
            ];
        }

        $items = $this->items();
        $actual = (int) ($items[$productoId] ?? 0);
        $nuevaCantidad = min($stock, $actual + $cantidad);
        $items[$productoId] = $nuevaCantidad;

        session()->put(self::SESSION_KEY, $items);

        return [
            'ok' => true,
        ];
    }

    public function quitarUno(int $productoId): void
    {
        $items = $this->items();
        $actual = (int) ($items[$productoId] ?? 0);

        if ($actual <= 1) {
            unset($items[$productoId]);
        } else {
            $items[$productoId] = $actual - 1;
        }

        session()->put(self::SESSION_KEY, $items);
    }

    public function quitarProducto(int $productoId): void
    {
        $items = $this->items();
        unset($items[$productoId]);
        session()->put(self::SESSION_KEY, $items);
    }

    public function vaciar(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function pagar(): array
    {
        $items = $this->items();
        if (count($items) === 0) {
            return [
                'ok' => false,
                'message' => 'Revisa los campos marcados. Hay datos inválidos o incompletos.',
            ];
        }

        $ids = array_map('intval', array_keys($items));

        $productos = Producto::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        foreach ($items as $productoId => $cantidad) {
            $producto = $productos->get((int) $productoId);
            if ($producto === null) {
                return [
                    'ok' => false,
                    'message' => 'No se encontró el registro solicitado.',
                ];
            }

            $codigo = trim((string) ($producto->codigo ?? ''));
            $bodega = $codigo === '' ? null : $this->bodegaRepo->findByCodigo($codigo);
            $stock = (int) ($bodega['stock'] ?? 0);
            $estadoBodega = (string) ($bodega['estado'] ?? '');

            if (! $this->isProductoActivo($producto) || $bodega === null || strtolower($estadoBodega) !== 'activo' || $stock < (int) $cantidad) {
                return [
                    'ok' => false,
                    'message' => 'Revisa los campos marcados. Hay datos inválidos o incompletos.',
                ];
            }
        }

        $applied = [];
        foreach ($items as $productoId => $cantidad) {
            $producto = $productos->get((int) $productoId);
            if ($producto === null) {
                continue;
            }
            $codigo = trim((string) ($producto->codigo ?? ''));
            if ($codigo === '') {
                continue;
            }

            $res = $this->bodegaService->adjustStock($codigo, -((int) $cantidad));
            if (! $res['ok']) {
                foreach ($applied as $c => $q) {
                    $this->bodegaService->adjustStock((string) $c, (int) $q);
                }

                return [
                    'ok' => false,
                    'message' => 'Revisa los campos marcados. Hay datos inválidos o incompletos.',
                ];
            }

            $applied[$codigo] = ($applied[$codigo] ?? 0) + (int) $cantidad;
        }

        $this->vaciar();

        return [
            'ok' => true,
        ];
    }
}
