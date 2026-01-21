<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Repositories\BodegaProductoTxtRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TiendaController extends Controller
{
    public function __construct(
        private readonly BodegaProductoTxtRepository $bodegaRepo,
    ) {
    }

    /**
     * Mostrar catálogo público de productos
     */
    public function index(Request $request): View
    {
        $categoriaFiltro = $request->input('categoria');

        $query = Producto::query();

        if ($categoriaFiltro) {
            $query->where('CAT_CODIGO', $categoriaFiltro);
        }

        $productos = $query->get();

        // Obtener stock de bodega para cada producto
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
            // Compatibilidad con ambos esquemas
            $codigo = trim((string) ($p->PRD_CODIGO ?? $p->codigo ?? ''));
            $b = $codigo !== '' ? ($bodegaIndex[$codigo] ?? null) : null;
            $p->setAttribute('stock', (int) (($b['stock'] ?? 0) ?? 0));
            $p->setAttribute('estado', (string) ($b['estado'] ?? 'inactivo'));
        }

        // Filtrar solo productos activos con stock
        $productosDisponibles = $productos->filter(function ($p) {
            return strtolower((string) ($p->estado ?? '')) === 'activo' && (int) ($p->stock ?? 0) > 0;
        });

        $categorias = Categoria::all();

        return view('tienda.index', [
            'productos' => $productosDisponibles,
            'categorias' => $categorias,
            'categoriaFiltro' => $categoriaFiltro,
        ]);
    }

    /**
     * Mostrar detalle de un producto
     */
    public function show(string $codigo): View
    {
        $producto = Producto::where('PRD_CODIGO', $codigo)->firstOrFail();

        // Obtener stock de bodega
        $codigo = trim((string) ($producto->PRD_CODIGO ?? $producto->codigo ?? ''));
        $bodega = $codigo === '' ? null : $this->bodegaRepo->findByCodigo($codigo);
        $producto->setAttribute('stock', (int) ($bodega['stock'] ?? 0));
        $producto->setAttribute('estado', (string) ($bodega['estado'] ?? 'inactivo'));

        // Cargar categoría
        $categoria = $producto->categoria;

        return view('tienda.show', [
            'producto' => $producto,
            'categoria' => $categoria,
        ]);
    }
}
