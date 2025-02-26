<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IncomingGoodController;
use App\Http\Controllers\SubCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [IncomingGoodController::class, 'index'])->name('home');
    Route::prefix('incoming-good')->name('incoming-good.')->group(function () {
        Route::get('/data-table', [IncomingGoodController::class, 'dataTable'])->name('data-table');
        Route::get('/report/{id?}', [IncomingGoodController::class, 'downloadReport'])->name('download-report');
        Route::post('/', [IncomingGoodController::class, 'store'])->name('store');
        Route::get('/{id?}', [IncomingGoodController::class, 'show'])->name('show');
        Route::patch('/status/{id?}', [IncomingGoodController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{id?}', [IncomingGoodController::class, 'update'])->name('update');
        Route::delete('/{id?}', [IncomingGoodController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('user-management')->name('user-management.')->group(function () {
        Route::get('/', [AuthController::class, 'userManagement'])->name('index');
        Route::get('/data-table', [AuthController::class, 'dataTable'])->name('data-table');
        Route::post('/', [AuthController::class, 'register'])->name('register');
        Route::patch('/lock/{id?}', [AuthController::class, 'lockUser'])->name('lock');
        Route::get('/{id?}', [AuthController::class, 'show'])->name('show');
        Route::patch('/{id?}', [AuthController::class, 'update'])->name('update');
        Route::delete('/{id?}', [AuthController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('category-management')->name('category-management.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/data-table', [CategoryController::class, 'dataTable'])->name('data-table');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id?}', [CategoryController::class, 'show'])->name('show');
        Route::patch('/{id?}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id?}', [CategoryController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('sub-category-management')->name('sub-category-management.')->group(function () {
        Route::get('/', [SubCategoryController::class, 'index'])->name('index');
        Route::get('/all/{id?}', [SubCategoryController::class, 'all'])->name('all');
        Route::get('/data-table', [SubCategoryController::class, 'dataTable'])->name('data-table');
        Route::post('/', [SubCategoryController::class, 'store'])->name('store');
        Route::get('/{id?}', [SubCategoryController::class, 'show'])->name('show');
        Route::patch('/{id?}', [SubCategoryController::class, 'update'])->name('update');
        Route::delete('/{id?}', [SubCategoryController::class, 'destroy'])->name('destroy');
    });
});
Route::get('/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/process-login', [AuthController::class, 'login'])->name('process-login')->middleware('guest');
