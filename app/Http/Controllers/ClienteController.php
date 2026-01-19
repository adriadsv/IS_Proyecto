<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Services\ClienteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function __construct(
        private readonly ClienteService $clienteService,
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

        $clientes = $this->clienteService->paginate($filters);

        return view('clientes.index', [
            'clientes' => $clientes,
            'filters' => $filters,
        ]);
    }

    public function consultaParametro(Request $request): View
    {
        $filters = [
            'identificacion' => $request->query('identificacion'),
            'nombre' => $request->query('nombre'),
            'correo' => $request->query('correo'),
            'estado' => 'todos',
        ];

        $clientes = $this->clienteService->paginate($filters);

        return view('clientes.consulta_parametro', [
            'clientes' => $clientes,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $result = $this->clienteService->create($request->validated());

        if (! $result['ok']) {
            return back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View|RedirectResponse
    {
        $cliente = $this->clienteService->find($id);

        if ($cliente === null) {
            return redirect()
                ->route('clientes.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('clientes.show', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $cliente = $this->clienteService->find($id);

        if ($cliente === null) {
            return redirect()
                ->route('clientes.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('clientes.edit', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClienteRequest $request, int $id): RedirectResponse
    {
        $result = $this->clienteService->update($id, $request->validated());

        if (! $result['ok']) {
            return redirect()
                ->route('clientes.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function confirmDelete(int $id): View|RedirectResponse
    {
        $cliente = $this->clienteService->find($id);

        if ($cliente === null) {
            return redirect()
                ->route('clientes.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('clientes.delete', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $result = $this->clienteService->inactivate($id);

        if (! $result['ok']) {
            return redirect()
                ->route('clientes.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
