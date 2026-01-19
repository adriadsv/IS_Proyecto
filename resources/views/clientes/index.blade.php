<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Clientes') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('clientes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Consulta General') }}
                </a>

                @if (auth()->user()?->isAdmin())
                    <a href="{{ route('clientes.consulta_parametro') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        {{ __('Consulta por parámetro') }}
                    </a>

                    <a href="{{ route('clientes.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        {{ __('Crear Cliente') }}
                    </a>
                @endif
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Identificación</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombres</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellidos</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($clientes as $cliente)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $cliente->identificacion }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $cliente->nombres }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $cliente->apellidos }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $cliente->correo ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $cliente->estado === 'inactivo' ? 'Eliminado' : 'Activo' }}</td>
                                    <td class="px-4 py-3 text-sm text-right whitespace-nowrap space-x-2">
                                        @if (auth()->user()?->isAdmin())
                                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-indigo-600 hover:text-indigo-900">Modificar Cliente</a>

                                            @if ($cliente->estado === 'activo')
                                                <a href="{{ route('clientes.confirm_delete', $cliente) }}" class="text-red-600 hover:text-red-900">Eliminar Cliente</a>
                                            @endif
                                        @else
                                            <a href="{{ route('clientes.show', $cliente) }}" class="text-indigo-600 hover:text-indigo-900">Consultar Cliente</a>
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

                <div class="p-4">
                    {{ $clientes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
