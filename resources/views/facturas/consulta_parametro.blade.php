<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Consulta por parámetro') }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('facturas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Consulta General') }}
                </a>

                <a href="{{ route('facturas.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Crear Factura') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow sm:rounded-lg p-4">
                <form method="GET" action="{{ route('facturas.consulta_parametro') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div>
                        <x-input-label for="numero" :value="__('Número')" />
                        <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full" value="{{ $filters['numero'] ?? '' }}" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="cliente" :value="__('Cliente')" />
                        <x-text-input id="cliente" name="cliente" type="text" class="mt-1 block w-full" value="{{ $filters['cliente'] ?? '' }}" />
                    </div>

                    <div>
                        <x-input-label for="fecha_desde" :value="__('Desde')" />
                        <x-text-input id="fecha_desde" name="fecha_desde" type="date" class="mt-1 block w-full" value="{{ $filters['fecha_desde'] ?? '' }}" />
                    </div>

                    <div>
                        <x-input-label for="fecha_hasta" :value="__('Hasta')" />
                        <x-text-input id="fecha_hasta" name="fecha_hasta" type="date" class="mt-1 block w-full" value="{{ $filters['fecha_hasta'] ?? '' }}" />
                    </div>

                    <div>
                        <x-input-label for="estado" :value="__('Estado')" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Todos</option>
                            <option value="Emitida" @selected(($filters['estado'] ?? '') === 'Emitida')>Emitida</option>
                            <option value="Anulada" @selected(($filters['estado'] ?? '') === 'Anulada')>Anulada</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="metodo_pago" :value="__('Método de pago')" />
                        <x-text-input id="metodo_pago" name="metodo_pago" type="text" class="mt-1 block w-full" value="{{ $filters['metodo_pago'] ?? '' }}" />
                    </div>

                    <div class="md:col-span-4"></div>

                    <div class="md:col-span-6 flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Buscar') }}</x-primary-button>
                        <a href="{{ route('facturas.consulta_parametro') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if (! $hasQuery)
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">0 resultados</td>
                                </tr>
                            @else
                                @forelse ($facturas as $factura)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $factura['numero'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $factura['fecha'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $factura['cliente'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ number_format((float) ($factura['total'] ?? 0), 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $factura['estado'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">0 resultados</td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
