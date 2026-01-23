<?php

namespace App\Repositories;

use App\Models\Compra;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CompraRepository
{
    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Compra::query()->with('proveedor');

        $this->applyFilters($query, $filters);

        return $query
            ->orderByDesc('CMP_FECHA_ENTREGA')
            ->orderByDesc('CMP_CODIGO')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Compra
    {
        return Compra::query()
            ->with(['proveedor', 'detalles.producto'])
            ->whereKey($id)
            ->first();
    }

    public function existsComprobante(int $proveedorId, string $numeroComprobante, ?int $ignoreId = null): bool
    {
        return Compra::query()
            ->where('proveedor_id', $proveedorId)
            ->where('numero_comprobante', $numeroComprobante)
            ->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))
            ->exists();
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        $proveedorId = $filters['proveedor_id'] ?? null;
        $numero = trim((string) ($filters['numero_comprobante'] ?? ''));
        $tipo = $filters['tipo_comprobante'] ?? null;
        $estado = $filters['estado'] ?? null;
        $desde = $filters['fecha_desde'] ?? null;
        $hasta = $filters['fecha_hasta'] ?? null;

        if (! empty($proveedorId)) {
            $query->where('proveedor_id', (int) $proveedorId);
        }

        if ($numero !== '') {
            $query->where('numero_comprobante', 'like', "%{$numero}%");
        }

        if ($tipo === 'factura' || $tipo === 'nota_venta') {
            $query->where('tipo_comprobante', $tipo);
        }

        if ($estado === 'registrada' || $estado === 'anulada') {
            $query->where('estado', $estado);
        }

        if (! empty($desde)) {
            $query->whereDate('fecha_compra', '>=', $desde);
        }

        if (! empty($hasta)) {
            $query->whereDate('fecha_compra', '<=', $hasta);
        }
    }
}
