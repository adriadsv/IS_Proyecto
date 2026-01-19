<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Consulta por parámetro') }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('proveedores.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Consulta General') }}
                </a>

                <a href="{{ route('proveedores.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Crear Proveedor') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow sm:rounded-lg p-4">
                <form method="GET" action="{{ route('proveedores.consulta_parametro') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="identificacion" :value="__('Identificación/RUC')" />
                            <x-text-input id="identificacion" name="identificacion" type="text" class="mt-1 block w-full" value="{{ $filters['identificacion'] ?? '' }}" />
                        </div>

                        <div>
                            <x-input-label for="razon_social" :value="__('Razón social')" />
                            <x-text-input id="razon_social" name="razon_social" type="text" class="mt-1 block w-full" value="{{ $filters['razon_social'] ?? '' }}" />
                        </div>

                        <div>
                            <x-input-label for="nombre_comercial" :value="__('Nombre comercial')" />
                            <x-text-input id="nombre_comercial" name="nombre_comercial" type="text" class="mt-1 block w-full" value="{{ $filters['nombre_comercial'] ?? '' }}" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Buscar') }}</x-primary-button>
                        <a href="{{ route('proveedores.consulta_parametro') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Identificación/RUC</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Razón social</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($proveedores as $proveedor)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $proveedor->identificacion }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $proveedor->razon_social }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $proveedor->correo ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $proveedor->estado === 'inactivo' ? 'Eliminado' : 'Activo' }}</td>
                                    <td class="px-4 py-3 text-sm text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('proveedores.edit', $proveedor) }}" class="text-indigo-600 hover:text-indigo-900">Modificar Proveedor</a>

                                        @if ($proveedor->estado === 'activo')
                                            <a href="{{ route('proveedores.confirm_delete', $proveedor) }}" class="text-red-600 hover:text-red-900">Eliminar Proveedor</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">0 resultados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $proveedores->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
