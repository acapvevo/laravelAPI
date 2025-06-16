<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Setting\PermissionController;
use App\Http\Controllers\Setting\RoleController;
use App\Http\Controllers\Setting\UserController;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;

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

    Route::prefix('setting')->name('setting')->group(function () {
        Route::get('health', HealthCheckJsonResultsController::class)->middleware('can:health')->name('health');

        Route::prefix('user')->middleware('can:users')->name('user')->group(function () {
            Route::get('/', [UserController::class, 'list'])
                ->middleware('can:users.view')
                ->name('list');
            Route::get('/{id}', [UserController::class, 'index'])
                ->middleware('can:users.view')
                ->name('index');
            Route::post('/', [UserController::class, 'create'])
                ->middleware('can:users.create')
                ->name('create');
            Route::patch('/{id}', [UserController::class, 'update'])
                ->middleware('can:users.update')
                ->name('update');
            Route::delete('/{id}', [UserController::class, 'delete'])
                ->middleware('can:users.delete')
                ->name('delete');
        });

        Route::prefix('role')->middleware('can:roles')->name('role')->group(function () {
            Route::get('/', [RoleController::class, 'list'])
                ->middleware('can:roles.view')
                ->name('list');
            Route::get('/{id}', [RoleController::class, 'index'])
                ->middleware('can:roles.view')
                ->name('index');
            Route::post('/', [RoleController::class, 'create'])
                ->middleware('can:roles.create')
                ->name('create');
            Route::patch('/{id}', [RoleController::class, 'update'])
                ->middleware('can:roles.update')
                ->name('update');
            Route::delete('/{id}', [RoleController::class, 'delete'])
                ->middleware('can:roles.delete')
                ->name('delete');
        });

        Route::prefix('permission')->middleware('can:permissions')->name('permission')->group(function () {
            Route::get('/', [PermissionController::class, 'list'])
                ->middleware('can:permissions.view')
                ->name('list');
            Route::get('/{id}', [PermissionController::class, 'index'])
                ->middleware('can:permissions.view')
                ->name('index');
        });
    });
});
