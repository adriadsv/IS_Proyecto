<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Services\ProductoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function __construct(
        private readonly ProductoService $productoService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = [
            'estado' => 'todos',
        ];

        $productos = $this->productoService->paginate($filters);

        return view('productos.index', [
            'productos' => $productos,
            'filters' => $filters,
        ]);
    }

    public function consultaParametro(Request $request): View
    {
        $filters = [
            'codigo' => $request->query('codigo'),
            'nombre' => $request->query('nombre'),
            'categoria' => $request->query('categoria'),
            'estado' => 'todos',
        ];

        $productos = $this->productoService->paginate($filters);

        return view('productos.consulta_parametro', [
            'productos' => $productos,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('productos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductoRequest $request): RedirectResponse
    {
        $result = $this->productoService->create($request->validated());

        if (! $result['ok']) {
            return back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('productos.index')
            ->with('success', 'El producto fue creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View|RedirectResponse
    {
        $producto = $this->productoService->find($id);

        if ($producto === null) {
            return redirect()
                ->route('productos.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('productos.show', [
            'producto' => $producto,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $producto = $this->productoService->find($id);

        if ($producto === null) {
            return redirect()
                ->route('productos.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('productos.edit', [
            'producto' => $producto,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductoRequest $request, int $id): RedirectResponse
    {
        $result = $this->productoService->update($id, $request->validated());

        if (! $result['ok']) {
            return redirect()
                ->route('productos.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('productos.index')
            ->with('success', 'El producto fue actualizado correctamente.');
    }

    public function confirmDelete(int $id): View|RedirectResponse
    {
        $producto = $this->productoService->find($id);

        if ($producto === null) {
            return redirect()
                ->route('productos.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('productos.delete', [
            'producto' => $producto,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $result = $this->productoService->inactivate($id);

        if (! $result['ok']) {
            return redirect()
                ->route('productos.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('productos.index')
            ->with('success', 'La operación se realizó correctamente.');
    }
}
