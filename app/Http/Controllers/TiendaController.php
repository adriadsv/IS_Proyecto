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

        $query = Producto::query()->with('bodegas');

        if ($categoriaFiltro) {
            $query->where('CAT_CODIGO', $categoriaFiltro);
        }

        $productos = $query->get();

        foreach ($productos as $p) {
            // Calcular total de stock de todas las bodegas
            $totalStock = $p->bodegas->sum('pivot.DET_BOD_CANTIDAD');
            $p->setAttribute('stock', (int) $totalStock);

            // Estado basado en si tiene stock (o lógica de negocio simple)
            $p->setAttribute('estado', $totalStock > 0 ? 'activo' : 'inactivo');
        }

        // Filtrar solo productos con stock
        $productosDisponibles = $productos->filter(function ($p) {
            return (int) ($p->stock ?? 0) > 0;
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
        $producto = Producto::where('PRD_CODIGO', $codigo)->with('bodegas')->firstOrFail();

        // Obtener stock de bodega (DB)
        $totalStock = $producto->bodegas->sum('pivot.DET_BOD_CANTIDAD');
        $producto->setAttribute('stock', (int) $totalStock);
        $producto->setAttribute('estado', $totalStock > 0 ? 'activo' : 'inactivo');

        // Cargar categoría
        $categoria = $producto->categoria;

        return view('tienda.show', [
            'producto' => $producto,
            'categoria' => $categoria,
        ]);
    }
}
