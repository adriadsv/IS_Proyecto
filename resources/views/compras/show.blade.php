<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Consultar Compra') }}
            </h2>

            @if ($compra->estado === 'registrada')
                <a href="{{ route('compras.edit', $compra) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Modificar Compra') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Proveedor</div>
                        <div class="text-gray-900">{{ $compra->proveedor->razon_social ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Fecha</div>
                        <div class="text-gray-900">{{ $compra->fecha_compra?->format('Y-m-d') }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Estado</div>
                        <div class="text-gray-900">{{ ucfirst($compra->estado) }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Tipo</div>
                        <div class="text-gray-900">{{ $compra->tipo_comprobante }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Comprobante</div>
                        <div class="text-gray-900">{{ $compra->numero_comprobante }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Total</div>
                        <div class="text-gray-900">{{ number_format((float) $compra->total, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="p-4 font-semibold text-gray-800">Detalle</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo unitario</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($compra->detalles as $d)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $d->producto->nombre ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $d->cantidad }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format((float) $d->costo_unitario, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format((float) $d->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('compras.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Volver') }}
                </a>

                @if ($compra->estado === 'registrada')
                    <a href="{{ route('compras.confirm_delete', $compra) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        {{ __('Eliminar Compra') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
