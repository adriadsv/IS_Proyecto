<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Cliente') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('clientes.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="identificacion" :value="__('Identificación')" />
                        <x-text-input id="identificacion" name="identificacion" type="text" class="mt-1 block w-full" value="{{ old('identificacion') }}" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('identificacion')" />
                    </div>

                    <div>
                        <x-input-label for="nombres" :value="__('Nombres')" />
                        <x-text-input id="nombres" name="nombres" type="text" class="mt-1 block w-full" value="{{ old('nombres') }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('nombres')" />
                    </div>

                    <div>
                        <x-input-label for="apellidos" :value="__('Apellidos')" />
                        <x-text-input id="apellidos" name="apellidos" type="text" class="mt-1 block w-full" value="{{ old('apellidos') }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('apellidos')" />
                    </div>

                    <div>
                        <x-input-label for="direccion" :value="__('Dirección')" />
                        <x-text-input id="direccion" name="direccion" type="text" class="mt-1 block w-full" value="{{ old('direccion') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('direccion')" />
                    </div>

                    <div>
                        <x-input-label for="telefono" :value="__('Teléfono')" />
                        <x-text-input id="telefono" name="telefono" type="tel" maxlength="10" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full" value="{{ old('telefono') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
                    </div>

                    <div>
                        <x-input-label for="correo" :value="__('Correo')" />
                        <x-text-input id="correo" name="correo" type="email" class="mt-1 block w-full" value="{{ old('correo') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('correo')" />
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Guardar') }}</x-primary-button>
                        <a href="{{ route('clientes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
