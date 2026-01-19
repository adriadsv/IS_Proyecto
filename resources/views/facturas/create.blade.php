<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Factura') }}
        </h2>
    </x-slot>

    @php
        $oldProductos = old('detalle_producto_codigo', ['']);
        $oldCantidades = old('detalle_cantidad', ['']);
        $oldPrecios = old('detalle_precio', ['']);
        $rows = max(count($oldProductos), count($oldCantidades), count($oldPrecios), 1);
    @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('facturas.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="cliente" :value="__('Cliente')" />
                            <x-text-input id="cliente" name="cliente" type="text" class="mt-1 block w-full" value="{{ old('cliente') }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('cliente')" />
                        </div>

                        <div>
                            <x-input-label for="fecha" :value="__('Fecha')" />
                            <x-text-input id="fecha" name="fecha" type="date" class="mt-1 block w-full" value="{{ old('fecha') }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('fecha')" />
                        </div>

                        <div>
                            <x-input-label for="tipo_numero_comprobante" :value="__('Tipo/Número comprobante')" />
                            <x-text-input id="tipo_numero_comprobante" name="tipo_numero_comprobante" type="text" class="mt-1 block w-full" value="{{ old('tipo_numero_comprobante') }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('tipo_numero_comprobante')" />
                        </div>

                        <div>
                            <x-input-label for="metodo_pago" :value="__('Método de pago')" />
                            <select id="metodo_pago" name="metodo_pago" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-</option>
                                <option value="Efectivo" @selected(old('metodo_pago') === 'Efectivo')>Efectivo</option>
                                <option value="Tarjeta" @selected(old('metodo_pago') === 'Tarjeta')>Tarjeta</option>
                                <option value="Transferencia" @selected(old('metodo_pago') === 'Transferencia')>Transferencia</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('metodo_pago')" />
                        </div>
                    </div>

                    <div class="pt-2">
                        <div class="font-semibold text-gray-800">Detalle</div>
                        <div class="overflow-x-auto mt-2">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @for ($i = 0; $i < $rows; $i++)
                                        <tr>
                                            <td class="px-3 py-2">
                                                <select name="detalle_producto_codigo[]" class="factura-producto block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    <option value="">-</option>
                                                    @foreach ($productos as $p)
                                                        <option value="{{ $p['codigo'] }}" data-precio="{{ (float) ($p['precio'] ?? 0) }}" data-stock="{{ (int) ($p['stock'] ?? 0) }}" @selected(($oldProductos[$i] ?? '') === $p['codigo'])>
                                                            {{ $p['nombre'] }} ({{ $p['codigo'] }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-3 py-2">
                                                <x-text-input name="detalle_cantidad[]" type="number" class="factura-cantidad block w-full" value="{{ $oldCantidades[$i] ?? '' }}" min="1" step="1" inputmode="numeric" pattern="[0-9]*" />
                                            </td>
                                            <td class="px-3 py-2">
                                                <x-text-input name="detalle_precio[]" type="number" step="0.01" class="factura-precio block w-full" value="{{ $oldPrecios[$i] ?? '' }}" readonly />
                                            </td>
                                            <td class="px-3 py-2">
                                                <x-text-input type="number" step="0.01" class="factura-subtotal block w-full" value="" readonly />
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-2 text-sm">
                            <div><span class="font-semibold">Cantidad total:</span> <span id="totalCantidad">0</span></div>
                            <div><span class="font-semibold">Subtotal:</span> <span id="subtotal">0.00</span></div>
                            <div><span class="font-semibold">Descuento:</span> <span id="descuentoResumen">0.00</span></div>
                            <div><span class="font-semibold">Total:</span> <span id="total">0.00</span></div>
                        </div>

                        <div class="mt-2">
                            <button type="button" id="addRow" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                {{ __('Agregar línea') }}
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="descuento" :value="__('Descuento')" />
                            <x-text-input id="descuento" name="descuento" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('descuento') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('descuento')" />
                        </div>

                        <div>
                            <x-input-label for="observacion" :value="__('Observación')" />
                            <x-text-input id="observacion" name="observacion" type="text" class="mt-1 block w-full" value="{{ old('observacion') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('observacion')" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit">{{ __('Guardar') }}</x-primary-button>
                        <a href="{{ route('facturas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('addRow');
            const form = document.querySelector('form');
            const descuentoInput = document.getElementById('descuento');

            const fmt = (n) => (Number.isFinite(n) ? n : 0).toFixed(2);

            const recomputeRow = (tr) => {
                const select = tr.querySelector('.factura-producto');
                const qtyInput = tr.querySelector('.factura-cantidad');
                const priceInput = tr.querySelector('.factura-precio');
                const subtotalInput = tr.querySelector('.factura-subtotal');
                if (!select || !qtyInput || !priceInput || !subtotalInput) return { qty: 0, subtotal: 0 };

                const opt = select.selectedOptions?.[0];
                const unitPrice = opt ? parseFloat(opt.dataset.precio || '0') : 0;
                const stock = opt ? parseInt(opt.dataset.stock || '0', 10) : 0;

                const qty = parseInt(qtyInput.value || '0', 10);

                if (!select.value) {
                    qtyInput.setCustomValidity('');
                    priceInput.value = '';
                    subtotalInput.value = '';
                    return { qty: 0, subtotal: 0 };
                }

                if (!Number.isFinite(unitPrice) || unitPrice <= 0) {
                    qtyInput.setCustomValidity('El producto no tiene precio registrado.');
                    priceInput.value = '';
                    subtotalInput.value = '';
                    return { qty: 0, subtotal: 0 };
                }

                if (qty > stock) {
                    qtyInput.setCustomValidity('Producto no disponible/stock insuficiente. Ajusta cantidades o cambia el producto.');
                } else {
                    qtyInput.setCustomValidity('');
                }

                qtyInput.max = String(stock);

                priceInput.value = fmt(unitPrice);

                const lineSubtotal = Math.max(0, qty) * unitPrice;
                subtotalInput.value = fmt(lineSubtotal);

                return { qty: Math.max(0, qty), subtotal: lineSubtotal };
            };

            const recomputeTotals = () => {
                const tbody = document.querySelector('table tbody');
                if (!tbody) return;

                let totalQty = 0;
                let subtotal = 0;

                tbody.querySelectorAll('tr').forEach(tr => {
                    const r = recomputeRow(tr);
                    totalQty += r.qty;
                    subtotal += r.subtotal;
                });

                const descuento = parseFloat((descuentoInput?.value || '0').toString()) || 0;
                const total = Math.max(0, subtotal - descuento);

                document.getElementById('totalCantidad').textContent = String(totalQty);
                document.getElementById('subtotal').textContent = fmt(subtotal);
                document.getElementById('descuentoResumen').textContent = fmt(descuento);
                document.getElementById('total').textContent = fmt(total);
            };

            btn?.addEventListener('click', () => {
                const tbody = document.querySelector('table tbody');
                if (!tbody) return;
                const last = tbody.querySelector('tr:last-child');
                if (!last) return;
                const clone = last.cloneNode(true);
                clone.querySelectorAll('input').forEach(i => i.value = '');
                clone.querySelectorAll('select').forEach(s => s.value = '');
                tbody.appendChild(clone);
                recomputeTotals();
            });

            document.addEventListener('input', (e) => {
                const t = e.target;
                if (!t) return;
                if (t.classList?.contains('factura-cantidad') || t.id === 'descuento') {
                    recomputeTotals();
                }
            });

            document.addEventListener('change', (e) => {
                const t = e.target;
                if (!t) return;
                if (t.classList?.contains('factura-producto')) {
                    recomputeTotals();
                }
            });

            form?.addEventListener('submit', (e) => {
                recomputeTotals();
                const invalid = form.querySelector(':invalid');
                if (invalid) {
                    invalid.reportValidity();
                    e.preventDefault();
                }
            });

            recomputeTotals();
        });
    </script>
</x-app-layout>
