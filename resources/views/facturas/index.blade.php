<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestionar Facturas') }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('facturas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Consulta General') }}
                </a>

                <a href="{{ route('facturas.consulta_parametro') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Consulta por parámetro') }}
                </a>

                <a href="{{ route('facturas.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Crear Factura') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
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
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($facturas as $factura)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $factura['numero'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $factura['fecha'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $factura['cliente'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format((float) ($factura['total'] ?? 0), 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $factura['estado'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('facturas.show', $factura['numero']) }}" class="text-indigo-600 hover:text-indigo-900">Consultar Factura</a>
                                        @if (($factura['estado'] ?? '') === 'Emitida')
                                            <a href="{{ route('facturas.edit', $factura['numero']) }}" class="text-indigo-600 hover:text-indigo-900">Modificar Factura</a>
                                            <a href="{{ route('facturas.confirm_anular', $factura['numero']) }}" class="text-red-600 hover:text-red-900">Anular Factura</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">0 resultados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
