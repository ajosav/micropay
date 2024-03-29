<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\VerificationController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\WalletController;

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

Route::group(['prefix' => 'v1'], function () {

    // Users authentication
    Route::name('auth.')->prefix('auth')->group(function () {
        Route::post('register', [RegisterController::class, 'register'])->name('register');
        Route::post('login', [LoginController::class, 'login'])->name('login');
        Route::get('refresh-token', [LoginController::class, 'refreshToken'])->name('refresh');
        Route::get('current-user', [AuthController::class, 'authenticatedUser'])->name('current');
        Route::post('/email/verify', [VerificationController::class, 'verifyEmail'])->name('verify')->middleware('jwt');
        Route::get('resend/verification-link', [VerificationController::class, 'resendLink'])->name('resend')->middleware('jwt');
        Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->name('password.request');
        Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
        Route::get('logout', [AuthController::class, 'logout']);
    });

    Route::name('wallet.')->prefix('wallet')->group(function () {
        Route::post('fund-wallet', [WalletController::class, 'fundWallet'])->name('fund_wallet');
        Route::post('fund-my-wallet', [WalletController::class, 'fundMyWallet'])->name('fund_my_wallet');
        Route::get('balance', [WalletController::class, 'getWalletBalance'])->name('balance');
        Route::get('withdraw', [WalletController::class, 'withdrawFromWallet'])->name('withdraw');
    });
});
