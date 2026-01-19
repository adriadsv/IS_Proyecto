<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Compras') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('compras.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Consulta General') }}
                </a>

                @if (auth()->user()?->isAdmin())
                    <a href="{{ route('compras.consulta_parametro') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        {{ __('Consulta por parámetro') }}
                    </a>

                    <a href="{{ route('compras.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        {{ __('Registrar Compra') }}
                    </a>
                @endif
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
                                        @if (auth()->user()?->isAdmin())
                                            <a href="{{ route('compras.edit', $compra) }}" class="text-indigo-600 hover:text-indigo-900">Modificar Compra</a>

                                            @if ($compra->estado === 'registrada')
                                                <a href="{{ route('compras.confirm_delete', $compra) }}" class="text-red-600 hover:text-red-900">Eliminar Compra</a>
                                            @endif
                                        @else
                                            <a href="{{ route('compras.show', $compra) }}" class="text-indigo-600 hover:text-indigo-900">Consultar Compra</a>
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
