<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modificar Producto') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm text-gray-600 mb-4">El stock se gestiona en Bodega.</div>
                <form method="POST" action="{{ route('productos.update', $producto) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="codigo" :value="__('Código')" />
                        <x-text-input id="codigo" name="codigo" type="text" class="mt-1 block w-full" value="{{ old('codigo', $producto->codigo) }}" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('codigo')" />
                    </div>

                    <div>
                        <x-input-label for="nombre" :value="__('Nombre')" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" value="{{ old('nombre', $producto->nombre) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" :value="__('Descripción')" />
                        <textarea id="descripcion" name="descripcion" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="precio" :value="__('Precio')" />
                        <x-text-input id="precio" name="precio" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('precio', $producto->precio) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('precio')" />
                    </div>

                    <div>
                        <x-input-label for="categoria" :value="__('Categoría')" />
                        <x-text-input id="categoria" name="categoria" type="text" class="mt-1 block w-full" value="{{ old('categoria', $producto->categoria) }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('categoria')" />
                    </div>

                    <div>
                        <x-input-label for="estado" :value="__('Estado')" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="activo" @selected(old('estado', $producto->estado) === 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado', $producto->estado) === 'inactivo')>Eliminado</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Guardar') }}</x-primary-button>
                        <a href="{{ route('productos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
