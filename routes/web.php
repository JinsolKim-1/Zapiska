<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\SuperAuthController;
use App\Http\Controllers\SuperUserController;

// 🔹 HOME
Route::get('/', function () {
    return view('home');
})->name('home');


// 🔹 AUTH ROUTES
Route::prefix('auth')->middleware('prevent-back-history')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1') 
        ->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,1')
        ->name('register.post');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');
});

// 🔹 PASSWORD RESET ROUTES
Route::prefix('password')->group(function () {

    Route::get('/forgot', [AuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');

    Route::post('/email', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:3,15')
        ->name('password.email');

    Route::get('/reset/{token}', [AuthController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,15')
        ->name('password.update');
});


// 🔹 AUTHENTICATED ROUTES
Route::middleware(['auth', 'prevent-back-history'])->group(function () {

    Route::get('/post-verification', [AuthController::class, 'showPostVerificationForm'])
        ->name('post.verification');

    Route::post('/post-verification', [AuthController::class, 'submitPostVerification'])
        ->middleware('throttle:3,5')
        ->name('post.verification.post');

    Route::post('/delete-temp-user', [AuthController::class, 'deleteTempUser'])
        ->name('delete.temp.user');

    Route::post('/resend-verification', [AuthController::class, 'resendVerificationCode'])
        ->middleware('throttle:1,1')
        ->name('resend.verification');

    Route::get('/welcmain', function () {
        return view('welcmain');
    })->name('welcmain');

    Route::get('/profile-image/{filename}', [UserController::class, 'showProfileImage'])
        ->name('profile.image');

    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/create', [CompanyController::class, 'create'])->name('create');
        Route::post('/store', [CompanyController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('store');
    });

});

Route::prefix('superadmin')->group(function () {

    // 🔹 Authentication routes
    Route::get('/login', [SuperAuthController::class, 'showLoginForm'])
        ->name('superadmin.login');

    Route::post('/login', [SuperAuthController::class, 'login'])
        ->name('superadmin.login.post');

    Route::post('/logout', [SuperAuthController::class, 'logout'])
        ->name('superadmin.logout');

    // 🔹 Protected routes
    Route::middleware(['auth:superadmin', 'prevent-back-history'])->group(function () {

        // Dashboard
        Route::get('/dashboard', function () {
            $superadmin = Auth::guard('superadmin')->user();
            return view('superadmin.dashboard', compact('superadmin'));
        })->name('superadmin.dashboard');

        Route::get('/users', [SuperUserController::class, 'index'])->name('superadmin.users');
        Route::get('/users/fetch', [SuperUserController::class, 'fetch'])->name('superadmin.users.fetch');
        Route::post('/users/store', [SuperUserController::class, 'store'])->name('superadmin.users.store');
        Route::post('/users/update/{id}', [SuperUserController::class, 'update'])->name('superadmin.users.update');
        Route::delete('/users/{id}', [SuperUserController::class, 'destroy'])->name('superadmin.users.destroy');

        Route::get('/companies', [SuperUserController::class, 'index'])->name('superadmin.companies');
    });
});



