<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ThemeSettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Client
Route::prefix('/')->group(function (){
    Route::get('/',[HomeController::class, 'index'])->name('home');
    // Customer
    Route::prefix('customer')->group(function (){
        // Login
        Route::get('login',[CustomerController::class, 'login'])->name('home.login');
        Route::post('login',[CustomerController::class, 'store_login'])->name('home.store_login');
        // Register
        Route::get('register',[CustomerController::class, 'register'])->name('home.register');
        Route::post('register',[CustomerController::class, 'store_register'])->name('home.store_register');
        // Logout
        Route::get('logout',[CustomerController::class, 'logout'])->name('home.logout');
    });

});
// Admin
Route::prefix('admin')->group(function (){
    // Login
    Route::get('/',[AdminController::class, 'index'])->name('admin.login');
    Route::post('/',[AdminController::class, 'store_login'])->name('admin.store_login');
    // Register
    Route::get('/register',[AdminController::class, 'register'])->name('admin.register');
    Route::post('/register',[AdminController::class, 'store_register'])->name('admin.store_register');
    // Logout
    Route::get('/logout',[AdminController::class, 'logout'])->name('admin.logout');
    // Dashboard
    Route::get('dashboard',[AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware('auth');
    // Products
    Route::prefix('products')->middleware('auth')->group(function (){
        Route::get('/',[ProductController::class, 'index'])->name('admin.product');
        Route::get('addOrUpdate/{id?}',[ProductController::class, 'addOrUpdate'])->name('admin.product.addOrUpdate');
        Route::post('store/{id?}',[ProductController::class, 'store'])->name('admin.product.store');
        Route::get('delete/{id?}',[ProductController::class, 'delete'])->name('admin.product.delete');
    });
    // Categories
    Route::prefix('categories')->middleware('auth')->group(function (){
        Route::get('{id?}',[CategoryController::class, 'index'])->name('admin.category');
        Route::post('store/{id?}',[CategoryController::class, 'store'])->name('admin.category.store');
        Route::get('delete/{id?}',[CategoryController::class, 'delete'])->name('admin.category.delete');
    });
    // Customers
    Route::prefix('customers')->middleware('auth')->group(function (){
        Route::get('{id?}',[CustomerController::class, 'index'])->name('admin.customer');
        Route::post('store/{id?}',[CustomerController::class, 'store'])->name('admin.customer.store');
        Route::get('delete/{id?}',[CustomerController::class, 'delete'])->name('admin.customer.delete');
    });
    // Customers
    Route::prefix('users')->middleware('auth')->group(function (){
        Route::get('{id?}',[UserController::class, 'index'])->name('admin.user');
        Route::post('store/{id?}',[UserController::class, 'store'])->name('admin.user.store');
        Route::get('delete/{id?}',[UserController::class, 'delete'])->name('admin.user.delete');
    });
    // Setting store
    Route::post('store_theme_setting',[ThemeSettingController::class, 'store'])->name('admin.store_theme_setting');

});