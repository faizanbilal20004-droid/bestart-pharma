<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::post('signup', [UserController::class, 'store']);
Route::post('login', [UserController::class, 'login']);

Route::post('send_email_otp', [UserController::class, 'sendResetPasswordOtp']);
Route::post('verify_otp', [UserController::class, 'verifyResetPasswordOtp']);
Route::patch('reset_password', [UserController::class, 'resetPassword']);

Route::get('gift', [GiftController::class, 'index']);
Route::get('gift/{id}', [GiftController::class, 'show']);


/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('profile', [UserController::class, 'show']);
    Route::post('logout', [UserController::class, 'logout']);

    // Route::post('order', [OrderController::class, 'store']);
    // Route::get('orders/my', [OrderController::class, 'myOrders']);

    Route::post('gift', [GiftController::class, 'store']);
    Route::post('gift/{id}', [GiftController::class, 'update']);
    Route::delete('gift/{id}', [GiftController::class, 'destroy']);

    Route::post('bulk_gifts', [GiftController::class, 'bulkInsert']);

    Route::resource('coupon', CouponController::class);
    Route::post('bulk_coupons', [CouponController::class, 'bulkInsert']);

    Route::resource('address', AddressController::class);
     // Orders
     Route::resource('order', OrderController::class)->only(['index', 'store']);

});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (VERY IMPORTANT)
|--------------------------------------------------------------------------
*/



Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    Route::get('orders', [OrderController::class, 'adminOrders']);

});
