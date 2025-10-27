<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\SuperAuthController;
use App\Http\Controllers\SuperUserController;
use App\Http\Controllers\SuperCompanyController;
use App\Http\Controllers\AdminController;

// 🔹 HOME
Route::get('/', fn() => view('home'))->name('home');

// 🔹 AUTH ROUTES
Route::prefix('auth')->middleware('prevent-back-history')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1')->name('register.post');

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
});

// 🔹 PASSWORD RESET
Route::prefix('password')->group(function () {
    Route::get('/forgot', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/email', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,15')->name('password.email');
    Route::get('/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset', [AuthController::class, 'resetPassword'])->middleware('throttle:5,15')->name('password.update');
});

// 🔹 AUTHENTICATED ROUTES
Route::middleware(['auth', 'prevent-back-history'])->group(function () {

    // Email verification routes
    Route::get('/post-verification', [AuthController::class, 'showPostVerificationForm'])->name('post.verification');
    Route::post('/post-verification', [AuthController::class, 'submitPostVerification'])->middleware('throttle:3,5')->name('post.verification.post');

    Route::post('/delete-temp-user', [AuthController::class, 'deleteTempUser'])->name('delete.temp.user');
    Route::post('/resend-verification', [AuthController::class, 'resendVerificationCode'])->middleware('throttle:1,1')->name('resend.verification');

    // Main welcome
    Route::get('/welcmain', fn() => view('welcmain'))->name('welcmain');

    Route::get('/company/dashboard', [CompanyController::class, 'dashboard'])
        ->middleware('company.verified')
        ->name('company.dashboard');

    // Company registration
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/create', [CompanyController::class, 'create'])->name('create');
        Route::post('/store', [CompanyController::class, 'store'])->middleware('throttle:5,1')->name('store');

        Route::middleware('company.verified')->group(function () {
            Route::get('/dashboard', [CompanyController::class, 'dashboard'])->name('dashboard');
        });
    });

    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('users.Maindashboard');
        })->name('dashboard');
        Route::get('/departments', [App\Http\Controllers\AdminController::class, 'departments'])->name('departments');
        Route::get('/assets', [App\Http\Controllers\AdminController::class, 'assets'])->name('assets');
        Route::get('/requests', [App\Http\Controllers\AdminController::class, 'requests'])->name('requests');
        Route::get('/receipts', [App\Http\Controllers\AdminController::class, 'receipts'])->name('receipts');
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
        Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
    });

    // Profile image
    Route::get('/profile-image/{filename}', [UserController::class, 'showProfileImage'])->name('profile.image');
});

// 🔹 SUPERADMIN ROUTES
Route::prefix('superadmin')->group(function () {
    Route::get('/login', [SuperAuthController::class, 'showLoginForm'])->name('superadmin.login');
    Route::post('/login', [SuperAuthController::class, 'login'])->name('superadmin.login.post');
    Route::post('/logout', [SuperAuthController::class, 'logout'])->name('superadmin.logout');

    Route::middleware(['auth:superadmin', 'prevent-back-history'])->group(function () {
        Route::get('/dashboard', function () {
            $superadmin = Auth::guard('superadmin')->user();
            return view('superadmin.dashboard', compact('superadmin'));
        })->name('superadmin.dashboard');

        Route::get('/users', [SuperUserController::class, 'index'])->name('superadmin.users');
        Route::get('/users/fetch', [SuperUserController::class, 'fetch'])->name('superadmin.users.fetch');
        Route::post('/users/store', [SuperUserController::class, 'store'])->name('superadmin.users.store');
        Route::post('/users/update/{id}', [SuperUserController::class, 'update'])->name('superadmin.users.update');
        Route::delete('/users/{id}', [SuperUserController::class, 'destroy'])->name('superadmin.users.destroy');

        Route::post('/request-edit/{id}', [SuperUserController::class, 'requestEdit'])->name('superadmin.requestEdit');
        Route::get('/confirm-edit/{id}', [SuperUserController::class, 'confirmEdit'])->name('superadmin.confirmEdit');

        Route::get('/companies', [SuperCompanyController::class, 'index'])->name('superadmin.companies');
        Route::post('/companies/{id}/approve', [SuperCompanyController::class, 'approve'])->name('superadmin.companies.approve');
        Route::post('/companies/{id}/reject', [SuperCompanyController::class, 'reject'])->name('superadmin.companies.reject');
    });
});
