<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Producto;
use App\Repositories\BodegaProductoTxtRepository;
use App\Repositories\FacturaTxtRepository;

class FacturaTxtService
{
    public function __construct(
        private readonly FacturaTxtRepository $repo,
        private readonly BodegaProductoTxtRepository $bodegaRepo,
        private readonly BodegaProductoService $bodegaService,
    ) {
    }

    public function all(): array
    {
        // Read from Database
        return \App\Models\Factura::with('cliente')->get()->map(function ($f) {
            return [
                'numero' => $f->FAC_CODIGO,
                'fecha' => $f->FAC_FECHA ? $f->FAC_FECHA->format('Y-m-d') : '',
                'cliente' => $f->cliente ? $f->cliente->CLI_NOMBRE : 'Consumidor Final',
                'total' => (float) $f->FAC_MONTO_TOTAL,
                'estado' => $f->FAC_ESTADO === 'PAG' ? 'Emitida' : ($f->FAC_ESTADO === 'ANU' ? 'Anulada' : $f->FAC_ESTADO),
                'metodo_pago' => 'Efectivo', // Default or from new column if exists
            ];
        })->toArray();
    }

    public function find(int $numero): ?array
    {
        return $this->repo->findByNumero($numero);
    }

    public function create(array $data): array
    {
        $cliente = trim((string) ($data['cliente'] ?? ''));
        if ($cliente === '') {
            return ['ok' => false, 'error' => 'invalid', 'message' => 'Cliente no encontrado/no seleccionado.'];
        }

        $detalleBase = $this->normalizeDetalle($data);
        if (count($detalleBase) === 0) {
            return ['ok' => false, 'error' => 'invalid', 'message' => 'El sistema bloquea la emisión y solicita agregar productos.'];
        }

        $detalle = [];

        // Validate products and stock
        foreach ($detalleBase as $item) {
            $producto = $this->findProductoActivo($item['producto_codigo']);
            if ($producto === null) {
                return ['ok' => false, 'error' => 'invalid', 'message' => 'Producto no disponible/stock insuficiente. Ajusta cantidades o cambia el producto.'];
            }

            $prodBodega = $this->bodegaRepo->findByCodigo($item['producto_codigo']);
            if ($prodBodega === null || (string) ($prodBodega['estado'] ?? '') !== 'activo') {
                return ['ok' => false, 'error' => 'invalid', 'message' => 'Producto no disponible/stock insuficiente. Ajusta cantidades o cambia el producto.'];
            }
            if ($item['cantidad'] > (int) ($prodBodega['stock'] ?? 0)) {
                return ['ok' => false, 'error' => 'invalid', 'message' => 'Producto no disponible/stock insuficiente. Ajusta cantidades o cambia el producto.'];
            }

            $precio = (float) ($producto->precio ?? 0);
            if ($precio <= 0) {
                return ['ok' => false, 'error' => 'invalid', 'message' => 'El producto no tiene precio registrado.'];
            }

            $detalle[] = [
                'producto_codigo' => $item['producto_codigo'],
                'cantidad' => $item['cantidad'],
                'precio' => $precio,
                'subtotal' => $item['cantidad'] * $precio,
            ];
        }

        $numero = $this->repo->nextNumero();

        $subtotal = 0.0;
        foreach ($detalle as $item) {
            $subtotal += $item['cantidad'] * $item['precio'];
        }

        $descuento = $data['descuento'] === null || $data['descuento'] === '' ? 0.0 : (float) $data['descuento'];
        $impuesto = 0.0;
        $total = max(0.0, $subtotal - $descuento + $impuesto);

        $factura = [
            'numero' => $numero,
            'cliente' => (string) ($data['cliente'] ?? ''),
            'fecha' => (string) ($data['fecha'] ?? ''),
            'tipo_numero_comprobante' => (string) ($data['tipo_numero_comprobante'] ?? ''),
            'metodo_pago' => (string) ($data['metodo_pago'] ?? ''),
            'detalle' => $detalle,
            'descuento' => $descuento,
            'observacion' => $data['observacion'] === null || $data['observacion'] === '' ? null : (string) $data['observacion'],
            'subtotal' => $subtotal,
            'impuesto' => $impuesto,
            'total' => $total,
            'estado' => 'Emitida',
            'fecha_anulacion' => null,
            'motivo_anulacion' => null,
        ];

        // Apply stock decrement
        foreach ($detalle as $item) {
            $res = $this->bodegaService->adjustStock($item['producto_codigo'], -$item['cantidad']);
            if (!$res['ok']) {
                return ['ok' => false, 'error' => 'invalid', 'message' => 'Producto no disponible/stock insuficiente. Ajusta cantidades o cambia el producto.'];
            }
        }

        $this->repo->save($factura);

        return ['ok' => true, 'factura' => $factura];
    }

    public function update(int $numero, array $data): array
    {
        $existing = $this->repo->findByNumero($numero);
        if ($existing === null) {
            return ['ok' => false, 'error' => 'not_found', 'message' => 'No se encontró el registro solicitado.'];
        }

        if (($existing['estado'] ?? '') === 'Anulada') {
            return ['ok' => false, 'error' => 'invalid', 'message' => 'La factura está anulada. No se puede modificar.'];
        }

        $cliente = trim((string) ($data['cliente'] ?? ''));
        if ($cliente === '') {
            return ['ok' => false, 'error' => 'invalid', 'message' => 'Cliente no encontrado/no seleccionado.'];
        }

        $newDetalleBase = $this->normalizeDetalle($data);
        if (count($newDetalleBase) === 0) {
            return ['ok' => false, 'error' => 'invalid', 'message' => 'El sistema bloquea la emisión y solicita agregar productos.'];
        }

        $newDetalle = [];

        // Revert old stock
        $oldDetalle = $existing['detalle'] ?? [];
        if (is_array($oldDetalle)) {
            foreach ($oldDetalle as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $codigo = (string) ($item['producto_codigo'] ?? '');
                $cantidad = (int) ($item['cantidad'] ?? 0);
                if ($codigo !== '' && $cantidad > 0) {
                    $this->bodegaService->adjustStock($codigo, $cantidad);
                }
            }
        }

        // Validate new and apply decrement
        foreach ($newDetalleBase as $item) {
            $producto = $this->findProductoActivo($item['producto_codigo']);
            $prodBodega = $this->bodegaRepo->findByCodigo($item['producto_codigo']);
            if ($producto === null || $prodBodega === null || (string) ($prodBodega['estado'] ?? '') !== 'activo') {
                // rollback: re-apply old decrement
                if (is_array($oldDetalle)) {
                    foreach ($oldDetalle as $it) {
                        if (!is_array($it)) {
                            continue;
                        }
                        $codigo = (string) ($it['producto_codigo'] ?? '');
                        $cantidad = (int) ($it['cantidad'] ?? 0);
                        if ($codigo !== '' && $cantidad > 0) {
                            $this->bodegaService->adjustStock($codigo, -$cantidad);
                        }
                    }
                }
                return ['ok' => false, 'error' => 'invalid', 'message' => 'Producto no disponible/stock insuficiente. Ajusta cantidades o cambia el producto.'];
            }
            if ($item['cantidad'] > (int) ($prodBodega['stock'] ?? 0)) {
                if (is_array($oldDetalle)) {
                    foreach ($oldDetalle as $it) {
                        if (!is_array($it)) {
                            continue;
                        }
                        $codigo = (string) ($it['producto_codigo'] ?? '');
                        $cantidad = (int) ($it['cantidad'] ?? 0);
                        if ($codigo !== '' && $cantidad > 0) {
                            $this->bodegaService->adjustStock($codigo, -$cantidad);
                        }
                    }
                }
                return ['ok' => false, 'error' => 'invalid', 'message' => 'Producto no disponible/stock insuficiente. Ajusta cantidades o cambia el producto.'];
            }

            $precio = (float) ($producto->precio ?? 0);
            if ($precio <= 0) {
                if (is_array($oldDetalle)) {
                    foreach ($oldDetalle as $it) {
                        if (!is_array($it)) {
                            continue;
                        }
                        $codigo = (string) ($it['producto_codigo'] ?? '');
                        $cantidad = (int) ($it['cantidad'] ?? 0);
                        if ($codigo !== '' && $cantidad > 0) {
                            $this->bodegaService->adjustStock($codigo, -$cantidad);
                        }
                    }
                }
                return ['ok' => false, 'error' => 'invalid', 'message' => 'El producto no tiene precio registrado.'];
            }

            $newDetalle[] = [
                'producto_codigo' => $item['producto_codigo'],
                'cantidad' => $item['cantidad'],
                'precio' => $precio,
                'subtotal' => $item['cantidad'] * $precio,
            ];
        }

        foreach ($newDetalle as $item) {
            $res = $this->bodegaService->adjustStock($item['producto_codigo'], -$item['cantidad']);
            if (!$res['ok']) {
                return ['ok' => false, 'error' => 'invalid', 'message' => 'Producto no disponible/stock insuficiente. Ajusta cantidades o cambia el producto.'];
            }
        }

        $subtotal = 0.0;
        foreach ($newDetalle as $item) {
            $subtotal += $item['cantidad'] * $item['precio'];
        }

        $descuento = $data['descuento'] === null || $data['descuento'] === '' ? 0.0 : (float) $data['descuento'];
        $impuesto = 0.0;
        $total = max(0.0, $subtotal - $descuento + $impuesto);

        $factura = array_merge($existing, [
            'cliente' => (string) ($data['cliente'] ?? ''),
            'fecha' => (string) ($data['fecha'] ?? ''),
            'tipo_numero_comprobante' => (string) ($data['tipo_numero_comprobante'] ?? ''),
            'metodo_pago' => (string) ($data['metodo_pago'] ?? ''),
            'detalle' => $newDetalle,
            'descuento' => $descuento,
            'observacion' => $data['observacion'] === null || $data['observacion'] === '' ? null : (string) $data['observacion'],
            'subtotal' => $subtotal,
            'impuesto' => $impuesto,
            'total' => $total,
        ]);

        $this->repo->save($factura);

        return ['ok' => true, 'factura' => $factura];
    }

    public function anular(int $numero, ?string $motivo = null, ?string $fecha = null): array
    {
        $existing = $this->repo->findByNumero($numero);
        if ($existing === null) {
            return ['ok' => false, 'error' => 'not_found', 'message' => 'No se encontró el registro solicitado.'];
        }

        if (($existing['estado'] ?? '') === 'Anulada') {
            return ['ok' => false, 'error' => 'invalid', 'message' => 'La factura ya está anulada.'];
        }

        // Restore stock
        $detalle = $existing['detalle'] ?? [];
        if (is_array($detalle)) {
            foreach ($detalle as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $codigo = (string) ($item['producto_codigo'] ?? '');
                $cantidad = (int) ($item['cantidad'] ?? 0);
                if ($codigo !== '' && $cantidad > 0) {
                    $this->bodegaService->adjustStock($codigo, $cantidad);
                }
            }
        }

        $existing['estado'] = 'Anulada';
        $existing['fecha_anulacion'] = $fecha;
        $existing['motivo_anulacion'] = $motivo;

        $this->repo->save($existing);

        return ['ok' => true, 'factura' => $existing];
    }

    public function filter(array $filters): array
    {
        $numero = trim((string) ($filters['numero'] ?? ''));
        $cliente = trim((string) ($filters['cliente'] ?? ''));
        $desde = trim((string) ($filters['fecha_desde'] ?? ''));
        $hasta = trim((string) ($filters['fecha_hasta'] ?? ''));
        $estado = $filters['estado'] ?? null;
        $metodo = trim((string) ($filters['metodo_pago'] ?? ''));

        $rows = $this->all();

        $rows = array_values(array_filter($rows, function (array $row) use ($numero, $cliente, $desde, $hasta, $estado, $metodo) {
            if ($numero !== '' && stripos((string) ($row['numero'] ?? ''), $numero) === false) {
                return false;
            }
            if ($cliente !== '' && stripos((string) ($row['cliente'] ?? ''), $cliente) === false) {
                return false;
            }
            if ($metodo !== '' && stripos((string) ($row['metodo_pago'] ?? ''), $metodo) === false) {
                return false;
            }
            if (($estado === 'Emitida' || $estado === 'Anulada') && (string) ($row['estado'] ?? '') !== $estado) {
                return false;
            }
            if ($desde !== '' && (string) ($row['fecha'] ?? '') < $desde) {
                return false;
            }
            if ($hasta !== '' && (string) ($row['fecha'] ?? '') > $hasta) {
                return false;
            }

            return true;
        }));

        return $rows;
    }

    public function listProductosActivos(): array
    {
        $rows = [];
        $productos = Producto::query()
            ->with('bodegas')
            ->orderBy('PRD_DESCRIPCION')
            ->get();

        foreach ($productos as $p) {
            $stock = (int) $p->bodegas->sum('pivot.DET_BOD_CANTIDAD');

            if ($stock > 0) {
                $rows[] = [
                    'codigo' => $p->PRD_CODIGO,
                    'nombre' => $p->PRD_DESCRIPCION,
                    'precio' => (float) $p->PRD_PRECIO,
                    'stock' => $stock,
                    'estado' => 'activo',
                ];
            }
        }

        return $rows;
    }

    public function listProductosParaEdicion(array $factura): array
    {
        $cantidadEnFactura = [];
        $detalle = $factura['detalle'] ?? [];
        if (is_array($detalle)) {
            foreach ($detalle as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $codigo = trim((string) ($item['producto_codigo'] ?? ''));
                $cantidad = (int) ($item['cantidad'] ?? 0);
                if ($codigo === '' || $cantidad <= 0) {
                    continue;
                }
                $cantidadEnFactura[$codigo] = ($cantidadEnFactura[$codigo] ?? 0) + $cantidad;
            }
        }

        $bodegaIndex = [];
        foreach ($this->bodegaRepo->all() as $r) {
            if (!is_array($r)) {
                continue;
            }
            $codigo = (string) ($r['codigo'] ?? '');
            if ($codigo !== '') {
                $bodegaIndex[$codigo] = $r;
            }
        }

        $rows = [];
        foreach (Producto::query()->where('estado', 'activo')->orderBy('nombre')->get() as $p) {
            $codigo = (string) ($p->codigo ?? '');
            $b = $codigo !== '' ? ($bodegaIndex[$codigo] ?? null) : null;

            $stockActual = (int) (($b['stock'] ?? 0) ?? 0);
            $stockEfectivo = $stockActual + (int) ($cantidadEnFactura[$codigo] ?? 0);

            $rows[] = [
                'codigo' => $codigo,
                'nombre' => (string) ($p->nombre ?? ''),
                'precio' => (float) ($p->precio ?? 0),
                'stock' => $stockEfectivo,
                'estado' => 'activo',
            ];
        }

        return $rows;
    }

    private function findProductoActivo(string $codigo): ?Producto
    {
        $codigo = trim($codigo);
        if ($codigo === '') {
            return null;
        }

        return Producto::query()
            ->where('codigo', $codigo)
            ->where('estado', 'activo')
            ->first();
    }

    private function normalizeDetalle(array $data): array
    {
        $productos = $data['detalle_producto_codigo'] ?? [];
        $cantidades = $data['detalle_cantidad'] ?? [];

        $rows = [];
        $count = max(count($productos), count($cantidades));

        for ($i = 0; $i < $count; $i++) {
            $codigo = trim((string) ($productos[$i] ?? ''));
            $cantidad = (int) ($cantidades[$i] ?? 0);

            if ($codigo === '' || $cantidad <= 0) {
                continue;
            }

            $rows[] = [
                'producto_codigo' => $codigo,
                'cantidad' => $cantidad,
            ];
        }

        return $rows;
    }
}
