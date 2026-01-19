<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Consultar Producto') }}
            </h2>

            @if (auth()->user()?->isAdmin())
                <a href="{{ route('productos.edit', $producto) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Modificar Producto') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-2">
                <div class="text-sm text-gray-600">El stock se gestiona en Bodega.</div>
                <div class="text-sm text-gray-500">Código</div>
                <div class="text-gray-900">{{ $producto->codigo }}</div>

                <div class="text-sm text-gray-500 mt-4">Nombre</div>
                <div class="text-gray-900">{{ $producto->nombre }}</div>

                <div class="text-sm text-gray-500 mt-4">Descripción</div>
                <div class="text-gray-900">{{ $producto->descripcion ?? '-' }}</div>

                <div class="text-sm text-gray-500 mt-4">Precio</div>
                <div class="text-gray-900">{{ number_format((float) $producto->precio, 2) }}</div>

                <div class="text-sm text-gray-500 mt-4">Categoría</div>
                <div class="text-gray-900">{{ $producto->categoria ?? '-' }}</div>

                <div class="text-sm text-gray-500 mt-4">Stock</div>
                <div class="text-gray-900">{{ $producto->stock }}</div>

                <div class="text-sm text-gray-500 mt-4">Estado</div>
                <div class="text-gray-900">{{ $producto->estado === 'inactivo' ? 'Eliminado' : 'Activo' }}</div>

                <div class="mt-6 flex items-center gap-2">
                    @if (! auth()->user()?->isAdmin())
                        @if (strtolower((string) $producto->estado) === 'activo' && $producto->stock > 0)
                            <form method="POST" action="{{ route('carrito.agregar', $producto) }}" class="inline">
                                @csrf
                                <div class="inline-flex items-center gap-2">
                                    <x-input-label for="cantidad" :value="__('Cantidad')" />
                                    <input id="cantidad" name="cantidad" type="number" min="1" max="{{ $producto->stock }}" value="1" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-24" />

                                    <button type="submit" style="color:#ffffff;background:#4f46e5;border:1px solid transparent;padding:0.5rem 1rem;border-radius:0.375rem;font-weight:600;font-size:0.75rem;letter-spacing:0.08em;text-transform:uppercase;" class="inline-flex items-center">
                                        {{ __('Agregar al carrito') }}
                                    </button>
                                </div>
                            </form>
                        @endif

                        <a href="{{ route('carrito.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            {{ __('Carrito') }}
                        </a>
                    @endif

                    <a href="{{ route('productos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        {{ __('Volver') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
