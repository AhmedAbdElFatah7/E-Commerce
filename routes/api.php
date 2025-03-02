<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Checkout\CheckoutController;
use App\Http\Controllers\Checkout\LocationController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Shop\ShopController;
use App\Models\Cart;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('sign', [AuthController::class, 'sign']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['jwt.auth'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    
    Route::post('upload-image', [ProductController::class, 'uploadImage']);
    Route::post('/products', [ProductController::class, 'store']);

    Route::post('/add-reviews', [ShopController::class, 'store']);

    Route::post('/checkout', [CheckoutController::class, 'checkout']);
    Route::get('/checkout-info', [LocationController::class, 'checkout']);
    Route::post('/store-Location', [LocationController::class, 'storeLocation']);




    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::post('/cart/remove', [CartController::class, 'removeFromCart']);
    Route::get('/cart', [CartController::class, 'viewCart']);
    Route::get('/cart/count', function () {
        $cartItems =Cart::where('user_id', auth()->id())->get();
    
        return response()->json([
            'total_items' => $cartItems->sum('quantity'), 
        ]);
    });
});
Route::get('/home', [HomeController::class, 'index']);
Route::get('/men', [HomeController::class, 'men']);
Route::get('/women', [HomeController::class, 'women']);
Route::get('/topselling', [HomeController::class, 'topSelling']);
Route::get('/newest', [HomeController::class, 'newest']);

Route::get('/shop', [ShopController::class, 'index']);
Route::get('/get-reviews', [ShopController::class, 'getReviews']);