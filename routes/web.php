<?php

use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Главная страница (список товаров) - доступна всем
Route::get('/', [AdminProductController::class, 'index'])->name('products.index');
Route::get('/products', [AdminProductController::class, 'index']); // Можно удалить, если не нужен отдельный URL
Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show');

// Корзина
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

// Заказы (требуется аутентификация)
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
});

// Админ-раздел (требуется аутентификация и права администратора is_admin = 1)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Route::get('/', function () { return view('admin.dashboard'); })->name('admin.dashboard'); // Главная страница админки
    Route::resource('products', AdminProductController::class)->except(['show', 'create', 'edit']);
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
});

// Маршруты, созданные Breeze (аутентификация)
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';