<?php

use App\Http\Controllers\BrandsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($routes) {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('user', [AuthController::class, 'user'])->name('user')->middleware([JwtMiddleware::class]);
    Route::get('refresh', [AuthController::class, 'refresh'])->name('refresh')->middleware([JwtMiddleware::class]);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware([JwtMiddleware::class]);
});

Route::prefix('brands')->middleware(JwtMiddleware::class)->controller(BrandsController::class)->group(function ($routes) {
    Route::get('index', 'index');
    Route::get('view/{id}', 'show');
    Route::post('save', 'store');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'destroy');
});

Route::prefix('category')->middleware(JwtMiddleware::class)->controller(CategoryController::class)->group(function ($routes) {
    Route::get('index', 'index');
    Route::get('view/{id}', 'show');
    Route::post('save', 'store');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'destroy');
});

Route::prefix('address')->middleware(JwtMiddleware::class)->controller(LocationController::class)->group(function ($routes) {
    Route::post('new', 'store');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'destroy');
});

Route::prefix('product')->middleware(JwtMiddleware::class)->controller(ProductController::class)->group(function ($routes) {
    Route::get('index', 'index');
    Route::get('view/{id}', 'show');
    Route::post('save', 'store');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'destroy');
});

Route::prefix('order')->middleware(JwtMiddleware::class)->controller(OrdersController::class)->group(function ($routes) {
    Route::get('index', 'index');
    Route::get('view/{id}', 'show');
    Route::post('save', 'store');
    Route::put('get_items/{id}', 'get_order_items');
    Route::delete('get_user/{id}', 'get_user_orders');
    Route::delete('update_status/{id}', 'change_order_status');
});
