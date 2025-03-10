<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Auth
Route::group([
   'middleware' => 'api',
   'prefix' => 'auth'
], function ($router){
   Route::post('/login', [AuthController::class, 'login'])->name('login');
   Route::post('/register', [AuthController::class, 'register'])->name('register');;
   Route::post('/logout', [AuthController::class, 'logout'])->name('logout');;
   Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');;
   Route::get('/me', [AuthController::class, 'me'])->name('me');;
});

// Route::middleware('auth:api')->group(function() {  //Для авторизованных
// });

// Главная страница (список товаров) - доступна ВСЕМ
Route::get('/', [AdminProductController::class, 'index'])->name('products.index');
Route::get('/products', [AdminProductController::class, 'index']); //дублирует
Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add'); //->middleware('auth');  // Если нужна корзина только для авторизованных
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

// Заказы (требуется аутентификация)
 Route::middleware('auth:api')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
 });

// Админ-панель (требуется аутентификация и роль администратора)
Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {  //auth:api
    Route::get('/', function () { return response()->json(['message' => 'Admin dashboard']); })->name('admin.dashboard'); // !!! Админка.  Заглушка.  В реальности здесь, скорее всего, будет какой-то контроллер.
    Route::get('/products/{product}/view', [AdminProductController::class, 'adminShow'])->name('admin.products.show'); //Если нужен
    Route::resource('products', AdminProductController::class)->except(['show', 'edit']); // !!!
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']); // !!!
});