<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;

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


