<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modificar Proveedor') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('proveedores.update', $proveedor) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="identificacion" :value="__('Identificación / RUC')" />
                        <x-text-input id="identificacion" name="identificacion" type="text" class="mt-1 block w-full" value="{{ old('identificacion', $proveedor->identificacion) }}" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('identificacion')" />
                    </div>

                    <div>
                        <x-input-label for="razon_social" :value="__('Razón social')" />
                        <x-text-input id="razon_social" name="razon_social" type="text" class="mt-1 block w-full" value="{{ old('razon_social', $proveedor->razon_social) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('razon_social')" />
                    </div>

                    <div>
                        <x-input-label for="nombre_comercial" :value="__('Nombre comercial')" />
                        <x-text-input id="nombre_comercial" name="nombre_comercial" type="text" class="mt-1 block w-full" value="{{ old('nombre_comercial', $proveedor->nombre_comercial) }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre_comercial')" />
                    </div>

                    <div>
                        <x-input-label for="direccion" :value="__('Dirección')" />
                        <x-text-input id="direccion" name="direccion" type="text" class="mt-1 block w-full" value="{{ old('direccion', $proveedor->direccion) }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('direccion')" />
                    </div>

                    <div>
                        <x-input-label for="telefono" :value="__('Teléfono')" />
                        <x-text-input id="telefono" name="telefono" type="tel" maxlength="10" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full" value="{{ old('telefono', $proveedor->telefono) }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
                    </div>

                    <div>
                        <x-input-label for="correo" :value="__('Correo')" />
                        <x-text-input id="correo" name="correo" type="email" class="mt-1 block w-full" value="{{ old('correo', $proveedor->correo) }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('correo')" />
                    </div>

                    <div>
                        <x-input-label for="estado" :value="__('Estado')" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="activo" @selected(old('estado', $proveedor->estado) === 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado', $proveedor->estado) === 'inactivo')>Eliminado</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Guardar') }}</x-primary-button>
                        <a href="{{ route('proveedores.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
