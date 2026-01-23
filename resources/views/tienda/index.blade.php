<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chocolatería - Catálogo de Productos</title>
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

    <!-- Filtros de Categoría -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center space-x-4 overflow-x-auto">
            <a href="{{ route('tienda.index') }}"
               class="px-4 py-2 rounded-md text-sm font-medium {{ !$categoriaFiltro ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                Todos
            </a>
            @foreach($categorias as $cat)
                <a href="{{ route('tienda.index', ['categoria' => $cat->CAT_CODIGO]) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap {{ $categoriaFiltro === $cat->CAT_CODIGO ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    {{ $cat->CAT_NOMBRE }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Grid de Productos -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($productos->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($productos as $producto)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Imagen placeholder -->
                        <div class="h-48 bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center">
                            <span class="text-6xl">🍫</span>
                        </div>

                        <!-- Contenido -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                {{ $producto->PRD_DESCRIPCION ?? $producto->nombre ?? 'Sin nombre' }}
                            </h3>

                            <div class="mb-2">
                                <span class="text-sm text-gray-600">Stock disponible: </span>
                                <span class="text-sm font-semibold text-green-600">{{ $producto->stock }}</span>
                            </div>

                            <div class="mb-4">
                                <span class="text-2xl font-bold text-indigo-600">
                                    ${{ number_format((float) ($producto->PRD_PRECIO ?? $producto->precio ?? 0), 2) }}
                                </span>
                            </div>

                            <div class="flex space-x-2">
                                <a href="{{ route('tienda.producto', $producto->PRD_CODIGO ?? $producto->codigo) }}"
                                   class="flex-1 text-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-semibold">
                                    Ver Detalle
                                </a>

                                @auth
                                    <form method="POST" action="{{ route('carrito.agregar', $producto->id ?? $producto->PRD_CODIGO) }}" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="cantidad" value="1">
                                        <button type="submit"
                                                class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-semibold">
                                            Agregar
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="flex-1 text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-semibold">
                                        Iniciar para comprar
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <p class="text-gray-600 text-lg">No hay productos disponibles en este momento.</p>
                <a href="{{ route('tienda.index') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                    Ver todos los productos
                </a>
            </div>
        @endif
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
