<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Services\ProveedorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function __construct(
        private readonly ProveedorService $proveedorService,
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

        $proveedores = $this->proveedorService->paginate($filters);

        return view('proveedores.index', [
            'proveedores' => $proveedores,
            'filters' => $filters,
        ]);
    }

    public function consultaParametro(Request $request): View
    {
        $filters = [
            'identificacion' => $request->query('identificacion'),
            'razon_social' => $request->query('razon_social'),
            'nombre_comercial' => $request->query('nombre_comercial'),
            'estado' => 'todos',
        ];

        $proveedores = $this->proveedorService->paginate($filters);

        return view('proveedores.consulta_parametro', [
            'proveedores' => $proveedores,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('proveedores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProveedorRequest $request): RedirectResponse
    {
        $result = $this->proveedorService->create($request->validated());

        if (! $result['ok']) {
            return back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'El proveedor fue creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View|RedirectResponse
    {
        $proveedor = $this->proveedorService->find($id);

        if ($proveedor === null) {
            return redirect()
                ->route('proveedores.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('proveedores.show', [
            'proveedor' => $proveedor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $proveedor = $this->proveedorService->find($id);

        if ($proveedor === null) {
            return redirect()
                ->route('proveedores.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('proveedores.edit', [
            'proveedor' => $proveedor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProveedorRequest $request, int $id): RedirectResponse
    {
        $result = $this->proveedorService->update($id, $request->validated());

        if (! $result['ok']) {
            return redirect()
                ->route('proveedores.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'El proveedor fue actualizado correctamente.');
    }

    public function confirmDelete(int $id): View|RedirectResponse
    {
        $proveedor = $this->proveedorService->find($id);

        if ($proveedor === null) {
            return redirect()
                ->route('proveedores.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('proveedores.delete', [
            'proveedor' => $proveedor,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $result = $this->proveedorService->inactivate($id);

        if (! $result['ok']) {
            return redirect()
                ->route('proveedores.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'La operación se realizó correctamente.');
    }
}
