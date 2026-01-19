<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Consultar Factura') }}
        </h2>
    </x-slot>

    @php
        $detalle = is_array($factura['detalle'] ?? null) ? $factura['detalle'] : [];
    @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                    <div><span class="font-semibold">Número:</span> {{ $factura['numero'] ?? '-' }}</div>
                    <div><span class="font-semibold">Fecha:</span> {{ $factura['fecha'] ?? '-' }}</div>
                    <div><span class="font-semibold">Cliente:</span> {{ $factura['cliente'] ?? '-' }}</div>
                    <div><span class="font-semibold">Estado:</span> {{ $factura['estado'] ?? '-' }}</div>
                    <div><span class="font-semibold">Método de pago:</span> {{ $factura['metodo_pago'] ?? '-' }}</div>
                    <div><span class="font-semibold">Tipo/Número comprobante:</span> {{ $factura['tipo_numero_comprobante'] ?? '-' }}</div>
                </div>

                <div>
                    <div class="font-semibold text-gray-800">Detalle</div>
                    <div class="overflow-x-auto mt-2">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($detalle as $item)
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $item['producto_codigo'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $item['cantidad'] ?? 0 }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ number_format((float) ($item['precio'] ?? 0), 2) }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ number_format((float) ($item['subtotal'] ?? 0), 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">0 resultados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-2 text-sm">
                    <div><span class="font-semibold">Subtotal:</span> {{ number_format((float) ($factura['subtotal'] ?? 0), 2) }}</div>
                    <div><span class="font-semibold">Descuento:</span> {{ number_format((float) ($factura['descuento'] ?? 0), 2) }}</div>
                    <div><span class="font-semibold">Impuesto:</span> {{ number_format((float) ($factura['impuesto'] ?? 0), 2) }}</div>
                    <div><span class="font-semibold">Total:</span> {{ number_format((float) ($factura['total'] ?? 0), 2) }}</div>
                </div>

                @if (($factura['estado'] ?? '') === 'Anulada')
                    <div class="text-sm">
                        <div><span class="font-semibold">Fecha anulación:</span> {{ $factura['fecha_anulacion'] ?? '-' }}</div>
                        <div><span class="font-semibold">Motivo anulación:</span> {{ $factura['motivo_anulacion'] ?? '-' }}</div>
                    </div>
                @endif

                <div class="pt-2 flex items-center gap-2">
                    <a href="{{ route('facturas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        {{ __('Volver') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
