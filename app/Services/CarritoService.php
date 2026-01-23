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
     * @return array<string,int> producto_id => cantidad
     */
    public function items(): array
    {
        /** @var array<string,int> $items */
        $items = session()->get(self::SESSION_KEY, []);

        return $items;
    }

    public function cantidadTotal(): int
    {
        return array_sum($this->items());
    }

    public function agregar(string $productoId, int $cantidad = 1): array
    {
        $cantidad = max(1, $cantidad);

        $producto = Producto::query()->with('bodegas')->find($productoId);
        if ($producto === null) {
            return [
                'ok' => false,
                'message' => 'No se encontró el registro solicitado.',
            ];
        }

        $stock = (int) $producto->bodegas->sum('pivot.DET_BOD_CANTIDAD');

        if (!$this->isProductoActivo($producto) || $stock <= 0) {
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

    public function quitarUno(string $productoId): void
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

    public function quitarProducto(string $productoId): void
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

        $ids = array_keys($items);

        $productos = Producto::query()
            ->with(['bodegas']) // Load bodegas for stock check
            ->whereKey($ids)
            ->get()
            ->keyBy(function ($item) {
                return (string) $item->getKey();
            });

        // Validar productos y stock
        foreach ($items as $productoId => $cantidad) {
            $producto = $productos->get((string) $productoId); // Explicitly cast key to string
            if ($producto === null) {
                return [
                    'ok' => false,
                    'message' => 'No se encontró el registro solicitado.',
                ];
            }

            // Calculate stock from database
            $stock = (int) $producto->bodegas->sum('pivot.DET_BOD_CANTIDAD');
            // Assuming 'activo' if stock > 0, or check another flag if needed.
            // Using logic from TiendaController: implicit check.

            if ($stock < (int) $cantidad) {
                return [
                    'ok' => false,
                    'message' => 'Revisa los campos marcados. Hay datos inválidos o incompletos.',
                ];
            }
        }

        // Calcular totales
        $subtotal = 0.0;
        foreach ($items as $productoId => $cantidad) {
            $producto = $productos->get((string) $productoId);
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
                $producto = $productos->get((string) $productoId);
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

            // Descontar stock (Database Update)
            foreach ($items as $productoId => $cantidad) {
                $producto = $productos->get((string) $productoId);
                if ($producto === null) {
                    continue;
                }

                // Simple logic: Decrement from first bodega with available stock
                $qtyToDeduct = (int) $cantidad;

                foreach ($producto->bodegas as $bodega) {
                    if ($qtyToDeduct <= 0)
                        break;

                    $currentStock = $bodega->pivot->DET_BOD_CANTIDAD;
                    $deduct = min($currentStock, $qtyToDeduct);

                    if ($deduct > 0) {
                        // Update pivot
                        $producto->bodegas()->updateExistingPivot($bodega->BOD_CODIGO, [
                            'DET_BOD_CANTIDAD' => $currentStock - $deduct
                        ]);
                        $qtyToDeduct -= $deduct;
                    }
                }

                if ($qtyToDeduct > 0) {
                    throw new \Exception("Stock inconsistency during checkout for " . $producto->PRD_CODIGO);
                }
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
