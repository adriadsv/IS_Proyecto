<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Consultar Proveedor') }}
            </h2>

            @if (auth()->user()?->isAdmin())
                <a href="{{ route('proveedores.edit', $proveedor) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Modificar Proveedor') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-2">
                <div class="text-sm text-gray-500">Identificación / RUC</div>
                <div class="text-gray-900">{{ $proveedor->identificacion }}</div>

                <div class="text-sm text-gray-500 mt-4">Razón social</div>
                <div class="text-gray-900">{{ $proveedor->razon_social }}</div>

                <div class="text-sm text-gray-500 mt-4">Nombre comercial</div>
                <div class="text-gray-900">{{ $proveedor->nombre_comercial ?? '-' }}</div>

                <div class="text-sm text-gray-500 mt-4">Dirección</div>
                <div class="text-gray-900">{{ $proveedor->direccion ?? '-' }}</div>

                <div class="text-sm text-gray-500 mt-4">Teléfono</div>
                <div class="text-gray-900">{{ $proveedor->telefono ?? '-' }}</div>

                <div class="text-sm text-gray-500 mt-4">Correo</div>
                <div class="text-gray-900">{{ $proveedor->correo ?? '-' }}</div>

                <div class="text-sm text-gray-500 mt-4">Estado</div>
                <div class="text-gray-900">{{ $proveedor->estado === 'inactivo' ? 'Eliminado' : 'Activo' }}</div>

                <div class="mt-6 flex items-center gap-2">
                    <a href="{{ route('proveedores.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        {{ __('Volver') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
