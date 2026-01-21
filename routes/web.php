<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\BodegaProductoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\TiendaController;
use Illuminate\Support\Facades\Route;

// Rutas públicas de la tienda (catálogo de ecommerce)
Route::get('/', [TiendaController::class, 'index'])->name('tienda.index');
Route::get('/tienda', [TiendaController::class, 'index'])->name('tienda.catalogo');
Route::get('/producto/{codigo}', [TiendaController::class, 'show'])->name('tienda.producto');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('clientes', ClienteController::class)
        ->parameters(['clientes' => 'cliente'])
        ->only(['index', 'show'])
        ->whereNumber('cliente');
    Route::resource('productos', ProductoController::class)
        ->parameters(['productos' => 'producto'])
        ->only(['index', 'show'])
        ->whereNumber('producto');
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor'])
        ->only(['index', 'show'])
        ->whereNumber('proveedor');
    Route::resource('compras', CompraController::class)
        ->parameters(['compras' => 'compra'])
        ->only(['index', 'show'])
        ->whereNumber('compra');

    Route::get('carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('carrito/{producto}', [CarritoController::class, 'agregar'])->whereNumber('producto')->name('carrito.agregar');
    Route::post('carrito/{producto}/quitar-uno', [CarritoController::class, 'quitarUno'])->whereNumber('producto')->name('carrito.quitar_uno');
    Route::delete('carrito/{producto}', [CarritoController::class, 'quitarProducto'])->whereNumber('producto')->name('carrito.quitar_producto');
    Route::post('carrito/pagar', [CarritoController::class, 'pagar'])->name('carrito.pagar');

    Route::middleware('admin')->group(function () {
        Route::get('clientes/consulta-parametro', [ClienteController::class, 'consultaParametro'])
            ->name('clientes.consulta_parametro');
        Route::resource('clientes', ClienteController::class)
            ->parameters(['clientes' => 'cliente'])
            ->except(['index', 'show'])
            ->whereNumber('cliente');
        Route::get('clientes/{cliente}/eliminar', [ClienteController::class, 'confirmDelete'])
            ->whereNumber('cliente')
            ->name('clientes.confirm_delete');
        Route::resource('productos', ProductoController::class)
            ->parameters(['productos' => 'producto'])
            ->except(['index', 'show'])
            ->whereNumber('producto');
        Route::get('productos/consulta-parametro', [ProductoController::class, 'consultaParametro'])
            ->name('productos.consulta_parametro');
        Route::get('productos/{producto}/eliminar', [ProductoController::class, 'confirmDelete'])
            ->whereNumber('producto')
            ->name('productos.confirm_delete');
        Route::resource('proveedores', ProveedorController::class)
            ->parameters(['proveedores' => 'proveedor'])
            ->except(['index', 'show'])
            ->whereNumber('proveedor');
        Route::get('proveedores/consulta-parametro', [ProveedorController::class, 'consultaParametro'])
            ->name('proveedores.consulta_parametro');
        Route::get('proveedores/{proveedor}/eliminar', [ProveedorController::class, 'confirmDelete'])
            ->whereNumber('proveedor')
            ->name('proveedores.confirm_delete');
        Route::resource('compras', CompraController::class)
            ->parameters(['compras' => 'compra'])
            ->except(['index', 'show'])
            ->whereNumber('compra');
        Route::get('compras/consulta-parametro', [CompraController::class, 'consultaParametro'])
            ->name('compras.consulta_parametro');
        Route::get('compras/{compra}/eliminar', [CompraController::class, 'confirmDelete'])
            ->whereNumber('compra')
            ->name('compras.confirm_delete');

        Route::get('bodega/consulta-parametro', [BodegaProductoController::class, 'consultaParametro'])->name('bodega.consulta_parametro');
        Route::get('bodega/{codigo}/eliminar', [BodegaProductoController::class, 'confirmDelete'])->name('bodega.confirm_delete');
        Route::resource('bodega', BodegaProductoController::class)
            ->parameters(['bodega' => 'codigo'])
            ->except(['destroy']);
        Route::delete('bodega/{codigo}', [BodegaProductoController::class, 'destroy'])->name('bodega.destroy');

        Route::get('facturas/consulta-parametro', [FacturaController::class, 'consultaParametro'])->name('facturas.consulta_parametro');
        Route::get('facturas/{numero}/anular', [FacturaController::class, 'confirmAnular'])->whereNumber('numero')->name('facturas.confirm_anular');
        Route::post('facturas/{numero}/anular', [FacturaController::class, 'anular'])->whereNumber('numero')->name('facturas.anular');
        Route::resource('facturas', FacturaController::class)
            ->parameters(['facturas' => 'numero'])
            ->except(['destroy'])
            ->whereNumber('numero');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
