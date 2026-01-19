<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modificar Compra') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('compras.update', $compra) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="proveedor_id" :value="__('Proveedor')" />
                            <select id="proveedor_id" name="proveedor_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Seleccione...</option>
                                @foreach ($proveedores as $prov)
                                    <option value="{{ $prov->id }}" @selected(old('proveedor_id', $compra->proveedor_id) == $prov->id)>{{ $prov->razon_social }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('proveedor_id')" />
                        </div>

                        <div>
                            <x-input-label for="fecha_compra" :value="__('Fecha')" />
                            <x-text-input id="fecha_compra" name="fecha_compra" type="date" class="mt-1 block w-full" value="{{ old('fecha_compra', $compra->fecha_compra?->format('Y-m-d')) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('fecha_compra')" />
                        </div>

                        <div>
                            <x-input-label for="tipo_comprobante" :value="__('Tipo')" />
                            <select id="tipo_comprobante" name="tipo_comprobante" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="factura" @selected(old('tipo_comprobante', $compra->tipo_comprobante) === 'factura')>Factura</option>
                                <option value="nota_venta" @selected(old('tipo_comprobante', $compra->tipo_comprobante) === 'nota_venta')>Nota de venta</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('tipo_comprobante')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="numero_comprobante" :value="__('Número de comprobante')" />
                            <x-text-input id="numero_comprobante" name="numero_comprobante" type="text" class="mt-1 block w-full" value="{{ old('numero_comprobante', $compra->numero_comprobante) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('numero_comprobante')" />
                        </div>
                    </div>

                    <div class="border rounded-md p-4">
                        <div class="font-semibold text-gray-800 mb-3">Detalle de productos</div>

                        @php
                            $oldProducto = old('detalle_producto_id');
                            $oldCantidad = old('detalle_cantidad');
                            $oldCosto = old('detalle_costo_unitario');
                            $useOld = is_array($oldProducto) && is_array($oldCantidad) && is_array($oldCosto);

                            $rows = [];
                            if ($useOld) {
                                $max = max(count($oldProducto), count($oldCantidad), count($oldCosto));
                                for ($i = 0; $i < $max; $i++) {
                                    $rows[] = [
                                        'producto_id' => $oldProducto[$i] ?? null,
                                        'cantidad' => $oldCantidad[$i] ?? null,
                                        'costo' => $oldCosto[$i] ?? null,
                                    ];
                                }
                            } else {
                                foreach ($compra->detalles as $d) {
                                    $rows[] = [
                                        'producto_id' => $d->producto_id,
                                        'cantidad' => $d->cantidad,
                                        'costo' => $d->costo_unitario,
                                    ];
                                }
                            }

                            for ($i = count($rows); $i < 5; $i++) {
                                $rows[] = ['producto_id' => null, 'cantidad' => null, 'costo' => null];
                            }
                        @endphp

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Costo unitario</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($rows as $row)
                                        <tr>
                                            <td class="px-3 py-2">
                                                <select name="detalle_producto_id[]" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    <option value="">Seleccione...</option>
                                                    @foreach ($productos as $prod)
                                                        <option value="{{ $prod->id }}" @selected($row['producto_id'] == $prod->id)>
                                                            {{ $prod->nombre }} ({{ $prod->codigo }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-3 py-2">
                                                <input name="detalle_cantidad[]" type="number" min="1" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="{{ $row['cantidad'] }}" />
                                            </td>
                                            <td class="px-3 py-2">
                                                <input name="detalle_costo_unitario[]" type="number" step="0.01" min="0.01" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="{{ $row['costo'] }}" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-2">
                            <x-input-error class="mt-2" :messages="$errors->get('detalle_producto_id')" />
                            <x-input-error class="mt-2" :messages="$errors->get('detalle_cantidad')" />
                            <x-input-error class="mt-2" :messages="$errors->get('detalle_costo_unitario')" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Guardar') }}</x-primary-button>
                        <a href="{{ route('compras.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
