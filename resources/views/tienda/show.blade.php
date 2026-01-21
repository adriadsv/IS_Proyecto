<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->PRD_DESCRIPCION ?? $producto->nombre ?? 'Producto' }} - Chocolatería</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Chocolatería</h1>
                    <p class="text-sm text-gray-600">Los mejores chocolates artesanales</p>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('carrito.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            🛒 Carrito
                        </a>
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-gray-900">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Registrarse
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('tienda.index') }}" class="text-gray-700 hover:text-indigo-600">
                        Catálogo
                    </a>
                </li>
                @if($categoria)
                <li>
                    <div class="flex items-center">
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-700">{{ $categoria->CAT_NOMBRE }}</span>
                    </div>
                </li>
                @endif
                <li aria-current="page">
                    <div class="flex items-center">
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-500">{{ $producto->PRD_DESCRIPCION ?? $producto->nombre ?? 'Producto' }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Detalle del Producto -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                <!-- Imagen -->
                <div class="flex items-center justify-center bg-gradient-to-br from-amber-100 to-amber-200 rounded-lg" style="min-height: 400px;">
                    <span class="text-9xl">🍫</span>
                </div>

                <!-- Información -->
                <div class="flex flex-col justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">
                            {{ $producto->PRD_DESCRIPCION ?? $producto->nombre ?? 'Sin nombre' }}
                        </h2>

                        @if($categoria)
                            <div class="mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                    {{ $categoria->CAT_NOMBRE }}
                                </span>
                            </div>
                        @endif

                        <div class="mb-6">
                            <span class="text-4xl font-bold text-indigo-600">
                                ${{ number_format((float) ($producto->PRD_PRECIO ?? $producto->precio ?? 0), 2) }}
                            </span>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Disponibilidad:</h3>
                            @if((int) ($producto->stock ?? 0) > 0 && strtolower((string) ($producto->estado ?? '')) === 'activo')
                                <div class="flex items-center">
                                    <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                    <span class="text-green-700 font-medium">En Stock ({{ $producto->stock }} unidades)</span>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                    <span class="text-red-700 font-medium">Agotado</span>
                                </div>
                            @endif
                        </div>

                        @if($categoria && $categoria->CAT_DESCRIPCION)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Descripción:</h3>
                                <p class="text-gray-700">{{ $categoria->CAT_DESCRIPCION }}</p>
                            </div>
                        @endif

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Código:</h3>
                            <p class="text-gray-600 font-mono">{{ $producto->PRD_CODIGO ?? $producto->codigo }}</p>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="space-y-3">
                        @auth
                            @if((int) ($producto->stock ?? 0) > 0 && strtolower((string) ($producto->estado ?? '')) === 'activo')
                                <form method="POST" action="{{ route('carrito.agregar', $producto->id ?? $producto->PRD_CODIGO) }}">
                                    @csrf
                                    <div class="flex items-center space-x-4 mb-4">
                                        <label for="cantidad" class="text-sm font-medium text-gray-700">Cantidad:</label>
                                        <input type="number" name="cantidad" id="cantidad" value="1" min="1" max="{{ $producto->stock }}"
                                               class="w-20 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <button type="submit"
                                            class="w-full px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-lg font-semibold">
                                        Agregar al Carrito
                                    </button>
                                </form>
                            @else
                                <button disabled
                                        class="w-full px-6 py-3 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed text-lg font-semibold">
                                    Producto No Disponible
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="block w-full px-6 py-3 bg-indigo-600 text-white text-center rounded-md hover:bg-indigo-700 text-lg font-semibold">
                                Iniciar Sesión para Comprar
                            </a>
                        @endauth

                        <a href="{{ route('tienda.index') }}"
                           class="block w-full px-6 py-3 bg-gray-200 text-gray-700 text-center rounded-md hover:bg-gray-300 text-lg font-semibold">
                            Volver al Catálogo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-600 text-sm">
                © {{ date('Y') }} Chocolatería. Todos los derechos reservados.
            </p>
        </div>
    </footer>
</body>
</html>
