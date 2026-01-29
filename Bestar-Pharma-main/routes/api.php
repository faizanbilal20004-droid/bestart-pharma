<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\OrderController;


// ---------------- PUBLIC ROUTES ---------------- //

// User routes
Route::post('signup', [UserController::class, 'store']);
Route::post('login', [UserController::class, 'login']);

Route::post('send_email_otp', [UserController::class, 'sendResetPasswordOtp']);
Route::post('verify_otp', [UserController::class, 'verifyResetPasswordOtp']);
Route::patch('reset_password', [UserController::class, 'resetPassword']);

// 🔓 Gift public GET routes (Browser testing allowed)
Route::get('gift', [GiftController::class, 'index']);      // All Gifts
Route::get('gift/{id}', [GiftController::class, 'show']);  // Single Gift





// ---------------- PROTECTED ROUTES (auth required) ---------------- //

Route::group(['middleware' => ['auth:sanctum']], function() {

    // User
    Route::get('profile', [UserController::class, 'show']);
    Route::post('logout', [UserController::class, 'logout']);

    // 🔐 Gift protected routes (login required)
    Route::post('gift', [GiftController::class, 'store']);
    Route::post('gift/{id}', [GiftController::class, 'update']);
    Route::delete('gift/{id}', [GiftController::class, 'destroy']);
    Route::post('bulk_gifts', [GiftController::class, 'bulkInsert']);

    // Coupon Routes
    Route::resource('coupon', CouponController::class);
    Route::post('bulk_coupons', [CouponController::class, 'bulkInsert']);

    // Address Routes
    Route::resource('address', AddressController::class);

    // Orders
// Route::post('order', [OrderController::class, 'store']);

    Route::resource('order', OrderController::class);
});
