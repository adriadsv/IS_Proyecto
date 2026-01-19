<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Services\BodegaProductoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class BodegaProductoController extends Controller
{
    public function __construct(
        private readonly BodegaProductoService $service,
    ) {
    }

    public function index(): View
    {
        $productos = $this->attachPrecios($this->service->all());

        return view('bodega.index', [
            'productos' => $productos,
        ]);
    }

    public function consultaParametro(Request $request): View
    {
        $filters = [
            'codigo' => $request->query('codigo'),
            'nombre' => $request->query('nombre'),
            'categoria' => $request->query('categoria'),
            'estado' => $request->query('estado'),
            'stock_bajo_minimo' => $request->query('stock_bajo_minimo'),
        ];

        $hasQuery = false;
        foreach ($filters as $v) {
            if ($v !== null && $v !== '') {
                $hasQuery = true;
                break;
            }
        }

        $productos = $hasQuery
            ? $this->service->filter([
                'codigo' => $filters['codigo'],
                'nombre' => $filters['nombre'],
                'categoria' => $filters['categoria'],
                'estado' => $filters['estado'],
                'stock_bajo_minimo' => $filters['stock_bajo_minimo'] === '1',
            ])
            : [];

        $productos = $this->attachPrecios($productos);

        return view('bodega.consulta_parametro', [
            'productos' => $productos,
            'filters' => $filters,
            'hasQuery' => $hasQuery,
        ]);
    }

    public function create(): View
    {
        return view('bodega.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo' => ['required', 'string'],
            'nombre' => ['required', 'string'],
            'categoria' => ['required', 'string'],
            'unidad' => ['required', 'string'],
            'stock_inicial' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'ubicacion' => ['nullable', 'string'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Revisa los campos marcados. Hay datos inválidos o incompletos.');
        }

        $result = $this->service->create($validator->validated());
        if (! $result['ok']) {
            return back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('bodega.index')
            ->with('success', 'El producto fue creado correctamente.');
    }

    public function show(string $codigo): View|RedirectResponse
    {
        $producto = $this->service->find($codigo);
        if ($producto === null) {
            return redirect()
                ->route('bodega.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        $p = Producto::query()->where('codigo', $codigo)->first();
        if ($p !== null) {
            $producto['precio'] = (float) ($p->precio ?? 0);
        }

        return view('bodega.show', [
            'producto' => $producto,
        ]);
    }

    public function edit(string $codigo): View|RedirectResponse
    {
        $producto = $this->service->find($codigo);
        if ($producto === null) {
            return redirect()
                ->route('bodega.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        $p = Producto::query()->where('codigo', $codigo)->first();
        if ($p !== null) {
            $producto['precio'] = (float) ($p->precio ?? 0);
        }

        return view('bodega.edit', [
            'producto' => $producto,
        ]);
    }

    public function update(Request $request, string $codigo): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo' => ['required', 'string'],
            'nombre' => ['required', 'string'],
            'categoria' => ['required', 'string'],
            'unidad' => ['required', 'string'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'ubicacion' => ['nullable', 'string'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Revisa los campos marcados. Hay datos inválidos o incompletos.');
        }

        $result = $this->service->update($codigo, $validator->validated());
        if (! $result['ok']) {
            return redirect()
                ->route('bodega.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('bodega.index')
            ->with('success', 'El producto fue actualizado correctamente.');
    }

    public function confirmDelete(string $codigo): View|RedirectResponse
    {
        $producto = $this->service->find($codigo);
        if ($producto === null) {
            return redirect()
                ->route('bodega.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('bodega.delete', [
            'producto' => $producto,
        ]);
    }

    public function destroy(string $codigo): RedirectResponse
    {
        $result = $this->service->inactivate($codigo);
        if (! $result['ok']) {
            return redirect()
                ->route('bodega.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('bodega.index')
            ->with('success', 'La operación se realizó correctamente.');
    }

    private function attachPrecios(array $productos): array
    {
        $codigos = [];
        foreach ($productos as $p) {
            if (! is_array($p)) {
                continue;
            }
            $c = trim((string) ($p['codigo'] ?? ''));
            if ($c !== '') {
                $codigos[] = $c;
            }
        }

        $precios = Producto::query()
            ->whereIn('codigo', array_values(array_unique($codigos)))
            ->get(['codigo', 'precio'])
            ->keyBy('codigo');

        foreach ($productos as &$p) {
            if (! is_array($p)) {
                continue;
            }
            $codigo = trim((string) ($p['codigo'] ?? ''));
            $prod = $codigo !== '' ? $precios->get($codigo) : null;
            $p['precio'] = $prod === null ? null : (float) ($prod->precio ?? 0);
        }
        unset($p);

        return $productos;
    }
}
