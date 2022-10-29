<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\User\PictureController;
use App\Http\Controllers\SuperAdmin\User\ProfileController;
use App\Http\Controllers\SuperAdmin\User\PasswordController;
use App\Http\Controllers\SuperAdmin\Auth\NewPasswordController;
use App\Http\Controllers\SuperAdmin\Auth\VerifyEmailController;
use App\Http\Controllers\SuperAdmin\Auth\RegisteredUserController;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use App\Http\Controllers\SuperAdmin\Auth\PasswordResetLinkController;
use App\Http\Controllers\SuperAdmin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SuperAdmin\Auth\EmailVerificationNotificationController;

Route::prefix('super_admin')->name('super_admin.')->group(function () {

    Route::middleware('guest:super_admin')->group(function () {
        // Route::post('/register', [RegisteredUserController::class, 'store'])
        //     ->name('register');

        Route::post('/login', [AuthenticatedSessionController::class, 'store'])
            ->name('login');

        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
            ->name('password.email');

        Route::post('/reset-password', [NewPasswordController::class, 'store'])
            ->name('password.update');
    });

    Route::middleware('auth:super_admin')->group(function () {

        Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware(['throttle:6,1'])
            ->name('verification.send');

        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::get('/', [DashboardController::class, 'index']);

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        //Pengurusan Pengguna Routes
        Route::prefix('user')->name('user.')->group(function () {

            Route::get('', function(Request $request){
                $request->user('super_admin')->setImageURL();
                return response()->json($request->user('super_admin'));
            });

            //Profile
            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('', [ProfileController::class, 'view'])->name('view');
                Route::patch('', [ProfileController::class, 'update'])->name('update');
            });

            //Password
            Route::prefix('password')->name('password.')->group(function () {
                Route::patch('', [PasswordController::class, 'update'])->name('update');
            });

            //Profile Picture
            Route::prefix('picture')->name('picture.')->group(function () {
                Route::patch('', [PictureController::class, 'update'])->name('update');
            });
        });

        Route::prefix('system')->name('system.')->group(function () {
            Route::prefix('health')->name('health.')->group(function () {
                Route::get('', HealthCheckJsonResultsController::class)->name('check');
            });
        });
    });
});
