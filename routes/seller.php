<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\Seller\UserController;
use App\Http\Controllers\API\Seller\LoginController;
use App\Http\Controllers\API\Seller\OrderController;
use App\Http\Controllers\API\Seller\BannerController;
use App\Http\Controllers\API\Seller\WalletController;
use App\Http\Controllers\Seller\SellerChatController;
use App\Http\Controllers\API\Seller\ProductController;
use App\Http\Controllers\API\Seller\DashboardController;
use App\Http\Controllers\API\Seller\ReturnOrderController;
use App\Http\Controllers\API\Seller\NotificationController;

// ==========Route for seller==========
Route::prefix('/seller')->group(function () {

    // auth route
    Route::controller(LoginController::class)->group(function () {
        Route::post('/login', 'login')->name('seller.login');
        Route::post('/registration', 'register')->name('seller.register');
        Route::post('/forgot-password', 'forgotPassword');
        Route::post('/send-otp', 'sendOTP');
        Route::post('/verify-otp', 'verifyOtp');
        Route::get('/check-user-status', 'checkUserStatus');
        Route::post('/check-email-phone', 'checkEmailPhone');
    });

    // auth middleware for rider
    Route::middleware(['auth:sanctum', 'role:shop'])->group(function () {

        // user route
        Route::controller(UserController::class)->group(function () {
            Route::get('/details', 'show');
            Route::post('/user-update', 'updateProfile');
            Route::post('/shop-update', 'shopUpdate');
            Route::post('/shop-setting-update', 'shopSettingUpdate');
        });

        // banner route
        Route::controller(BannerController::class)->group(function () {
            Route::get('/banners', 'index');
            Route::post('/banners/store', 'store');
            Route::post('/banners/update', 'update');
            Route::delete('/banners/{banner}', 'destroy');
        });

        // dashboard route
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // change password
        Route::post('/change-password', [LoginController::class, 'changePassword']);

        // order route
        Route::controller(OrderController::class)->group(function () {
            Route::get('/orders', 'index');
            Route::get('/orders/details', 'show');
            Route::post('/orders/status-update', 'update');
        });

        // wallet route
        Route::controller(WalletController::class)->group(function () {
            Route::get('/wallet', 'index');
            Route::get('/wallet/history', 'history');
            Route::post('/wallet/withdraw', 'withdraw');
        });

        // notification
        Route::controller(NotificationController::class)->group(function () {
            Route::get('/notifications', 'index');
            Route::post('/notifications/{notification}', 'update');
            Route::delete('/notifications/{notification}', 'delete');
        });

        // Products
        Route::controller(ProductController::class)->group(function () {
            Route::get('/products', 'index');
            Route::post('/product/store', 'store');
            Route::post('/product/{product}/update', 'update');
            Route::get('/product/{product}/show', 'show');
            Route::get('/product/create-data', 'createData');
            Route::post('/product/status/toggle/{product}', 'statusToggle');
            Route::delete('/product/{product}/destroy', 'destroy');
            Route::delete('/product/thumbnail/delete', 'thumbnailDestroy');
        });

        // customer messages route
        Route::controller(SellerChatController::class)->group(function () {
            Route::get('/get-users', 'getUsers');
            Route::get('/get-message', 'getMessageAdmin');
            Route::post('/send-message', 'sendMessageAdmin');
            Route::get('/unread-messages', 'unreadMessages');
        });

         //return order
        Route::controller(ReturnOrderController::class)->group(function () {
            Route::get('/return-orders', 'index');
            Route::get('/return-order-details/{returnOrder}', 'show');
            Route::post('/return-order/{returnOrder}/status-change', 'statusChange');
        });

        // logout
        Route::get('/logout', [LoginController::class, 'logout']);
    });
});
