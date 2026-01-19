<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Anular Factura') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <div class="text-sm text-gray-700">
                    {{ __('¿Seguro que deseas anular esta factura?') }}
                </div>

                <div class="text-sm text-gray-900">
                    <div><span class="font-semibold">Número:</span> {{ $factura['numero'] ?? '-' }}</div>
                    <div><span class="font-semibold">Cliente:</span> {{ $factura['cliente'] ?? '-' }}</div>
                    <div><span class="font-semibold">Total:</span> {{ number_format((float) ($factura['total'] ?? 0), 2) }}</div>
                </div>

                <form method="POST" action="{{ route('facturas.anular', $factura['numero']) }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="fecha_anulacion" :value="__('Fecha anulación')" />
                        <x-text-input id="fecha_anulacion" name="fecha_anulacion" type="date" class="mt-1 block w-full" value="{{ old('fecha_anulacion') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_anulacion')" />
                    </div>

                    <div>
                        <x-input-label for="motivo_anulacion" :value="__('Motivo anulación')" />
                        <x-text-input id="motivo_anulacion" name="motivo_anulacion" type="text" class="mt-1 block w-full" value="{{ old('motivo_anulacion') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('motivo_anulacion')" />
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Confirmar') }}</x-primary-button>
                        <a href="{{ route('facturas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
