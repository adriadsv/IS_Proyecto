<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\FacturaTxtService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class FacturaController extends Controller
{
    public function __construct(
        private readonly FacturaTxtService $service,
    ) {
    }

    public function index(): View
    {
        $facturas = $this->service->all();

        return view('facturas.index', [
            'facturas' => $facturas,
        ]);
    }

    public function consultaParametro(Request $request): View
    {
        $filters = [
            'numero' => $request->query('numero'),
            'cliente' => $request->query('cliente'),
            'fecha_desde' => $request->query('fecha_desde'),
            'fecha_hasta' => $request->query('fecha_hasta'),
            'estado' => $request->query('estado'),
            'metodo_pago' => $request->query('metodo_pago'),
        ];

        $hasQuery = false;
        foreach ($filters as $v) {
            if ($v !== null && $v !== '') {
                $hasQuery = true;
                break;
            }
        }

        $facturas = $hasQuery ? $this->service->filter($filters) : [];

        return view('facturas.consulta_parametro', [
            'facturas' => $facturas,
            'filters' => $filters,
            'hasQuery' => $hasQuery,
        ]);
    }

    public function create(): View
    {
        return view('facturas.create', [
            'productos' => $this->service->listProductosActivos(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'cliente' => ['required', 'string'],
            'fecha' => ['required', 'date'],
            'tipo_numero_comprobante' => ['required', 'string'],
            'metodo_pago' => ['required', 'in:Efectivo,Tarjeta,Transferencia'],
            'detalle_producto_codigo' => ['array'],
            'detalle_producto_codigo.*' => ['nullable', 'string'],
            'detalle_cantidad' => ['array'],
            'detalle_cantidad.*' => ['nullable', 'integer', 'min:1'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'observacion' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Revisa los campos marcados. Hay datos inválidos o incompletos.');
        }

        $payload = $validator->validated() + $request->only([
            'detalle_producto_codigo',
            'detalle_cantidad',
        ]);

        $result = $this->service->create($payload);
        if (! $result['ok']) {
            return back()
                ->withInput()
                ->with('error', $result['message']);
        }

        $numero = (int) ($result['factura']['numero'] ?? 0);

        return redirect()
            ->route('facturas.index')
            ->with('success', 'La factura fue emitida correctamente. Número: '.$numero.'.');
    }

    public function show(int $numero): View|RedirectResponse
    {
        $factura = $this->service->find($numero);
        if ($factura === null) {
            return redirect()
                ->route('facturas.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('facturas.show', [
            'factura' => $factura,
        ]);
    }

    public function edit(int $numero): View|RedirectResponse
    {
        $factura = $this->service->find($numero);
        if ($factura === null) {
            return redirect()
                ->route('facturas.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('facturas.edit', [
            'factura' => $factura,
            'productos' => $this->service->listProductosParaEdicion($factura),
        ]);
    }

    public function update(Request $request, int $numero): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'cliente' => ['required', 'string'],
            'fecha' => ['required', 'date'],
            'tipo_numero_comprobante' => ['required', 'string'],
            'metodo_pago' => ['required', 'in:Efectivo,Tarjeta,Transferencia'],
            'detalle_producto_codigo' => ['array'],
            'detalle_producto_codigo.*' => ['nullable', 'string'],
            'detalle_cantidad' => ['array'],
            'detalle_cantidad.*' => ['nullable', 'integer', 'min:1'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'observacion' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Revisa los campos marcados. Hay datos inválidos o incompletos.');
        }

        $payload = $validator->validated() + $request->only([
            'detalle_producto_codigo',
            'detalle_cantidad',
        ]);

        $result = $this->service->update($numero, $payload);
        if (! $result['ok']) {
            return redirect()
                ->route('facturas.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('facturas.index')
            ->with('success', 'La factura fue actualizada correctamente.');
    }

    public function confirmAnular(int $numero): View|RedirectResponse
    {
        $factura = $this->service->find($numero);
        if ($factura === null) {
            return redirect()
                ->route('facturas.index')
                ->with('error', 'No se encontró el registro solicitado.');
        }

        return view('facturas.anular', [
            'factura' => $factura,
        ]);
    }

    public function anular(Request $request, int $numero): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_anulacion' => ['nullable', 'date'],
            'motivo_anulacion' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Revisa los campos marcados. Hay datos inválidos o incompletos.');
        }

        $data = $validator->validated();

        $result = $this->service->anular(
            $numero,
            $data['motivo_anulacion'] ?? null,
            $data['fecha_anulacion'] ?? null,
        );

        if (! $result['ok']) {
            return redirect()
                ->route('facturas.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('facturas.index')
            ->with('success', 'La factura fue anulada correctamente.');
    }
}
