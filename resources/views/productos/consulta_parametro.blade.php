<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Consulta por parámetro') }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('productos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Consulta General') }}
                </a>

                <a href="{{ route('productos.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Crear Producto') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow sm:rounded-lg p-4">
                <form method="GET" action="{{ route('productos.consulta_parametro') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="codigo" :value="__('Código')" />
                            <x-text-input id="codigo" name="codigo" type="text" class="mt-1 block w-full" value="{{ $filters['codigo'] ?? '' }}" />
                        </div>

                        <div>
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" value="{{ $filters['nombre'] ?? '' }}" />
                        </div>

                        <div>
                            <x-input-label for="categoria" :value="__('Categoría')" />
                            <x-text-input id="categoria" name="categoria" type="text" class="mt-1 block w-full" value="{{ $filters['categoria'] ?? '' }}" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Buscar') }}</x-primary-button>
                        <a href="{{ route('productos.consulta_parametro') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($productos as $producto)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $producto->codigo }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $producto->nombre }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $producto->categoria ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format((float) $producto->precio, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $producto->stock }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $producto->estado === 'inactivo' ? 'Eliminado' : 'Activo' }}</td>
                                    <td class="px-4 py-3 text-sm text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('productos.edit', $producto) }}" class="text-indigo-600 hover:text-indigo-900">Modificar Producto</a>

                                        @if ($producto->estado === 'activo')
                                            <a href="{{ route('productos.confirm_delete', $producto) }}" class="text-red-600 hover:text-red-900">Eliminar Producto</a>
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
                    {{ $productos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
