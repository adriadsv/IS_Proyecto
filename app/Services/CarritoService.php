<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Factura;
use App\Models\Proxfac;
use App\Models\Cliente;
use App\Repositories\BodegaProductoTxtRepository;
use Illuminate\Support\Facades\DB;

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

        // Obtener o crear cliente para el usuario autenticado
        $user = auth()->user();
        $cliente = $this->obtenerOCrearCliente($user);

        if ($cliente === null) {
            return [
                'ok' => false,
                'message' => 'No se pudo identificar el cliente. Inicia sesión para continuar.',
            ];
        }

        $ids = array_map('intval', array_keys($items));

        $productos = Producto::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        // Validar productos y stock
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

        // Calcular totales
        $subtotal = 0.0;
        foreach ($items as $productoId => $cantidad) {
            $producto = $productos->get((int) $productoId);
            if ($producto === null) {
                continue;
            }
            $precio = (float) $producto->PRD_PRECIO;
            $subtotal += $precio * (int) $cantidad;
        }

        $iva = round($subtotal * 0.15, 2);
        $total = round($subtotal + $iva, 2);

        try {
            DB::beginTransaction();

            // Crear factura
            $factura = new Factura();
            $factura->FAC_FECHA = now();
            $factura->FAC_SUBTOTAL = $subtotal;
            $factura->FAC_IVA = $iva;
            $factura->FAC_MONTO_TOTAL = $total;
            $factura->FAC_ESTADO = 'PAG'; // Pagada
            $factura->ID_CARRITO = (int) time(); // ID único basado en timestamp
            $factura->CLI_ID = $cliente->CLI_ID;
            $factura->save();

            // Crear detalles de factura en PROXFAC
            foreach ($items as $productoId => $cantidad) {
                $producto = $productos->get((int) $productoId);
                if ($producto === null) {
                    continue;
                }

                $detalle = new Proxfac();
                $detalle->FAC_CODIGO = $factura->FAC_CODIGO;
                $detalle->PRD_CODIGO = $producto->PRD_CODIGO;
                $detalle->DET_FAC_CANTIDAD = (int) $cantidad;
                $detalle->DET_FAC_PRECIO_UNITARIO = (float) $producto->PRD_PRECIO;
                $detalle->ESTADO_PROXFAC = 'ACT'; // Activo
                $detalle->save();
            }

            // Descontar stock
            $applied = [];
            foreach ($items as $productoId => $cantidad) {
                $producto = $productos->get((int) $productoId);
                if ($producto === null) {
                    continue;
                }
                $codigo = trim((string) ($producto->PRD_CODIGO ?? ''));
                if ($codigo === '') {
                    continue;
                }

                $res = $this->bodegaService->adjustStock($codigo, -((int) $cantidad));
                if (! $res['ok']) {
                    // Revertir cambios de stock
                    foreach ($applied as $c => $q) {
                        $this->bodegaService->adjustStock((string) $c, (int) $q);
                    }

                    DB::rollBack();
                    return [
                        'ok' => false,
                        'message' => 'Revisa los campos marcados. Hay datos inválidos o incompletos.',
                    ];
                }

                $applied[$codigo] = ($applied[$codigo] ?? 0) + (int) $cantidad;
            }

            DB::commit();
            $this->vaciar();

            return [
                'ok' => true,
                'factura_id' => $factura->FAC_CODIGO,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'ok' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Obtener o crear un cliente para el usuario autenticado
     */
    private function obtenerOCrearCliente($user): ?Cliente
    {
        if ($user === null) {
            return null;
        }

        // Buscar cliente por correo del usuario
        $cliente = Cliente::where('CLI_CORREO', $user->email)->first();

        if ($cliente === null) {
            // Crear cliente si no existe
            $cliente = new Cliente();
            $cliente->CLI_CEDULA_RUC = $user->email; // Usar email como identificador temporal
            $cliente->CLI_NOMBRE = $user->name;
            $cliente->CLI_TELEFONO = '0000000000'; // Teléfono por defecto
            $cliente->CLI_CORREO = $user->email;
            $cliente->save();
        }

        return $cliente;
    }
}
