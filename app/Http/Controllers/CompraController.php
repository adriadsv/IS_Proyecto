<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompraRequest;
use App\Http\Requests\UpdateCompraRequest;
use App\Services\CompraService;
use App\Services\ProductoService;
use App\Services\ProveedorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompraController extends Controller
{
    public function __construct(
        private readonly CompraService $compraService,
        private readonly ProveedorService $proveedorService,
        private readonly ProductoService $productoService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = [];

        $compras = $this->compraService->paginate($filters);

        return view('compras.index', [
            'compras' => $compras,
            'filters' => $filters,
        ]);
    }

    public function consultaParametro(Request $request): View
    {
        $filters = [
            'proveedor_id' => $request->query('proveedor_id'),
            'fecha_desde' => $request->query('fecha_desde'),
            'fecha_hasta' => $request->query('fecha_hasta'),
            'numero_comprobante' => $request->query('numero_comprobante'),
            'tipo_comprobante' => $request->query('tipo_comprobante'),
        ];

        $compras = $this->compraService->paginate($filters);
        $proveedores = $this->proveedorService->listActive();

        return view('compras.consulta_parametro', [
            'compras' => $compras,
            'proveedores' => $proveedores,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('compras.create', [
            'proveedores' => $this->proveedorService->listActive(),
            'productos' => $this->productoService->listActive(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompraRequest $request): RedirectResponse
    {
        $result = $this->compraService->create($request->validated());

        if (! $result['ok']) {
            return back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('compras.index')
            ->with('success', 'La compra fue registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View|RedirectResponse
    {
        $compra = $this->compraService->find($id);

        if ($compra === null) {
            return redirect()
                ->route('compras.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('compras.show', [
            'compra' => $compra,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $compra = $this->compraService->find($id);

        if ($compra === null) {
            return redirect()
                ->route('compras.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('compras.edit', [
            'compra' => $compra,
            'proveedores' => $this->proveedorService->listActive(),
            'productos' => $this->productoService->listActive(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompraRequest $request, int $id): RedirectResponse
    {
        $result = $this->compraService->update($id, $request->validated());

        if (! $result['ok']) {
            return redirect()
                ->route('compras.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('compras.index')
            ->with('success', 'La compra fue actualizada correctamente.');
    }

    public function confirmDelete(int $id): View|RedirectResponse
    {
        $compra = $this->compraService->find($id);

        if ($compra === null) {
            return redirect()
                ->route('compras.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('compras.delete', [
            'compra' => $compra,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $result = $this->compraService->anular($id);

        if (! $result['ok']) {
            return redirect()
                ->route('compras.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('compras.index')
            ->with('success', 'La compra fue eliminada correctamente.');
    }
}
