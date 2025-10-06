<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountsController;

Route::get('/', function () {
    return view('home');
});

Route::get('/accounts', [AccountsController::class, 'index'])->name('accounts');
