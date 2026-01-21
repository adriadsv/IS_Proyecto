<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Carrito') }}
            </h2>

            <a href="{{ route('productos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                {{ __('Volver') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($rows as $row)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $row['producto']->PRD_DESCRIPCION ?? $row['producto']->nombre ?? 'Sin nombre' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ number_format((float) ($row['producto']->PRD_PRECIO ?? $row['producto']->precio ?? 0), 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $row['cantidad'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ number_format((float) $row['subtotal'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right whitespace-nowrap space-x-2">
                                        @php
                                            $stock = (int) ($row['producto']->stock ?? 0);
                                            $estado = (string) ($row['producto']->estado ?? 'inactivo');
                                        @endphp
                                        @if ($stock > $row['cantidad'] && strtolower($estado) === 'activo')
                                            <form method="POST" action="{{ route('carrito.agregar', $row['producto']) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="cantidad" value="1">
                                                <button type="submit" class="text-indigo-600 hover:text-indigo-900">Añadir 1</button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('carrito.quitar_uno', $row['producto']) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900">Quitar 1</button>
                                        </form>

                                        <form method="POST" action="{{ route('carrito.quitar_producto', $row['producto']) }}" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar este registro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Quitar todo el producto</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">0 resultados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    <div class="flex items-center justify-between gap-4">
                        <div class="text-sm text-gray-900 font-semibold">
                            <div>Subtotal: {{ number_format((float) $subtotal, 2) }}</div>
                            <div>IVA 15%: {{ number_format((float) $iva, 2) }}</div>
                            <div>Total: {{ number_format((float) $total, 2) }}</div>
                        </div>

                        <form method="POST" action="{{ route('carrito.pagar') }}">
                            @csrf
                            <button type="submit" style="color:#ffffff;background:#4f46e5;border:1px solid transparent;padding:0.5rem 1rem;border-radius:0.375rem;font-weight:600;font-size:0.75rem;letter-spacing:0.08em;text-transform:uppercase;" class="inline-flex items-center" {{ count($rows) === 0 ? 'disabled' : '' }}>
                                PAGAR {{ number_format((float) $total, 2) }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
