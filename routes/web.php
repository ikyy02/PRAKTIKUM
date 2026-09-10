<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabItemController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('items', [LabItemController::class, 'index'])->name('items.index');

    Route::middleware('admin')->group(function () {
        Route::get('items/create', [LabItemController::class, 'create'])->name('items.create');
        Route::post('items', [LabItemController::class, 'store'])->name('items.store');
        Route::get('items/{item}/edit', [LabItemController::class, 'edit'])->name('items.edit');
        Route::put('items/{item}', [LabItemController::class, 'update'])->name('items.update');
        Route::delete('items/{item}', [LabItemController::class, 'destroy'])->name('items.destroy');

        Route::post('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        Route::post('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
        Route::post('loans/{loan}/return', [LoanController::class, 'return'])->name('loans.return');
    });

    Route::get('loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('loans', [LoanController::class, 'store'])->name('loans.store');
    Route::delete('loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
});
