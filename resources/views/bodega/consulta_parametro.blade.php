<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Consulta por parámetro') }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('bodega.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Consulta General') }}
                </a>

                <a href="{{ route('bodega.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    {{ __('Crear Producto en Bodega') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow sm:rounded-lg p-4">
                <form method="GET" action="{{ route('bodega.consulta_parametro') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div>
                        <x-input-label for="codigo" :value="__('Código')" />
                        <x-text-input id="codigo" name="codigo" type="text" class="mt-1 block w-full" value="{{ $filters['codigo'] ?? '' }}" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="nombre" :value="__('Nombre')" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" value="{{ $filters['nombre'] ?? '' }}" />
                    </div>

                    <div>
                        <x-input-label for="categoria" :value="__('Categoría')" />
                        <x-text-input id="categoria" name="categoria" type="text" class="mt-1 block w-full" value="{{ $filters['categoria'] ?? '' }}" />
                    </div>

                    <div>
                        <x-input-label for="estado" :value="__('Estado')" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Todos</option>
                            <option value="activo" @selected(($filters['estado'] ?? '') === 'activo')>activo</option>
                            <option value="inactivo" @selected(($filters['estado'] ?? '') === 'inactivo')>inactivo</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="stock_bajo_minimo" value="1" class="rounded border-gray-300" @checked(($filters['stock_bajo_minimo'] ?? '') === '1')>
                            {{ __('Stock bajo mínimo') }}
                        </label>
                    </div>

                    <div class="md:col-span-6 flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Buscar') }}</x-primary-button>
                        <a href="{{ route('bodega.consulta_parametro') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock mínimo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if (! $hasQuery)
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">0 resultados</td>
                                </tr>
                            @else
                                @forelse ($productos as $producto)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $producto['codigo'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $producto['nombre'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $producto['precio'] === null ? '-' : number_format((float) $producto['precio'], 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $producto['stock'] ?? 0 }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $producto['stock_minimo'] ?? 0 }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $producto['estado'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">0 resultados</td>
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
