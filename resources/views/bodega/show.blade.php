<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Consultar Producto en Bodega') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-2">
                <div><span class="font-semibold">Código:</span> {{ $producto['codigo'] ?? '-' }}</div>
                <div><span class="font-semibold">Nombre:</span> {{ $producto['nombre'] ?? '-' }}</div>
                <div><span class="font-semibold">Categoría:</span> {{ $producto['categoria'] ?? '-' }}</div>
                <div><span class="font-semibold">Unidad:</span> {{ $producto['unidad'] ?? '-' }}</div>
                <div><span class="font-semibold">Stock inicial:</span> {{ $producto['stock_inicial'] ?? 0 }}</div>
                <div><span class="font-semibold">Stock:</span> {{ $producto['stock'] ?? 0 }}</div>
                <div><span class="font-semibold">Stock mínimo:</span> {{ $producto['stock_minimo'] ?? 0 }}</div>
                <div><span class="font-semibold">Precio:</span> {{ $producto['precio'] ?? '-' }}</div>
                <div><span class="font-semibold">Ubicación:</span> {{ $producto['ubicacion'] ?? '-' }}</div>
                <div><span class="font-semibold">Estado:</span> {{ $producto['estado'] ?? '-' }}</div>

                <div class="pt-4">
                    <a href="{{ route('bodega.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        {{ __('Volver') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
