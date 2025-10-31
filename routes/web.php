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
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ManagerUserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VendorController;

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
        Route::post('/join', [CompanyController::class, 'joinCompany'])->name('join');

        Route::middleware('company.verified')->group(function () {
            Route::get('/dashboard', [CompanyController::class, 'dashboard'])->name('dashboard');
        });
    });

    // ==========================
    // 🔹 ADMIN / USERS DASHBOARD
    // ==========================
    Route::middleware(['auth','company.member'])->prefix('users')->name('users.')->group(function () {
        Route::get('/dashboard', function () {
            return view('users.Maindashboard');
        })->name('dashboard');

        Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
        Route::get('/assets/order-form', [AssetController::class, 'showOrderForm'])->name('orders.form');
        Route::get('/assets', [AssetController::class, 'assets'])->name('assets');
        Route::post('/assets/add-category', [AssetController::class, 'addCategory'])->name('assets.addCategory');

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index'); // List all orders
            Route::get('/create/{itemType}/{itemId}', [OrderController::class, 'create'])->name('create');
            Route::post('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('users.orders.updateStatus');
            Route::post('/store', [OrderController::class, 'store'])->name('store');
        });

        Route::post('/vendors', [VendorController::class, 'store'])->name('vendors.store');

        // Inventory routes
    Route::prefix('inventory')->name('inventory.')->group(function() {
        Route::get('/', [InventoryController::class, 'index'])->name('index');             
        Route::get('/create', [InventoryController::class, 'create'])->name('create');      
        Route::post('/', [InventoryController::class, 'store'])->name('store');            
        Route::put('/{id}', [InventoryController::class, 'update'])->name('update');       
        Route::put('/{id}/restock', [InventoryController::class, 'restock'])->name('restock');
        Route::post('/{id}/assign', [InventoryController::class, 'assign'])->name('assign');   
        Route::post('/{id}/withdraw', [InventoryController::class, 'withdraw'])->name('withdraw'); 
        Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('destroy');  
    });

        Route::get('/requests', [AdminController::class, 'requests'])->name('requests');
        Route::get('/receipts', [AdminController::class, 'receipts'])->name('receipts');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/add-sector', [AdminController::class, 'addSector'])->name('addSector');

        Route::get('/sector/{sector}/users', [AdminController::class, 'sectorUsers'])->name('sector.users');
        Route::get('/sector/{sector}/add-user', [AdminController::class, 'addUserForm'])->name('addUserForm');
        Route::post('/sector/{sector}/assign-user/{user}', [AdminController::class, 'assignUserToSector'])->name('assignUserToSector');
        Route::delete('/user/{user}', [AdminController::class, 'kickUser'])->name('kickUser'); 
        Route::get('/sector/{sector}/edit-manager', [AdminController::class, 'editManager'])->name('editManager'); 

        Route::get('/invite', [InvitationController::class, 'index'])->name('invite');
        Route::post('/invite/send', [InvitationController::class, 'sendInvite'])->name('sendInvite');
        Route::get('/invite/{token}', [InvitationController::class, 'acceptInvite'])->name('users.acceptInvite');

        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    });

    // ==========================
    // 🔹 MANAGER DASHBOARD
    // ==========================
    Route::middleware(['auth', 'company.member'])
        ->prefix('manager')
        ->name('manager.')
        ->group(function () {
            Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
            Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
            Route::post('/inventory/{id}/withdraw', [InventoryController::class, 'withdraw'])->name('inventory.withdraw');
            Route::get('/requests', [ManagerController::class, 'requests'])->name('requests');
            Route::get('/assets', [AssetController::class, 'assets'])->name('assets');
            Route::get('/analytics', [ManagerController::class, 'analytics'])->name('analytics');
            Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts');
            Route::get('/users', [ManagerUserController::class, 'index'])->name('users');
        });

    // ==========================
    // 🔹 EMPLOYEE DASHBOARD
    // ==========================
    Route::middleware(['auth', 'company.member'])
        ->prefix('employee')
        ->name('employee.')
        ->group(function () {
            Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
            Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
            Route::post('/inventory/{id}/withdraw', [InventoryController::class, 'withdraw'])->name('inventory.withdraw');
            Route::get('/my-requests', [EmployeeController::class, 'myRequests'])->name('myRequests');
            Route::get('/assets', [AssetController::class, 'assets'])->name('assets');
            Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts');
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
        Route::post('/users/inventory/{id}/withdraw', [InventoryController::class, 'withdraw'])
            ->name('users.inventory.withdraw');

        Route::post('/request-edit/{id}', [SuperUserController::class, 'requestEdit'])->name('superadmin.requestEdit');
        Route::get('/confirm-edit/{id}', [SuperUserController::class, 'confirmEdit'])->name('superadmin.confirmEdit');

        Route::get('/companies', [SuperCompanyController::class, 'index'])->name('superadmin.companies');
        Route::post('/companies/{id}/approve', [SuperCompanyController::class, 'approve'])->name('superadmin.companies.approve');
        Route::post('/companies/{id}/reject', [SuperCompanyController::class, 'reject'])->name('superadmin.companies.reject');
    });
});
