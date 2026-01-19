<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use App\Repositories\CompraRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompraService
{
    public function __construct(
        private readonly CompraRepository $compraRepository,
    ) {
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->compraRepository->paginate($filters, $perPage);
    }

    public function find(int $id): ?Compra
    {
        return $this->compraRepository->findById($id);
    }

    public function create(array $data): array
    {
        $proveedorId = (int) $data['proveedor_id'];
        $numero = (string) $data['numero_comprobante'];

        if ($this->compraRepository->existsComprobante($proveedorId, $numero)) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'numero_comprobante',
                'message' => 'El número de comprobante ya existe y no permite registrar la compra.',
            ];
        }

        $detalleRows = $this->normalizeDetalle($data);
        if (count($detalleRows) === 0) {
            return [
                'ok' => false,
                'error' => 'invalid',
                'message' => 'Revisa los campos marcados. Hay datos inválidos o incompletos.',
            ];
        }

        return DB::transaction(function () use ($data, $detalleRows) {
            $compra = Compra::query()->create([
                'proveedor_id' => (int) $data['proveedor_id'],
                'fecha_compra' => $data['fecha_compra'],
                'numero_comprobante' => $data['numero_comprobante'],
                'tipo_comprobante' => $data['tipo_comprobante'],
                'subtotal' => 0,
                'impuesto' => 0,
                'total' => 0,
                'estado' => 'registrada',
            ]);

            $subtotal = 0;
            foreach ($detalleRows as $row) {
                $lineSubtotal = $row['cantidad'] * $row['costo_unitario'];
                $subtotal += $lineSubtotal;

                CompraDetalle::query()->create([
                    'compra_id' => $compra->id,
                    'producto_id' => $row['producto_id'],
                    'cantidad' => $row['cantidad'],
                    'costo_unitario' => $row['costo_unitario'],
                    'subtotal' => $lineSubtotal,
                ]);

                Producto::query()->whereKey($row['producto_id'])->increment('stock', $row['cantidad']);
            }

            $compra->subtotal = $subtotal;
            $compra->impuesto = 0;
            $compra->total = $subtotal;
            $compra->save();

            return [
                'ok' => true,
                'compra' => $compra,
            ];
        });
    }

    public function update(int $id, array $data): array
    {
        $compra = $this->compraRepository->findById($id);

        if ($compra === null) {
            return [
                'ok' => false,
                'error' => 'not_found',
                'message' => 'No se encontró el registro solicitado.',
            ];
        }

        if ($compra->estado === 'anulada') {
            return [
                'ok' => false,
                'error' => 'invalid',
                'message' => 'Revisa los campos marcados. Hay datos inválidos o incompletos.',
            ];
        }

        $proveedorId = (int) $data['proveedor_id'];
        $numero = (string) $data['numero_comprobante'];

        if ($this->compraRepository->existsComprobante($proveedorId, $numero, $compra->id)) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'numero_comprobante',
                'message' => 'El número de comprobante ya existe y no permite registrar la compra.',
            ];
        }

        $detalleRows = $this->normalizeDetalle($data);
        if (count($detalleRows) === 0) {
            return [
                'ok' => false,
                'error' => 'invalid',
                'message' => 'Revisa los campos marcados. Hay datos inválidos o incompletos.',
            ];
        }

        return DB::transaction(function () use ($compra, $data, $detalleRows) {
            // Revert old stock
            foreach ($compra->detalles as $detalle) {
                Producto::query()->whereKey($detalle->producto_id)->decrement('stock', $detalle->cantidad);
            }

            // Replace details
            CompraDetalle::query()->where('compra_id', $compra->id)->delete();

            $subtotal = 0;
            foreach ($detalleRows as $row) {
                $lineSubtotal = $row['cantidad'] * $row['costo_unitario'];
                $subtotal += $lineSubtotal;

                CompraDetalle::query()->create([
                    'compra_id' => $compra->id,
                    'producto_id' => $row['producto_id'],
                    'cantidad' => $row['cantidad'],
                    'costo_unitario' => $row['costo_unitario'],
                    'subtotal' => $lineSubtotal,
                ]);

                Producto::query()->whereKey($row['producto_id'])->increment('stock', $row['cantidad']);
            }

            $compra->fill([
                'proveedor_id' => (int) $data['proveedor_id'],
                'fecha_compra' => $data['fecha_compra'],
                'numero_comprobante' => $data['numero_comprobante'],
                'tipo_comprobante' => $data['tipo_comprobante'],
            ]);

            $compra->subtotal = $subtotal;
            $compra->impuesto = 0;
            $compra->total = $subtotal;
            $compra->save();

            return [
                'ok' => true,
                'compra' => $compra,
            ];
        });
    }

    public function anular(int $id): array
    {
        $compra = $this->compraRepository->findById($id);

        if ($compra === null) {
            return [
                'ok' => false,
                'error' => 'not_found',
                'message' => 'No se encontró el registro solicitado.',
            ];
        }

        if ($compra->estado === 'anulada') {
            return [
                'ok' => true,
                'compra' => $compra,
            ];
        }

        return DB::transaction(function () use ($compra) {
            foreach ($compra->detalles as $detalle) {
                Producto::query()->whereKey($detalle->producto_id)->decrement('stock', $detalle->cantidad);
            }

            $compra->estado = 'anulada';
            $compra->save();

            return [
                'ok' => true,
                'compra' => $compra,
            ];
        });
    }

    /**
     * @return array<int, array{producto_id:int,cantidad:int,costo_unitario:float}>
     */
    private function normalizeDetalle(array $data): array
    {
        $productos = $data['detalle_producto_id'] ?? [];
        $cantidades = $data['detalle_cantidad'] ?? [];
        $costos = $data['detalle_costo_unitario'] ?? [];

        $rows = [];
        $count = max(count($productos), count($cantidades), count($costos));

        for ($i = 0; $i < $count; $i++) {
            $productoId = (int) ($productos[$i] ?? 0);
            $cantidad = (int) ($cantidades[$i] ?? 0);
            $costo = (float) ($costos[$i] ?? 0);

            if ($productoId <= 0 || $cantidad <= 0 || $costo <= 0) {
                continue;
            }

            $rows[] = [
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'costo_unitario' => $costo,
            ];
        }

        return $rows;
    }
}
