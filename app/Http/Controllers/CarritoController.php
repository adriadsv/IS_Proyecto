<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Repositories\BodegaProductoTxtRepository;
use App\Services\CarritoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarritoController extends Controller
{
    public function __construct(
        private readonly CarritoService $carritoService,
        private readonly BodegaProductoTxtRepository $bodegaRepo,
    ) {
    }

    public function index(): View
    {
        $items = $this->carritoService->items();
        $ids = array_map('intval', array_keys($items));

        $productos = Producto::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $bodegaIndex = [];
        foreach ($this->bodegaRepo->all() as $r) {
            if (! is_array($r)) {
                continue;
            }
            $codigo = trim((string) ($r['codigo'] ?? ''));
            if ($codigo !== '') {
                $bodegaIndex[$codigo] = $r;
            }
        }

        foreach ($productos as $p) {
            if (! $p instanceof Producto) {
                continue;
            }
            $codigo = trim((string) ($p->codigo ?? ''));
            $b = $codigo !== '' ? ($bodegaIndex[$codigo] ?? null) : null;
            $p->setAttribute('stock', (int) (($b['stock'] ?? 0) ?? 0));
        }

        $rows = [];
        $subtotal = 0.0;

        foreach ($items as $productoId => $cantidad) {
            $producto = $productos->get((int) $productoId);
            if ($producto === null) {
                continue;
            }

            $precio = (float) $producto->precio;
            $lineSubtotal = $precio * (int) $cantidad;
            $subtotal += $lineSubtotal;

            $rows[] = [
                'producto' => $producto,
                'cantidad' => (int) $cantidad,
                'subtotal' => $lineSubtotal,
            ];
        }

        $iva = round($subtotal * 0.15, 2);
        $total = round($subtotal + $iva, 2);

        return view('carrito.index', [
            'rows' => $rows,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total,
        ]);
    }

    public function agregar(Request $request, int $producto): RedirectResponse
    {
        $cantidad = (int) $request->input('cantidad', 1);
        $result = $this->carritoService->agregar($producto, $cantidad);

        if (! $result['ok']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('carrito.index');
    }

    public function quitarUno(int $producto): RedirectResponse
    {
        $this->carritoService->quitarUno($producto);

        return redirect()->route('carrito.index');
    }

    public function quitarProducto(int $producto): RedirectResponse
    {
        $this->carritoService->quitarProducto($producto);

        return redirect()->route('carrito.index');
    }

    public function pagar(): RedirectResponse
    {
        $result = $this->carritoService->pagar();

        if (! $result['ok']) {
            return redirect()->route('carrito.index')->with('error', $result['message']);
        }

        return redirect()->route('productos.index')->with('success', 'Pago exitoso.');
    }
}
