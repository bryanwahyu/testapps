<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CustomerAuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShoppingCartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('v1')->group(function () {
    Route::prefix('customer')->group(function(){
    Route::post('login',[CustomerAuthController::class,'login']);
    Route::post('register',[CustomerAuthController::class,'register']);
        Route::middleware(['auth:customer-api'])->group(function () {
            Route::post('auth/change/pass',[CustomerAuthController::class,'change_password_itself']);
            Route::get('auth',[CustomerAuthController::class,'auth_data']);
            Route::post('auth',[CustomerAuthController::class,'change_auth_data']);
            Route::post('cart',[ShoppingCartController::class,'add_into_cart']);
            Route::get('cart',[ShoppingCartController::class,'auth_index_cart']);
            Route::delete('cart/{cart}',[ShoppingCartController::class,'delete_cart']);
            Route::get('pay_cart',[ShoppingCartController::class,'pay_cart']);
        });
    });
    Route::resource('product', ProductController::class);
});
