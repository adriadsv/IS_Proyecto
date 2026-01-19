<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modificar Producto en Bodega') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('bodega.update', $producto['codigo']) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="codigo" :value="__('Código')" />
                        <x-text-input id="codigo" name="codigo" type="text" class="mt-1 block w-full" value="{{ old('codigo', $producto['codigo'] ?? '') }}" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('codigo')" />
                    </div>

                    <div>
                        <x-input-label for="nombre" :value="__('Nombre')" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" value="{{ old('nombre', $producto['nombre'] ?? '') }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div>
                        <x-input-label for="categoria" :value="__('Categoría')" />
                        <x-text-input id="categoria" name="categoria" type="text" class="mt-1 block w-full" value="{{ old('categoria', $producto['categoria'] ?? '') }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('categoria')" />
                    </div>

                    <div>
                        <x-input-label for="unidad" :value="__('Unidad')" />
                        <x-text-input id="unidad" name="unidad" type="text" class="mt-1 block w-full" value="{{ old('unidad', $producto['unidad'] ?? '') }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('unidad')" />
                    </div>

                    <div>
                        <x-input-label for="stock_minimo" :value="__('Stock mínimo')" />
                        <x-text-input id="stock_minimo" name="stock_minimo" type="number" min="0" step="1" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full" value="{{ old('stock_minimo', $producto['stock_minimo'] ?? 0) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('stock_minimo')" />
                    </div>

                    <div>
                        <x-input-label for="ubicacion" :value="__('Ubicación')" />
                        <x-text-input id="ubicacion" name="ubicacion" type="text" class="mt-1 block w-full" value="{{ old('ubicacion', $producto['ubicacion'] ?? '') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('ubicacion')" />
                    </div>

                    <div>
                        <x-input-label for="estado" :value="__('Estado')" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="activo" @selected(old('estado', $producto['estado'] ?? 'activo') === 'activo')>activo</option>
                            <option value="inactivo" @selected(old('estado', $producto['estado'] ?? 'activo') === 'inactivo')>inactivo</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Guardar') }}</x-primary-button>
                        <a href="{{ route('bodega.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
