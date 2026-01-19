<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Consulta por parámetro') }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('compras.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Consulta General') }}
                </a>

                <a href="{{ route('compras.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Registrar Compra') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow sm:rounded-lg p-4">
                <form method="GET" action="{{ route('compras.consulta_parametro') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="proveedor_id" :value="__('Proveedor')" />
                            <select id="proveedor_id" name="proveedor_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Todos</option>
                                @foreach ($proveedores as $prov)
                                    <option value="{{ $prov->id }}" @selected(($filters['proveedor_id'] ?? '') == $prov->id)>
                                        {{ $prov->razon_social }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="tipo_comprobante" :value="__('Tipo de comprobante')" />
                            <select id="tipo_comprobante" name="tipo_comprobante" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Todos</option>
                                <option value="factura" @selected(($filters['tipo_comprobante'] ?? '') === 'factura')>Factura</option>
                                <option value="nota_venta" @selected(($filters['tipo_comprobante'] ?? '') === 'nota_venta')>Nota de venta</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="numero_comprobante" :value="__('Número de comprobante')" />
                            <x-text-input id="numero_comprobante" name="numero_comprobante" type="text" class="mt-1 block w-full" value="{{ $filters['numero_comprobante'] ?? '' }}" />
                        </div>

                        <div>
                            <x-input-label for="fecha_desde" :value="__('Desde')" />
                            <x-text-input id="fecha_desde" name="fecha_desde" type="date" class="mt-1 block w-full" value="{{ $filters['fecha_desde'] ?? '' }}" />
                        </div>

                        <div>
                            <x-input-label for="fecha_hasta" :value="__('Hasta')" />
                            <x-text-input id="fecha_hasta" name="fecha_hasta" type="date" class="mt-1 block w-full" value="{{ $filters['fecha_hasta'] ?? '' }}" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Buscar') }}</x-primary-button>
                        <a href="{{ route('compras.consulta_parametro') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            {{ __('Limpiar') }}
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comprobante</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($compras as $compra)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $compra->fecha_compra?->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $compra->proveedor->razon_social ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $compra->tipo_comprobante }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $compra->numero_comprobante }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format((float) $compra->total, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ ucfirst($compra->estado) }}</td>
                                    <td class="px-4 py-3 text-sm text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('compras.edit', $compra) }}" class="text-indigo-600 hover:text-indigo-900">Modificar Compra</a>

                                        @if ($compra->estado === 'registrada')
                                            <a href="{{ route('compras.confirm_delete', $compra) }}" class="text-red-600 hover:text-red-900">Eliminar Compra</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">0 resultados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $compras->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
