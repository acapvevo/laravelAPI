<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

Route::middleware('guest:api')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->name('register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login');

    Route::post('/refresh', [AuthenticatedSessionController::class, 'refresh'])
        ->name('refresh');

    Route::prefix('password')->name('password')->group(function () {
        Route::post('/forgot', [PasswordResetLinkController::class, 'store'])
            ->name('email');

        Route::post('/reset', [NewPasswordController::class, 'store'])
            ->name('store');
    });
});

Route::middleware('auth:api')->group(function () {

    Route::prefix('verification')->name('verification')->group(function () {

        Route::get('/email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verify');

        Route::post('/email/notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware(['throttle:6,1'])
            ->name('send');
    });

    Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::prefix('profile')->name('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])
            ->name('index');

        Route::post('/picture', [ProfileController::class, 'picture'])
            ->name('picture');

        Route::post('/password', [ProfileController::class, 'password'])
            ->name('password');

        Route::patch('/', [ProfileController::class, 'update'])
            ->name('update');
    });
});
