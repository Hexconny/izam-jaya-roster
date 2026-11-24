<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Site as Pub;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Publik (Customer, tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [Pub\HomeController::class, 'index'])->name('home');
Route::get('/tentang', [Pub\AboutController::class, 'index'])->name('about');

Route::get('/katalog', [Pub\CatalogController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{category:slug}', [Pub\CatalogController::class, 'category'])->name('catalog.category');
Route::get('/produk/{product:slug}', [Pub\CatalogController::class, 'show'])->name('product.show');

Route::get('/keranjang', [Pub\CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang', [Pub\CartController::class, 'add'])->name('cart.add');
Route::patch('/keranjang/{variant}', [Pub\CartController::class, 'update'])->name('cart.update');
Route::delete('/keranjang/{variant}', [Pub\CartController::class, 'remove'])->name('cart.remove');

Route::post('/checkout', [Pub\CheckoutController::class, 'store'])->name('checkout.store');

// Lacak pesanan, bayar, dan upload bukti (tanpa login)
Route::get('/lacak', [Pub\TrackController::class, 'form'])->name('track.form');
Route::post('/lacak', [Pub\TrackController::class, 'lookup'])->name('track.lookup');
Route::get('/lacak/{order:order_code}', [Pub\TrackController::class, 'show'])->name('track.show');
Route::post('/lacak/{order:order_code}/bukti', [Pub\TrackController::class, 'proof'])->name('track.proof');

/*
|--------------------------------------------------------------------------
| Halaman Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'login'])->name('login.submit');

    Route::middleware('admin')->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');

        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('beranda', [Admin\HomepageController::class, 'edit'])->name('homepage.edit');
        Route::put('beranda', [Admin\HomepageController::class, 'update'])->name('homepage.update');

        Route::get('tentang', [Admin\AboutController::class, 'edit'])->name('about.edit');
        Route::put('tentang', [Admin\AboutController::class, 'update'])->name('about.update');

        Route::resource('kategori', Admin\CategoryController::class)
            ->only(['index', 'edit', 'update'])
            ->parameters(['kategori' => 'category'])
            ->names('categories');

        Route::get('stok', [Admin\StockController::class, 'index'])->name('stock.index');
        Route::put('stok', [Admin\StockController::class, 'update'])->name('stock.update');
        Route::get('stok/riwayat', [Admin\StockController::class, 'history'])->name('stock.history');

        Route::resource('produk', Admin\ProductController::class)
            ->except('show')
            ->parameters(['produk' => 'product'])
            ->names('products');

        Route::put('pesanan/{order}/ongkir', [Admin\OrderController::class, 'shipping'])->name('orders.shipping');
        Route::put('pesanan/{order}/verifikasi', [Admin\OrderController::class, 'verify'])->name('orders.verify');
        Route::get('pesanan/{order}/bukti', [Admin\OrderController::class, 'proof'])->name('orders.proof');

        Route::get('pembayaran', [Admin\PaymentSettingController::class, 'edit'])->name('payment.edit');
        Route::put('pembayaran', [Admin\PaymentSettingController::class, 'update'])->name('payment.update');

        Route::resource('pesanan', Admin\OrderController::class)
            ->except('edit')
            ->parameters(['pesanan' => 'order'])
            ->names('orders');
    });
});
