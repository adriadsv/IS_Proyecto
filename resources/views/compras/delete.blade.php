<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Eliminar Compra') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <div class="text-sm text-gray-700">
                    {{ __('¿Seguro que deseas eliminar la compra seleccionada?') }}
                </div>

                <div class="text-sm text-gray-900 space-y-1">
                    <div><span class="font-semibold">Proveedor:</span> {{ $compra->proveedor->razon_social ?? '-' }}</div>
                    <div><span class="font-semibold">Fecha:</span> {{ $compra->fecha_compra?->format('Y-m-d') }}</div>
                    <div><span class="font-semibold">Número de comprobante:</span> {{ $compra->numero_comprobante }}</div>
                </div>

                <form method="POST" action="{{ route('compras.destroy', $compra) }}" class="flex items-center gap-2">
                    @csrf
                    @method('DELETE')

                    <x-primary-button type="submit">{{ __('Confirmar') }}</x-primary-button>
                    <a href="{{ route('compras.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        {{ __('Cancelar') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
