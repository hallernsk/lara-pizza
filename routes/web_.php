<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');


// Маршруты, созданные Breeze (аутентификация)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// Auth::routes();

// Админ-панель (требуется аутентификация и роль администратора)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', function () { return view('admin.dashboard'); })->name('admin.dashboard'); // Главная страница админки
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::resource('orders', App\Http\Controllers\OrderController::class)->only(['index', 'show', 'update']); // Добавим, когда будет OrderController
});

require __DIR__.'/auth.php';
